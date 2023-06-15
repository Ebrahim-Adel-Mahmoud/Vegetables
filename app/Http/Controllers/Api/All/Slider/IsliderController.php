<?php

namespace App\Http\Controllers\Api\All\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Islider;
use Illuminate\Support\Facades\Validator;

class IsliderController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $sliders = Islider::all();
            return response()->json([
                'status' => true,
                'message' => 'Slider successfully',
                'data' => $sliders,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Slider failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Error, try again',
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Slider failed',
                'data' => $validator->errors(),
            ]);
        }

        try {
            $product = new Islider;
            $product->name = $request->input('name');
            $product->description = $request->input('description');

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/slider/'), $imageName);
            $product->image = asset('/images/slider/') . $imageName;
            $product->save();

            return response()->json([
                'status' => true,
                'message' => 'Slider successfully',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Slider failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Error, try again',
            ]);
        }
    }

}
