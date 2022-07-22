<?php

namespace App\Http\Controllers\v1\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FormDataCollection;
use App\Http\Resources\v1\FormDataResource;
use App\Models\v1\Form;
use App\Models\v1\GenericFormData;
use App\Services\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $forms = Form::find($id)->data()->paginate();

        return (new FormDataCollection($forms))->additional([
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
    public function all()
    {
        $forms = GenericFormData::paginate();

        return (new FormDataCollection($forms))->additional([
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
    public function store(Request $request, $form_id)
    {
        $form = Form::whereId($form_id)->orWhere('slug', $form_id)->firstOrFail();

        $errors = collect([]);
        $validation_rules = [];
        $custom_attributes = [];
        foreach ($request->get('data', []) as $key => $value) {
            if ($form->fields->pluck('name')->doesntContain($key)) {
                $errors->push([$key => "$key is not a valid input."]);
            }

            $rules = [];
            if ($form->fields->pluck('name')->contains($key)) {
                $field = $form->fields->firstWhere('name', $key);
                if ($field->type === 'number') {
                    $rules[] = 'numeric';
                } else {
                    $rules[] = 'string';
                }
                if ($field->required) {
                    $rules[] = 'required';
                } else {
                    $rules[] = 'nullable';
                }
                if ($field->type === 'url') {
                    $rules[] = 'url';
                }
                if ($field->type === 'email') {
                    $rules[] = 'email';
                }
                if ($field->options) {
                    $rules[] = 'in:'.collect($field->options)->pluck('value')->implode(',');
                }
                $validation_rules["data.$key"] = $rules;
                $custom_attributes["data.$key"] = $field->label;
            }
        }

        if ($errors->count() > 0) {
            throw ValidationException::withMessages($errors->toArray());
        }

        Validator::make($request->all(), $validation_rules, [], $custom_attributes)->validate();

        $key = $form->fields->firstWhere('key', true)->name ?? $form->fields->first()->name;
        $data = $request->get('data');
        if (!$data) {
            throw ValidationException::withMessages(['data' => 'No data passed']);
        }
        $formdata = GenericFormData::create([
            'form_id' => $form_id,
            'data' => $data,
            'key' => $data[$key] ?? '',
        ]);

        return (new FormDataResource($formdata))->additional([
            'message' => HttpStatus::message(HttpStatus::CREATED),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
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
        $data = GenericFormData::whereId($id)->orWhere('key', $id)->firstOrFail();

        return (new FormDataResource($data))->additional([
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
        \Gate::authorize('usable', 'formdata.update');
        //
    }
}