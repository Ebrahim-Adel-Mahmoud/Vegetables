<?php

namespace App\Http\Controllers\Api\All\HomeScreen;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CatSlider;
use App\Models\Slider;
use App\Models\ContactUs;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class HomeScreenController extends Controller
{
    public function homeScreen(): JsonResponse
    {
        try {
            $sliderCategories = CatSlider::all('id', 'images');
            $sliderProducts = Slider::all('id', 'images');
            $categories = Category::all('id', 'name', 'desc', 'image');
            $contacts = ContactUs::join('users', 'contact_us.user_id', '=', 'users.id')
                ->select('contact_us.id', 'contact_us.message', 'users.phone_number')
                ->get();
            $phoneNumbers = User::where('role', 'ADM')->select('phone_number')->get()->first();

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
}
