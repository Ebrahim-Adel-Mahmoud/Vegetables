<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LogoutController extends Controller
{
    public function logout(Request $request): Response|Application|ResponseFactory
    {
        try {
            $user = $request->user();
            $user->tokens()->delete();
            $user->update([
                'status' => 'inactive'
            ]);
            return response([
                'success' => true,
                'message' => 'Logged out successfully'
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
