<?php

namespace App\Http\Controllers\v1\Portal;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Auth\RegisteredUserController;
use App\Http\Controllers\v1\Guest\FormDataController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\v1\FormDataResource;
use App\Http\Resources\v1\Portal\PortalResource;
use App\Http\Resources\v1\User\UserResource;
use App\Models\v1\GenericFormData;
use App\Models\v1\Guest;
use App\Models\v1\Portal\Portal;
use App\Notifications\FormSubmitedSuccessfully;
use App\Services\HttpStatus;
use DeviceDetector\DeviceDetector;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortalUserController extends Controller
{
    public function register(Request $request, Portal $portal)
    {
        $request->validate([
            'email' => 'required|email|unique:'.$portal->registration_model.',email',
            'password' => 'required|string|confirmed',
        ]);

        $data = (new FormDataController)->store($request, $portal->reg_form_id, true);

        $name = str($data['name'] ?? $data['fullname'] ?? $data['full_name'] ?? $request->email)->explode(' ');

        $userModel = app($portal->registration_model ?? Guest::class);

        $user = $userModel->updateOrCreate(
            ['email' => $request->email],
            [
                'firstname' => $name->get(0, $data['firstname'] ?? $data['first_name'] ?? ''),
                'lastname' => $name->get(1, $data['lastname'] ?? $data['last_name'] ?? ''),
                'phone' => $data['phone'] ?? $data['mobile'] ?? $data['mobile_number'] ?? $data['phone_number'] ?? '',
                'password' => $request->password,
            ]
        );

        event(new Registered($user));

        $key = $portal->reg_form->fields->firstWhere('key', true)->name ?? $portal->reg_form->fields->first()->name;
        $formdata = GenericFormData::create([
            'form_id' => $portal->reg_form_id,
            'user_id' => $user->id ?? null,
            'data' => $data,
            'key' => $data[$key] ?? '',
        ]);

        $formdata->notify(new FormSubmitedSuccessfully());

        $dev = new DeviceDetector($request->userAgent());
        $device = $dev->getBrandName() ? ($dev->getBrandName().$dev->getDeviceName()) : $request->userAgent();

        $token = $user->createToken($device)->plainTextToken;
        (new RegisteredUserController)->setUserData($user);

        return (new RegisteredUserController)->preflight($token, [
            'portal' => new PortalResource($portal),
            'formdata' => new FormDataResource($formdata),
            'message' => __('Your registration for :portal has was completed successfully.', ['portal' => $portal->name]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ], $portal->registration_model === Guest::class ? 'guest' : null, $user);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginRequest $request, Portal $portal)
    {
        try {
            $request->authenticateGuest($portal);

            $dev = new DeviceDetector($request->userAgent());
            $device = $dev->getBrandName() ? ($dev->getBrandName().$dev->getDeviceName()) : $request->userAgent();

            $user = $request->user();

            $token = $user->createToken($device)->plainTextToken;
            (new RegisteredUserController)->setUserData($user);

            return (new UserResource($user))->additional([
                'message' => 'Login was successfull',
                'status' => 'success',
                'status_code' => HttpStatus::OK,
                'token' => $token,
            ])->response()->setStatusCode(HttpStatus::OK);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->buildResponse([
                'portal' => new PortalResource($portal),
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => HttpStatus::UNPROCESSABLE_ENTITY,
                'errors' => [
                    'email' => $e->getMessage(),
                ],
            ]);
        }
    }

    public function show(Request $request, Portal $portal)
    {
        $user = $request->user();
        $user_data = $portal->reg_form->data()->where('user_id', $user->id ?? '--')->first();

        return (new UserResource($user))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'user_data' => $user_data,
            'portal' => $portal,
        ])->response()->setStatusCode(HttpStatus::OK);
    }
}