<?php

namespace App\Http\Controllers\Api\All\User\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UpdateController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',

        ]);

        $user = auth()->user();
        $user->update($request->all());

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/users/'), $avatarName);
            $user->avatar = asset('images/users/' . $avatarName);
        }
        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }

    public function updatePassword(Request $request): Response|JsonResponse|Application|ResponseFactory
    {
        $request->validate([
            'old_password' => 'required|string|max:255',
            'password' => 'required|string|max:255|confirmed',
        ]);

        try {
            $user = auth()->user();
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Old password is incorrect',
                    'data' => null
                ], 400);
            }
            $user->update([
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Password updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e){
            return response([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
