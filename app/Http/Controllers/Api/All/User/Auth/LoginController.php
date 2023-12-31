<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
            'phone' => 'required',
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
                    'message' => 'Password not success'
                ], 401);
            }

            $token = $user->createToken('myToken')->plainTextToken;

            if ($token) {
                $response = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone_number'=> $user->phone_number,
                        'address'=> $user->address,
                        'longitude'=> $user->longitude,
                        'latitude'=> $user->latitude,
                        'city_id'=> $user->city_id,
                        'avatar'=> $user->avatar,
                        'city_name' => $user->city->name,
                        'status' => $user->status,
                        'role' => $user->role,
                        'token' => $token,
                ];
                return response([
                    'status' => true,
                    'message' => 'Login success',
                    'data' => $response,
                ], 201);
            } else {
                return response([
                    'status' => false,
                    'message' => 'Something went wrong',
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
