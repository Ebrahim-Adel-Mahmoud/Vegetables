<?php

namespace App\Http\Controllers\Api\All\Slider;

use App\Http\Controllers\Controller;
use App\Models\CatSlider;
use App\Models\Slider;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $slider = Slider::get('images');
            return response()->json([
                'status' => true,
                'message' => 'Slider successfully',
                'data' => $slider

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Slider Get Data Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Slider failed',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $images = $request->file('images');
            $imagesName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $images->getClientOriginalExtension();
            $images->move(public_path('images/slider'), $imagesName);
            $slider = Slider::create([
                'images' => asset('images/slider/' . $imagesName),
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Slider successfully',
                'data' => $slider
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Slider Get Data Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
