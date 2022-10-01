<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserResource;
use App\Models\v1\Portal\Portal;
use App\Models\v1\Transaction;
use App\Services\HttpStatus;
use App\Traits\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yabacon\Paystack;
use Yabacon\Paystack\Exception\ApiException;

class PaymentController extends Controller
{
    use Meta;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Initialize a paystack transaction.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => ['required', 'string'],
            'portal_id' => ['required_if:type,portal'],
            'items' => ['required_if:type,cart_checkout', 'array'],
        ]);

        $user = Auth::user();
        $code = HttpStatus::BAD_REQUEST;
        $due = 0;

        try {
            $reference = config('settings.trx_prefix', 'TRX-').$this->generate_string(20, 3);
            if ($request->type === 'portal') {
                $portal = Portal::findOrFail($request->portal_id);
                $transactions = $portal->transactions();

                $due = $request->installment === 1
                    ? $portal->reg_fee
                    : ($request->installment === (1/2)
                        ? $portal->reg_fee / 2
                        : ($request->installment === (1/3)
                            ? $portal->reg_fee / 3
                            : $portal->reg_fee / 4
                        )
                    );

                $transactions->create([
                    'user_id' => Auth::id(),
                    'reference' => $reference,
                    'method' => 'Paystack',
                    'status' => 'pending',
                    'amount' => $due,
                    'due' => $due,
                    'data' => [
                        'installment' => $request->installment,
                        'total' => $portal->reg_fee,
                        'signature' => MD5($portal->reg_fee.$user->email.time()),
                    ],
                ]);
            }

            $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));
            $real_due = round($due * 100, 2);

            // Dont initialize paystack for inline transaction
            if ($request->inline) {
                $tranx = [
                    'data' => ['reference' => $reference],
                ];
                $real_due = $due;
            } else {
                $tranx = $paystack->transaction->initialize([
                    'amount' => $real_due,       // in kobo
                    'email' => $user->email,     // unique to customers
                    'reference' => $reference,   // unique to transactions
                    'callback_url' => $request->get('redirect',
                        config('settings.frontend_link')
                            ? config('settings.frontend_link').'/payment/verify'
                            : config('settings.payment_verify_url', route('payment.paystack.verify'))
                    ),
                ]);
                $real_due = $due;
            }

            $code = 200;

            return $this->buildResponse([
                'message' => $msg ?? HttpStatus::message(HttpStatus::OK),
                'status' => 'success',
                'status_code' => $code ?? HttpStatus::OK, //202
                'payload' => $tranx ?? [],
                'transaction' => $transaction ?? [],
                'amount' => $real_due,
                'refresh' => ['user' => new UserResource($request->user()->refresh())],
            ]);
        } catch (ApiException | \InvalidArgumentException | \ErrorException $e) {
            return $this->buildResponse([
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => $e instanceof ApiException ? HttpStatus::BAD_REQUEST : HttpStatus::SERVER_ERROR,
                'payload' => $e instanceof ApiException ? $e->getResponseObject() : [],
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Verify the paystack payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $action
     * @return \Illuminate\Http\Response
     */
    public function paystackVerify(Request $request)
    {
        $type = 'company';
        $status_info = null;
        $process = [
            'message' => 'Invalid Transaction.',
            'status' => 'error',
            'status_code' => HttpStatus::BAD_REQUEST,
        ];

        if (! $request->reference) {
            $process['message'] = 'No reference supplied';
        }

        try {
            $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));
            $tranx = $paystack->transaction->verify([
                'reference' => $request->reference,   // unique to transactions
            ]);

            $transaction = Transaction::where('reference', $request->reference)->where('status', 'pending')->firstOrFail();
            $transactable = $transaction->transactable;

            if ($transactable instanceof Portal) {
                $process = [
                    'message' => 'Transaction Complete.',
                    'status' => 'success',
                    'status_code' => HttpStatus::ACCEPTED,
                ];
                $type = 'portal';
                $status_info = [
                    'message' => __('Congratulations on your successfull enrolment for :0', [
                        $transactable->name,
                    ]),
                    'info' => __('You can now access the portal and start learning, meanwhile we will reach out to you with more information after we review your entry.'),
                ];
                $transaction->status = 'paid';
                $transaction->save();
            }
        } catch (ApiException | \InvalidArgumentException | \ErrorException $e) {
            $payload = $e instanceof ApiException ? $e->getResponseObject() : [];
            Log::error($e->getMessage(), ['url' => url()->full(), 'request' => $request->all()]);

            return $this->buildResponse([
                'message' => $e->getMessage(),
                'status' => 'error',
                'status_code' => HttpStatus::UNPROCESSABLE_ENTITY,
                'payload' => $payload,
            ]);
        }

        return $this->buildResponse(array_merge($process, [
            'payload' => $tranx ?? [],
            'type' => $type,
            $type => $transactable,
        ]), $status_info ? ['status_info' => $status_info] : null);
    }
}