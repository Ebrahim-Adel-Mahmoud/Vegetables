<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OTP;
use App\Models\City;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RegisterController extends Controller
{
    public function register(Request $request): Response|Application|ResponseFactory
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:users,phone_number',
            'address' => 'required|string',
            'longitude' => 'required|string',
            'latitude' => 'required|string',
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'required|string|confirmed',
            'city_id' => 'required'
        ]);

        $cite = City::find( $request->city_id);

        try {
            $user = User::create([
                'name' => $fields['name'],
                'phone_number' => $fields['phone'],
                'password' => bcrypt($fields['password']),
                'address' => $fields['address'],
                'longitude' => $fields['longitude'],
                'latitude' => $fields['latitude'],
                'city_id' => $fields['city_id'],
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
}
