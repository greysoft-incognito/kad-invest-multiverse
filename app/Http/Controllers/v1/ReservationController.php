<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ReservationCollection;
use App\Http\Resources\v1\ReservationResource;
use App\Models\v1\Guest;
use App\Models\v1\Reservation;
use App\Models\v1\Space;
use App\Models\v1\User;
use App\Services\HttpStatus;
use App\Traits\Meta;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReservationController extends Controller
{
    use Meta;

    public function guest(Request $request, Space $space)
    {
        if ($space->available_spots <= 0) {
            $error = __('This space is already booked up');
            return $this->buildResponse([
                'message' => $error,
                'status' => 'error',
                'status_code' => HttpStatus::BAD_REQUEST,
            ]);
        }

        $phone_val = stripos($request->phone, '+') !== false || !config('settings.verify_phone', false)
            ? 'phone:AUTO,NG'
            : 'phone:'.$this->ipInfo('country');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],// 'unique:users'],
            'phone' => config('settings.verify_phone', false) ? "required|$phone_val" : 'nullable|string|max:255|unique:users',
            'company' => 'required|string|max:120',
        ]);

        $name = str($request->name ?? $request->username)->explode(' ');

        $user = Guest::updateOrCreate(
            ['email' => $request->email],
            [
                'firstname' => $name->get(0, $request->name),
                'lastname' => $name->get(1),
                'phone' => $request->phone,
                'company' => $request->company,
            ]
        );

        event(new Registered($user));

        $reservation = $space->reservations()->create([
            'name' => $request->name,
            'user_id' => $user->id,
            'user_type' => 'guest',
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null,
        ]);

        $reference = config('settings.trx_prefix', 'TRX-').$this->generate_string(20, 3);

        $reservation->transactions()->create([
            'user_id' => $user->id,
            'amount' => $space->price,
            'due' => $space->price,
            'reference' => $reference,
            'method' => 'Manual',
            'status' => 'pending',
        ]);

        return (new ReservationResource($reservation))->additional([
            'message' => __(':0 has been booked successfully, we would reach out to you with more information soon.', [$space->name]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    public function store(Request $request, Space $space)
    {
        if ($space->available_spots <= 0) {
            $error = __('This space is already booked up');
            return $this->buildResponse([
                'message' => $error,
                'status' => 'error',
                'status_code' => HttpStatus::BAD_REQUEST,
            ]);
        }

        $user = Auth::user();

        $reservation = $space->reservations()->create([
            'name' => $request->name,
            'user_id' => $user->id,
            'user_type' => 'user',
            'start_date' => $request->start_date ?? null,
            'end_date' => $request->end_date ?? null,
        ]);

        $reference = config('settings.trx_prefix', 'TRX-').$this->generate_string(20, 3);

        $reservation->transactions()->create([
            'user_id' => $user->id,
            'amount' => $space->price,
            'due' => $space->price,
            'reference' => $reference,
            'method' => 'Manual',
            'status' => 'pending',
        ]);

        return (new ReservationResource($reservation))->additional([
            'message' => __(':0 has been booked successfully, we would reach out to you with more information soon.', [$space->name]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }
}
