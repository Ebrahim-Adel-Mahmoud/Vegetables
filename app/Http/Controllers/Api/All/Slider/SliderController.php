<?php

namespace App\Http\Controllers\Api\All\Slider;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $slider = Slider::all();
            for ($i = 0; $i < count($slider); $i++) {
                $slider[$i]->images = explode("|", $slider[$i]->images);
            }
            return response()->json([
                'status' => true,
                'message' => 'Slider successfully',
                'data' => $slider

            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider Get Data Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'images' => 'required',
        ]);

        try {
            $images = array();
            if ($files = $request->file('images')) {
                foreach ($files as $file) {
                    $image_name = md5(rand(100, 200));
                    $ext = strtolower($file->getClientOriginalExtension());
                    $image_full_name = $image_name . '.' . $ext;
                    $upload_path = 'images/slider/';
                    $image_url = $upload_path . $image_full_name;
                    $file -> move($upload_path, $image_full_name);
                    $images[] = asset($image_url);
                }
            }
            Slider::insert([
                'images' => implode("|",$images),
            ]);
            return response()->json([
                'state' => true,
                'message' => 'Slider successfully',
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Slider Get Data Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
