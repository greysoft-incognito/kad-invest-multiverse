<?php

namespace App\Http\Controllers\v1\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FormCollection;
use App\Http\Resources\v1\FormResource;
use App\Models\v1\Form;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $forms = Form::paginate($request->get('limit', 30));

        return (new FormCollection($forms))->additional([
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
        $form = Form::whereId($id)->orWhere('slug', $id)->firstOrFail();

        return (new FormResource($form))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}