<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OTP;
use App\Models\City;
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
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|unique:users',
            'address' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|confirmed',
            'city_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'state' => false,
                'message' => 'Register Error.',
                'data' => $validator->errors(),
            ]);
        }

        $cite = City::find( $request->city_id);

        try {
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone,
                'address' => $request->address,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'city_id' => $request->city_id,
                'password' => bcrypt($request->password),
            ]);

            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('images/users/'), $avatarName);
                $user->avatar = asset('images/users/' . $avatarName);
            } else {
                $user->avatar = asset('images/users/user.png');
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
                        'city_name' => $cite->name
                    ],
                    'token' => $token,
                    'otp_id' => $otp->id
                ];
                return response([
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => $response
                ], 201);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Something went wrong'
                ], 500);
            }
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function allCity(): Response|Application|ResponseFactory
    {
        $city = City::all();
        return response([
            'success' => true,
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
                'state' => false,
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
                    'success' => true,
                    'message' => 'User created successfully',
                    'data' => $response
                ], 201);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Something went wrong'
                ], 500);
            }
        } catch (\Exception $e) {
            return response([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
