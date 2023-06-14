<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OTP;
use App\Models\City;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:3|max:255',
            'phone_number' => 'required|unique:users',
            'address' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'password' => 'required|string|confirmed',
            'city_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Register Error.',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $cite = City::find($request->city_id);
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'city_id' => $request->city_id,
                'password' => bcrypt($request->password),
            ]);
            if ($request->hasFile('avatar')) {
                $images = $request->file('avatar');
                $imagesName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $images->getClientOriginalExtension();
                $images->move(public_path('images/users'), $imagesName);
                $user->avatar = asset('images/users/' . $imagesName);
            } else {
                $user->avatar = asset('images/state/user.png');
            }
            $user->save();

            $token = $user->createToken('myToken')->plainTextToken;

            $otp = OTP::create([
                'user_id' => $user->id,
                'otp' => rand(1000, 9999),
                'expired_at' => now()->addMinutes(5)
            ]);

            if ($token) {
                $response = [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'phone_number'=> $user->phone_number,
                        'address'=> $user->address,
                        'longitude'=> $user->longitude,
                        'latitude'=> $user->latitude,
                        'city_id'=> $user->city_id,
                        'avatar'=> $user->avatar,
                        'city_name' => $cite->name,
                        'status' => $user->status,
                        'role' => $user->role,
                    ],
                    'token' => $token,
                    'otp_id' => $otp->id
                ];
                return response([
                    'status' => true,
                    'message' => 'User created successfully',
                    'data' => $response
                ], 201);
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

    public function allCity(): Response|Application|ResponseFactory
    {
        $city = City::all();
        return response([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $city
        ], 201);
    }
    public function addCity(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'City Error.',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $city = City::create([
                'name' => $request->name,
            ]);

            if ($city) {
                $response = [
                    'city' => [
                        'id' => $city->id,
                        'name' => $city->name,
                    ],
                ];
                return response([
                    'status' => true,
                    'message' => 'User created successfully',
                    'data' => $response
                ], 201);
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
