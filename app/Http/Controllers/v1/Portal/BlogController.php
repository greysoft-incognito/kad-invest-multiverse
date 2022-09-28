<?php

namespace App\Http\Controllers\v1\Portal;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Portal\BlogCollection;
use App\Http\Resources\v1\Portal\BlogResource;
use App\Models\v1\Portal\Portal;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Portal $portal)
    {
        $query = $portal->blogs();

        // Search and filter columns
        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', "%$request->search%");
                $query->orWhereFullText('content', $request->search);
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

        $posts = $query->paginate($request->get('limit', 30))->withQueryString();

        return (new BlogCollection($posts))->additional(array_merge([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ], $request->search ? ['total_results' => $query->count()] : []));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Portal $portal, $id)
    {
        $blog = $portal->blogs()->where('id', $id)->orWhere('slug', $id)->firstOrFail();

        return (new BlogResource($blog))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}
