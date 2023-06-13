<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\OTP;
use Illuminate\Support\Facades\Validator;

class ForgetPassController extends Controller
{
    public function forgetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'require',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ]);
        }
        try {
            $user = User::Where('phone_number', $request->phone_number)->first();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found'
                ], 404);
            } else {
                $otp = OTP::create([
                    'user_id' => $user->id,
                    'otp' => rand(1000, 9999),
                    'expired_at' => now()->addMinutes(5)
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'OTP has been sent to your phone number',
                    'otp_id' => $otp->id
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed',
            'otp_id' => 'required|integer',
            'otp' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'data' => $validator->errors(),
            ]);
        }
        try {
            $otp = OTP::find($request->otp_id);
            if ($otp->otp == $request->otp) {
                $otp->delete();
                User::where('id', $otp->user_id)->update([
                    'password' => bcrypt($request->password)
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Password successfully changed'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'OTP is not valid'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
