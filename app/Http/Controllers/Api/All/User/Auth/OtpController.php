<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\OTP;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    public function verifyOTP(Request $request): Response|JsonResponse|Application|ResponseFactory
    {

        $validator = Validator::make($request->all(), [
            'otp_id' => 'required|integer',
            'otp' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $otp = OTP::find($request->otp_id);
            if ($otp->otp == $request->otp) {
                $otp->delete();
                User::where('id', $otp->user_id)->update([
                    'email_verified_at' => now(),
                    'status' => 'active'
                ]);
                return response([
                    'success' => true,
                    'message' => 'OTP verified successfully'
                ], 200);
            } else {
                return response([
                    'success' => false,
                    'message' => 'OTP is not valid'
                ], 400);
            }
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function resendOTP(Request $request): Response|Application|ResponseFactory|JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'otp_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $otp = OTP::find($request->otp_id);
            $otp->otp = rand(1000, 9999);
            $otp->expired_at = now()->addMinutes(5);
            $otp->save();

            return response([
                'success' => true,
                'message' => 'OTP sent successfully'
            ], 200);
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
