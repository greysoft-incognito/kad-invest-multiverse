<?php

namespace App\Http\Controllers\v1\Manage;

use App\Http\Controllers\Controller;
use App\Http\Controllers\v1\Guest\FormDataController as GuestFormDataController;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Form $form)
    {
        \Gate::authorize('usable', 'formdata.list');
        $forms = $form->data()->paginate($request->get('limit', 30))->withQueryString();

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
        \Gate::authorize('usable', 'formdata.list');
        $forms = GenericFormData::paginate($request->get('limit', 30))->withQueryString();

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
        \Gate::authorize('usable', 'formdata.create');
        return (new GuestFormDataController)->store($request, $form_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($form_id, $id)
    {
        \Gate::authorize('usable', 'formdata.show');
        $data = GenericFormData::whereId($id)->orWhere('key', $id)->firstOrFail();

        return (new FormDataResource($data))->additional([
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
    public function decodeQr(Request $request)
    {
        \Gate::authorize('usable', 'formdata.show');
        // Use regex to extract the form_id and data_id parts of the following string 'grey:multiverse:form=1:data=23'
        $this->validate($request, [
            'qr' => 'required|regex:/^grey:multiverse:form=(\d+):data=(\d+)$/',
        ], [
            'qr.regex' => 'The QR code is invalid.',
        ]);

        // Decode a regex string into an array
        preg_match('/^grey:multiverse:form=(\d+):data=(\d+)$/', $request->qr, $matches);
        $form_id = $matches[1];
        $form_data_id = $matches[2];
        $qr_code = $matches[0];

        $data = GenericFormData::whereId($form_data_id)->firstOrFail();
        // save this scan to the history
        $data->scans()->create([
            'user_id' => auth()->user()->id,
            'qrcode' => $qr_code,
            'form_id' => $form_id,
        ]);

        $data->scan_date = $data->scans()->latest()->first()->created_at;
        $data->save();

        return (new FormDataResource($data))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }

    /**
     * Display the stats for the resources resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stats(Request $request, Form $form)
    {
        \Gate::authorize('usable', 'formdata.stats');

        if ($request->data) {
          	$request_data = str($request->data)->explode(',');
            $data = $request_data->mapWithKeys(function($value) use ($form, $request_data) {
                $stat = str($value)->explode(':');

                $key = is_numeric($stat[1]??$stat[0]) || is_bool($stat[1]??$stat[0])
                    ? $stat[0]
                    : $stat[1]??$stat[0];

                $values = str($stat[1]??'')->explode('.');

                if (str($stat[1]??'')->contains('.')) {
                    $stat[2] = $values->get(1);
                    $stat[3] = $values->get(2);
                }

                $stat[1] = $values->get(0);

                $query = $form->data();
                if ( isset($stat[3]) ) {
                    $query->whereJsonContains("data->{$stat[0]}", [$stat[1], $stat[2], $stat[3]]);
                } elseif ( isset($stat[2]) ) {
                    $query->whereJsonContains("data->{$stat[0]}", [$stat[1], $stat[2]]);
                } elseif ( $stat[1] ) {

                    $field = $form->fields()->where('name', $stat[0])->first();

                    if ($field && $field->type === 'multiple') {
                        $query->whereJsonContains("data->{$stat[0]}", $stat[1]);
                    } else {
                        $query->whereJsonContains("data->{$stat[0]}", $stat[1]);
                        $query->whereJsonDoesntContain("data->{$stat[0]}", [$stat[1]]);
                    }

                  	$others = $request_data->filter(fn($rd)=> $rd !== "{$stat[0]}:{$stat[1]}")->toArray();
                    foreach($others as $other) {
                      $query->whereJsonDoesntContain("data->{$stat[0]}", str_ireplace("{$stat[0]}:", '', $other));
                    }
                }

                return [$key => $query->count()];
            })->merge(['total' => $form->data()->count()]);
        } else {
            $data = ['total' => $form->data()->count()];
        }
        $data['form'] = $form;

        return $this->buildResponse([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'data' => $data,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Gate::authorize('usable', 'formdata.delete');
        //
    }
}