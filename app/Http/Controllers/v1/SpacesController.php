<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SpaceCollection;
use App\Http\Resources\v1\SpaceResource;
use App\Models\v1\Space;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class SpacesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Space::query();
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

        $spaces = $query->paginate($request->get('limit', 30));

        return (new SpaceCollection($spaces))->additional(array_merge([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ], $request->search ? ['total_results' => $query->count()] : []));
    }

    public function show(Request $request, Space $space)
    {
        return (new SpaceResource($space))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}
