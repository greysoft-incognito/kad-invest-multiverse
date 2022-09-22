<?php

namespace App\Http\Controllers\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\ReservationCollection;
use App\Http\Resources\v1\ReservationResource;
use App\Models\v1\Reservation;
use App\Models\v1\Space;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Space $space)
    {
        $query = $space->reservations();
        // Search and filter columns
        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%$request->search%");
                $query->orWhereFullText('info', $request->search);
                $query->orWherePrice($request->search);
            });
        }

        // Reorder Columns
        if ($request->order && is_array($request->order)) {
            foreach ($request->order as $key => $dir) {
                if ($dir === 'desc') {
                    $query->orderByDesc($key ?? 'id');
                } else {
                    $query->orderBy($key ?? 'id');
                }
            }
        }

        $reservations = $query->paginate($request->get('limit', 30));

        return (new ReservationCollection($reservations))->additional(array_merge([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ], $request->search ? ['total_results' => $query->count()] : []));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request, $status = 'reserved')
    {
        $query = Reservation::query();

        if (in_array($status, ['reserved', 'paid', 'pending', 'cancelled', 'failed'])) {

            $query->whereHas('transactions', function($query) use ($status) {
                if ($status === 'reserved') {
                    $query->where(function($query) {
                        $query->where('status', 'pending');
                        $query->orWhere('status', 'paid');
                    });
                } else {
                    $query->where('status', $status);
                }
            });
        }

        // Search and filter columns
        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('name', 'like', "%$request->search%");
                $query->orWhereFullText('info', $request->search);
                $query->orWherePrice($request->search);
            });
        }

        // Reorder Columns
        if ($request->order && is_array($request->order)) {
            foreach ($request->order as $key => $dir) {
                if ($dir === 'desc') {
                    $query->orderByDesc($key ?? 'id');
                } else {
                    $query->orderBy($key ?? 'id');
                }
            }
        }

        $reservations = $query->paginate($request->get('limit', 30));

        return (new ReservationCollection($reservations))->additional(array_merge([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ], $request->search ? ['total_results' => $query->count()] : []));
    }

    public function show(Request $request, Space $space, $reservation)
    {
        $reservation = $space->reservations()->where('id', $reservation)->firstOrFail();

        return (new ReservationResource($reservation))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    public function status(Request $request, Space $space, $reservation)
    {
        $reservation = $space->all_reservations()->where('id', $reservation)->firstOrFail();

        $this->authorize('can-do', ['spaces.update']);
        $request->validate([
            'status' => 'required|string|in:pending,paid,cancelled,failed',
        ]);

        $user = $reservation->user_type === 'guest' ? $reservation->guest : $reservation->user;
        $transaction = $reservation->transactions()->whereUserId($user->id)->latest()->first();

       if ($transaction) {
            $transaction->update([
                'status' => $request->status,
            ]);
        }

        return (new ReservationResource($reservation))->additional([
            'message' => __('The reservation has been marked as :0!', [$request->status]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {
        $this->authorize('can-do', ['spaces.delete']);
        if ($request->items) {
            $count = collect($request->items)->map(function ($item){
                $item = Reservation::find($item);
                if ($item) {
                    return $item->delete();
                }

                return false;
            })->filter(fn ($i) =>$i !== false)->count();

            return $this->buildResponse([
                'message' => "{$count} reservations have been deleted.",
                'status' =>  'success',
                'status_code' => HttpStatus::OK,
            ]);
        } else {
            $item = Reservation::findOrFail($id);
            if ($item) {
                $item->delete();

                return $this->buildResponse([
                    'message' => "This reservation has been deleted.",
                    'status' =>  'success',
                    'status_code' => HttpStatus::ACCEPTED,
                ]);
            }
        }
    }
}