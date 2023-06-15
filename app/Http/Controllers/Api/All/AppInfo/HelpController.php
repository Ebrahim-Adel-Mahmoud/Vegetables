<?php

namespace App\Http\Controllers\Api\All\AppInfo;

use App\Http\Controllers\Controller;
use App\Models\Help;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $helps = Help::all('question', 'answer');
            return response()->json([
                'status' => true,
                'message' => 'Helps Retrieved',
                'data' => $helps,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to Retrieve Helps',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $help = new Help;
            $help->question = $request->question;
            $help->answer = $request->answer;
            $help->save();
            return response()->json([
                'status' => true,
                'message' => 'Help Created',
                'data' => $help,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to Create Help ',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }
}
