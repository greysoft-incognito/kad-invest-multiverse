<?php

namespace App\Http\Controllers\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\SpaceCollection;
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
        $this->authorize('can-do', ['configuration']);
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

    public function store(Request $request)
    {
        $this->authorize('can-do', ['configuration']);
        $request->validate([
            'name' => 'required|string|max:255',
            'info' => 'required|string',
            'price' => 'required|numeric',
            'size' => 'sometimes|string',
            'image' => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
            'max_uccupants' => 'required|numeric|min:1',
        ]);

        $space = new Space();
        $space->name = $request->name;
        $space->info = $request->info;
        $space->price = $request->price;
        $space->image = $request->image->store('spaces');
        $space->save();

        return response()->json([
            'message' => HttpStatus::message(HttpStatus::CREATED),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ], HttpStatus::CREATED);
    }
}