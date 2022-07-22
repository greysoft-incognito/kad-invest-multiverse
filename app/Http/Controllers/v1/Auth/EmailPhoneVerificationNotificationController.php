<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\Controller;
use App\Services\HttpStatus;
use Illuminate\Http\Request;

class EmailPhoneVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $type = 'email')
    {
        $set_type = ($type == 'phone') ? 'phone number' : 'email address';
        $hasVerified = ($type == 'phone') ? $request->user()->hasVerifiedPhone() : $request->user()->hasVerifiedEmail();

        if ($hasVerified) {
            return $this->buildResponse([
                'message' => "Your $set_type is already verified.",
                'status' => 'success',
                'status_code' => HttpStatus::OK,
            ]);
            // return redirect()->intended(RouteServiceProvider::HOME);
        }

        if ($type === 'email') {
            $request->user()->sendEmailVerificationNotification();
        }

        if ($type === 'phone') {
            $request->user()->sendPhoneVerificationNotification();
        }

        return $this->buildResponse([
            'message' => "Verification code has been sent to your {$set_type}.",
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ]);
    }
}
