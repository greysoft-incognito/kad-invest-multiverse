<?php

namespace App\Http\Controllers\v1\Guest;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\FormDataCollection;
use App\Http\Resources\v1\FormDataResource;
use App\Models\v1\Form;
use App\Models\v1\GenericFormData;
use App\Services\HttpStatus;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FormDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $forms = Form::find($id)->data()->paginate($request->get('limit', 30));

        return (new FormDataCollection($forms))->additional([
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
        $forms = GenericFormData::paginate($request->get('limit', 30));

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

        $custom_messages = $form->fields->filter(fn($f)=>$f->custom_error)->mapWithKeys(function($field, $key) {
            if ($field->required_if) {
                return ["data.$field->name.required_if" => $field->custom_error];
            } elseif ($field->required) {
                return ["data.$field->name.required" => $field->custom_error];
            }
        })->toArray();
        
        $custom_attributes = $form->fields->mapWithKeys(function($field, $key) {
            return ['data.'.$field->name => $field->label];
        })->toArray();

        $validation_rules = $form->fields->mapWithKeys(function($field, $key) {
            if ($field->type === 'number') {
                $rules[] = 'numeric';
            } else {
                $rules[] = 'string';
            }
            if ($field->required_if) {
                $rules[] = 'nullable';
                foreach (explode(',', $field->required_if) as $k => $r) {
                    $rules[] = 'required_if:data.'. str($r)->replace('=',',');
                }
            } elseif ($field->required) {
                $rules[] = 'required';
            } else {
                $rules[] = 'nullable';
            }
            if ($field->type === 'url') {
                $rules[] = 'url';
            }
            if ($field->type !== 'date') {
                if ($field->min) {
                    $rules[] = "min:$field->min";
                }
                if ($field->max) {
                    $rules[] = "min:$field->max";
                }
            }
            
            if ($field->type === 'email') {
                $rules[] = 'email';
            }
            if ($field->options) {
                $rules[] = 'in:'.collect($field->options)->pluck('value')->implode(',');
            }
            return ['data.'.$field->name => $rules];
        })->toArray();
        
        foreach ($request->get('data', []) as $key => $value) {
            if ($form->fields->pluck('name')->doesntContain($key)) {
                $errors->push([$key => "$key is not a valid input."]);
            }
            if ($form->fields->pluck('name')->contains($key)) {
                
                $field = $form->fields->firstWhere('name', $key);

                if ($field->required && $field->type === 'date' && str($key)->contains(['dob', 'age', 'date_of_birth', 'birth_date'])) {
                    $parseDate = \DateTime::createFromFormat('D M d Y H:i:s e+', $value);
                    $date = ($parseDate !== false) ? CarbonImmutable::parse($parseDate) : new Carbon($value);
                    
                    if ($field->min && $date->diffInYears(Carbon::now()) < $field->min) {
                        $errors->push([$key => __("The minimum age requirement for this application is :0.", [$field->max])]);
                    }
                    
                    if ($field->max && $date->diffInYears(Carbon::now()) > $field->max) {
                        $errors->push([$key => __("The age limit for this application is :0.", [$field->max])]);
                    }
                }
            }
        }

        if ($errors->count() > 0) {
            throw ValidationException::withMessages($errors->toArray());
        }

        Validator::make($request->all(), $validation_rules, $custom_messages, $custom_attributes)->validate();

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