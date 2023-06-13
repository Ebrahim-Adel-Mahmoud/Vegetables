<?php

namespace App\Http\Controllers\Api\All\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index($id): JsonResponse
    {
        try {
            $product = Product::where('subcategory_id', 'like', '%' . $id . '%')->get();
            return response()->json([
                'status' => true,
                'message' => 'Product List',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function getAll(): JsonResponse
    {
        try {
            $product = Product::all();
            return response()->json([
                'status' => true,
                'message' => 'Product List',
                'data' => $product,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'subcategory_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors(),
            ]);
        }
        try {
            $product = new Product;
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->subcategory_id = $request->subcategory_id;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/product/'), $imageName);
                $product->image = asset('images/product/' . $imageName);
            } else {
                $product->image = asset('images/state/product.png');
            }
            $product->save();
            return response()->json([
                'status' => true,
                'message' => 'Product created',
                'data' => $product
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $product = Product::find($id);
            if ($product) {
                return response()->json([
                    'status' => true,
                    'message' => 'Product Details',
                    'data' => $product,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                    'data' => null,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }
}
