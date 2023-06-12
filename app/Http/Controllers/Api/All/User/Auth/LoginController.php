<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OTP;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request): Response|Application|ResponseFactory
    {
        $fields = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        try {
            $user = User::where('phone_number', $fields['phone'])->first();

            if (!$user || !Hash::check($fields['password'], $user->password)) {
                return response([
                    'status' => false,
                    'message' => 'The provided credentials are incorrect.'
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
