<?php

namespace App\Http\Controllers\Api\All\AppInfo;

use App\Http\Controllers\Controller;
use App\Models\AppInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppInfoController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $apps = AppInfo::all('id', 'description');
            return response()->json([
                'status' => true,
                'message' => 'App Info Retrieved',
                'data' => $apps,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to Retrieve App INfo',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $app = new AppInfo;
            $app->description = $request->description;
            $app->save();

            return response()->json([
                'status' => true,
                'message' => 'App Info Created',
                'data' => $app,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to Create App Info',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }
}
