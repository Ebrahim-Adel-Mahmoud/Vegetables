<?php

namespace App\Http\Controllers\Api\All\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RemoveAccountController extends Controller
{
    use SoftDeletes;
    public function removeAccount(): Response|JsonResponse|Application|ResponseFactory
    {
        try {
            $user = auth()->user();
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'Account removed successfully',
                'data' => null
            ]);
        } catch (\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
