<?php

namespace App\Http\Controllers\v1\Portal;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\Portal\PortalPageCollection;
use App\Http\Resources\v1\Portal\PortalPageResource;
use App\Models\v1\Portal\Portal;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class PortalPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Portal $portal)
    {
        $query = $portal->pages();

        // Search and filter columns
        if ($request->search) {
            $query->where(function ($query) use ($request) {
                $query->where('title', 'like', "%$request->search%");
                $query->where('description', 'like', "%$request->search%");
                $query->where('keywords', 'like', "%$request->search%");
                $query->where('meta', 'like', "%$request->search%");
                $query->orWhereFullText('content', $request->search);
            });
        }

        // Reorder Columns
        if ($request->has('order') && is_array($request->order)) {
            foreach ($request->order as $key => $dir) {
                if ($dir === 'desc') {
                    $query->orderByDesc($key ?? 'id');
                } else {
                    $query->orderBy($key ?? 'id');
                }
            }
        }

        // Reorder Columns
        if ($request->has('filter.only')) {
            $query->whereIn('id', explode(',', $request['filter.only']));
        } elseif ($request->has('filter.except')) {
            $query->whereNotIn('id', explode(',', $request['filter.except']));
        }

        $pages = $query->paginate($request->get('limit', 30))->withQueryString();

        return (new PortalPageCollection($pages))->additional(array_merge([
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
        $page = $portal->pages()->where('id', $id)->orWhere('slug', $id)->firstOrFail();

        return (new PortalPageResource($page))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showIndex(Portal $portal)
    {
        $page = $portal->pages()->whereIndex(true)->firstOrFail();

        return (new PortalPageResource($page))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}
