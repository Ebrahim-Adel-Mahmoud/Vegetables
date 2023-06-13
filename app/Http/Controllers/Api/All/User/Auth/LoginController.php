<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OTP;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Slider failed',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $user = User::where('phone_number', $request->phone)->first();

            if (!$user) {
                return response([
                    'status' => false,
                    'message' => 'User not found'
                ], 401);
            } else if (!Hash::check($request->password, $user->password)) {
                return response([
                    'status' => false,
                    'message' => 'Password not match'
                ], 401);
            }

            $token = $user->createToken('myToken')->plainTextToken;

            $otp = OTP::create([
                'user_id' => $user->id,
                'otp' => rand(1000, 9999),
                'expired_at' => now()->addMinutes(5)
            ]);

            if ($token) {
                $response = [
                    'user' => $user,
                    'token' => $token,
                    'otp_id' => $otp->id
                ];
                return response($response, 201);
            } else {
                return response([
                    'status' => false,
                    'message' => 'Something went wrong'
                ], 500);
            }

        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
