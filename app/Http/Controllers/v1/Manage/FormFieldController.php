<?php

namespace App\Http\Controllers\v1\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FormFieldCollection;
use App\Http\Resources\v1\FormFieldResource;
use App\Models\v1\Form;
use App\Models\v1\GenericFormField;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class FormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        \Gate::authorize('usable', 'formfield.list');
        $fields = GenericFormField::paginate($request->get('limit', 30));

        return (new FormFieldCollection($fields))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function form(Request $request, $id)
    {
        \Gate::authorize('usable', 'formfield.list');
        $fields = Form::find($id)->fields()->paginate($request->get('limit', 30));

        return (new FormFieldCollection($fields))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        \Gate::authorize('usable', 'formfield.create');
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        \Gate::authorize('usable', 'formfield.show');
        $field = GenericFormField::find($id);

        return (new FormFieldResource($field))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        \Gate::authorize('usable', 'formfield.update');
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Gate::authorize('usable', 'formfield.delete');
        //
    }
}