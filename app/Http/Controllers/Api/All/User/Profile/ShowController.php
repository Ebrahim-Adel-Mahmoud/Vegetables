<?php

namespace App\Http\Controllers\Api\All\User\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            return response()->json([
                'status' => true,
                'message' => 'User Profile Get Successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'User Profile Get Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

}
