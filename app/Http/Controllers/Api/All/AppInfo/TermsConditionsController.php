<?php

namespace App\Http\Controllers\Api\All\AppInfo;

use App\Http\Controllers\Controller;
use App\Models\TermsAndConditions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TermsConditionsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $terms = TermsAndConditions::all('id','description');
            return response()->json([
                'status' => true,
                'message' => 'Terms Conditions Retrieved',
                'data'  => $terms,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to Retrieve Descriptions',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $terms = new TermsAndConditions;
            $terms->description = $request->description;
            $terms->save();

            return response()->json([
                'status' => true,
                'message' => 'Terms Conditions Created',
                'data'=> $terms,

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to CreateTerms Conditions',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }
}
