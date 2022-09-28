<?php

namespace App\Http\Controllers\v1\Manage;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FormFieldCollection;
use App\Http\Resources\v1\FormFieldResource;
use App\Models\v1\Form;
use App\Models\v1\GenericFormField;
use App\Services\HttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class FormFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Form $form)
    {
        \Gate::authorize('usable', 'formfield.list');
        $fields = $form->fields()->paginate($request->get('limit', 30));

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
    public function all(Request $request)
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Form $form)
    {
        \Gate::authorize('usable', 'formfield.create');
        $request->validate([
            'name' => 'required|string',
            'label' => 'required|string',
            'value' => 'nullable|string',
            'hint' => 'nullable|string|min:3',
            'custom_error' => 'nullable|string|min:3',
            'compare' => 'nullable|date',
            'options' => 'required_if:element,select|nullable|array',
            'required' => 'nullable|boolean',
            'required_if' => 'nullable|string',
            'restricted' => 'nullable|boolean',
            'key' => 'nullable|string',
            'min' => ['numeric', Rule::requiredIf($request->compare && $request->type === 'date' && ! $request->max)],
            'max' => ['numeric', Rule::requiredIf($request->compare && $request->type === 'date' && ! $request->min)],
            'element' => 'required|string|in:input,textarea,select',
            'type' => 'required|string|in:hidden,text,number,email,password,date,time,datetime-local,file,tel,url,checkbox,radio',
        ], [
            'min.required' => 'the Min field is required if Compare is set and Type equals date while Max is missing',
            'max.required' => 'the Max field is required if Compare is set and Type equals date while Min is missing',
        ]);

        $field = new GenericFormField;
        $field->form_id = $form->id;
        $field->name = $request->name;
        $field->field_id = $request->name;
        $field->label = $request->label;
        $field->value = $request->value;
        $field->hint = $request->hint;
        $field->custom_error = $request->custom_error;
        $field->compare = $request->compare;
        $field->options = $request->options;
        $field->required = $request->required;
        $field->required_if = $request->required_if;
        $field->restricted = $request->restricted;
        $field->key = $request->key;
        $field->min = $request->min;
        $field->max = $request->max;
        $field->element = $request->element;
        $field->type = $request->type;
        $field->save();

        return (new FormFieldResource($field))->additional([
            'message' => __('Your form field has been created successfully.'),
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
    public function show(Form $form, $id)
    {
        \Gate::authorize('usable', 'formfield.show');
        $field = $form->fields()->findOrFail($id);

        return (new FormFieldResource($field))->additional([
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
    public function multiple(Request $request, Form $form)
    {
        $validator = Validator::make($request->all(), [
            'data.*.name' => 'required|string',
            'data.*.label' => 'required|string',
            'data.*.value' => 'nullable|string',
            'data.*.hint' => 'nullable|string|min:3',
            'data.*.custom_error' => 'nullable|string|min:3',
            'data.*.compare' => 'nullable|date',
            'data.*.options' => 'required_if:element,select|nullable|array',
            'data.*.required' => 'nullable|boolean',
            'data.*.required_if' => 'nullable|string',
            'data.*.restricted' => 'nullable|boolean',
            'data.*.key' => 'required|string',
            'data.*.min' => 'numeric',
            'data.*.max' => 'numeric',
            'data.*.element' => 'required|string|in:input,textarea,select',
            'data.*.type' => 'required|string|in:hidden,text,number,email,password,date,time,datetime-local,file,tel,url,checkbox,radio',
        ], [
            'data.*.min.required' => '[FIELD #:index] The Min field is required if Compare is set and Type equals date while Max is missing',
            'data.*.max.required' => '[FIELD #:index] The Max field is required if Compare is set and Type equals date while Min is missing',
        ], [
            'data.*.name' => '#:index Name',
            'data.*.label' => '#:index Label',
            'data.*.value' => '#:index Value',
            'data.*.hint' => '#:index Hint',
            'data.*.custom_error' => '#:index Custom Error',
            'data.*.compare' => '#:index Compare',
            'data.*.options' => '#:index Options',
            'data.*.required' => '#:index Required',
            'data.*.required_if' => '#:index Required If',
            'data.*.restricted' => '#:index Restricted',
            'data.*.key' => '#:index Key',
            'data.*.min' => '#:index Min',
            'data.*.max' => '#:index Max',
            'data.*.element' => '#:index Element',
            'data.*.type' => '#:index Type',
        ]);

        $validator->sometimes(['data.*.min', 'data.*.max'], 'required', function ($input, $item) {
            return $item->compare && $item->type === 'date' && ! $item->min;
        });

        $validator->validate();

        $fields = collect($request->data)->map(function ($data) use ($form) {
            $field = $form->fields()->where('id', $data['id'] ?? null)->firstOrNew();
            $field->name = $data['name'] ?? null;
            $field->field_id = $data['name'] ?? null;
            $field->label = $data['label'] ?? null;
            $field->value = $data['value'] ?? null;
            $field->hint = $data['hint'] ?? null;
            $field->custom_error = $data['custom_error'] ?? null;
            $field->compare = $data['compare'] ?? null;
            $field->options = $data['options'] ?? null;
            $field->required = $data['required'] ?? null;
            $field->required_if = $data['required_if'] ?? null;
            $field->restricted = $data['restricted'] ?? null;
            $field->key = $data['key'] ?? null;
            $field->min = $data['min'] ?? null;
            $field->max = $data['max'] ?? null;
            $field->element = $data['element'] ?? null;
            $field->type = $data['type'] ?? null;
            $field->save();
            $field->updated = (bool) ($data['id'] ?? null);

            return $field;
        });

        $count_id = $fields->filter(fn ($f) => $f['updated'])->count();
        $count_no_id = $fields->filter(fn ($f) => ! $f['updated'])->count();

        return (new FormFieldCollection($fields))->additional([
            'message' => __(':0 field(s) updated, :1 new field(s) created.', [$count_id, $count_no_id]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Form $form, $id)
    {
        \Gate::authorize('usable', 'formfield.update');
        $field = $form->fields()->findOrFail($id);

        $request->validate([
            'name' => 'required|string',
            'label' => 'required|string',
            'value' => 'nullable|string',
            'hint' => 'nullable|string|min:3',
            'custom_error' => 'nullable|string|min:3',
            'compare' => 'nullable|date',
            'options' => 'required_if:element,select|nullable|array',
            'required' => 'nullable|boolean',
            'required_if' => 'nullable|string',
            'restricted' => 'nullable|boolean',
            'key' => 'required|string',
            'min' => ['numeric', Rule::requiredIf($request->compare && $request->type === 'date' && ! $request->max)],
            'max' => ['numeric', Rule::requiredIf($request->compare && $request->type === 'date' && ! $request->min)],
            'element' => 'required|string|in:input,textarea,select',
            'type' => 'required|string|in:hidden,text,number,email,password,date,time,datetime-local,file,tel,url,checkbox,radio',
        ], [
            'min.required' => 'the Min field is required if Compare is set and Type equals date while Max is missing',
            'max.required' => 'the Max field is required if Compare is set and Type equals date while Min is missing',
        ]);

        $field->name = $request->name;
        $field->field_id = $request->name;
        $field->label = $request->label;
        $field->value = $request->value;
        $field->hint = $request->hint;
        $field->custom_error = $request->custom_error;
        $field->compare = $request->compare;
        $field->options = $request->options;
        $field->required = $request->required;
        $field->required_if = $request->required_if;
        $field->restricted = $request->restricted;
        $field->key = $request->key;
        $field->min = $request->min;
        $field->max = $request->max;
        $field->element = $request->element;
        $field->type = $request->type;
        $field->save();

        return (new FormFieldResource($field))->additional([
            'message' => __("{$field->label} has been updated successfully."),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Form $form, $id)
    {
        \Gate::authorize('usable', 'formfield.delete');
        if ($request->items) {
            $count = collect($request->items)->map(function ($item) use ($form) {
                $field = $form->fields()->find($item);
                if ($field) {
                    return $field->delete();
                }

                return false;
            })->filter(fn ($i) => $i !== false)->count();

            return $this->buildResponse([
                'message' => "{$count} form feilds have been deleted.",
                'status' => 'success',
                'status_code' => HttpStatus::OK,
            ]);
        } else {
            $field = $form->fields()->find($id);
        }

        if ($field) {
            $field->delete();

            return $this->buildResponse([
                'message' => "{$field->label} has been deleted.",
                'status' => 'success',
                'status_code' => HttpStatus::ACCEPTED,
            ]);
        }

        return $this->buildResponse([
            'message' => 'The requested field no longer exists.',
            'status' => 'error',
            'status_code' => HttpStatus::NOT_FOUND,
        ]);
    }
}
