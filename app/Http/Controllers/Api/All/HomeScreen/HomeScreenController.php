<?php

namespace App\Http\Controllers\Api\All\HomeScreen;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CatSlider;
use App\Models\Slider;
use App\Models\ContactUs;
use App\Models\User;
use App\Models\SubCat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeScreenController extends Controller
{
    public function homeScreen(): JsonResponse
    {
        try {
            $sliderCategories = CatSlider::all('id', 'images');
            $sliderProducts = Slider::all('id', 'images');
            $categories = Category::all('id', 'name', 'desc', 'image');
            $phoneNumbers = User::where('role', 'ADM')->select('phone_number')->get();
            $contacts = ContactUs::where('user_id', auth()->user()->id)->get(['id', 'email', 'phone', 'message']);

            $finalResult = [
                'SliderCategories' => $sliderCategories->toArray(),
                'SliderProducts' => $sliderProducts->toArray(),
                'Categories' => $categories->toArray(),
                'Contact' => $contacts->toArray(),
                'Administrator' => $phoneNumbers->toArray(),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Data retrieved successfully',
                'data' => $finalResult
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], $e->getCode());
        }
    }

    public function search($name): JsonResponse
    {
        try {
            $subcategories = SubCat::where('name', 'LIKE', "%{$name}%")->get();
            for ($i = 0; $i < count($subcategories); $i++) {
                $subcategories[$i]->images = explode("|", $subcategories[$i]->images);
            }
            return response()->json([
                'subcategories' => $subcategories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ], $e->getCode());
        }
    }
}
