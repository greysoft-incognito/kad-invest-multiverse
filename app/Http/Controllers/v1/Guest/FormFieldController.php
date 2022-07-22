<?php

namespace App\Http\Controllers\v1\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FormFieldCollection;
use App\Http\Resources\v1\FormFieldResource;
use App\Models\v1\Form;
use App\Models\v1\GenericFormField;
use App\Services\HttpStatus;

class FormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fields = GenericFormField::paginate();

        return (new FormFieldCollection($fields))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function form($id)
    {
        $fields = Form::find($id)->fields()->paginate();

        return (new FormFieldCollection($fields))->additional([
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
    public function show($id)
    {
        $field = GenericFormField::find($id);

        return (new FormFieldResource($field))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}
