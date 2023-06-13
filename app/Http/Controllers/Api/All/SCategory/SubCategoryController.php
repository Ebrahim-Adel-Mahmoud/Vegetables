<?php

namespace App\Http\Controllers\Api\All\SCategory;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\SubCat;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function index($id): JsonResponse
    {
        try {
            $sCat = SubCat::where('cat_id', 'like', '%' . $id . '%')->get();
            for ($i = 0; $i < count($sCat); $i++) {
                $sCat[$i]->images = explode("|", $sCat[$i]->images);
            }
            return response()->json([
                'status' => true,
                'message' => 'Sub Category List',
                'data' => $sCat,
            ]);
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
            $sCat = SubCat::find($id);
            for ($i = 0; $i < count($sCat); $i++) {
                $sCat[$i]->images = explode("|", $sCat[$i]->images);
            }
            return response()->json([
                'status' => true,
                'message' => 'Sub Category Details',
                'data' => $sCat,
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
            'cat_id' => 'required',
            'name' => 'required|min:3',
            'mini_desc' => 'required|min:10',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'from' => 'required|numeric',
            'to' => 'required|numeric',
            'images' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Register Error.',
                'data' => $validator->errors(),
            ]);
        }
        try {
            $sCat = new SubCat();
            $sCat->cat_id = $request->cat_id;
            $sCat->name = $request->name;
            $sCat->mini_desc = $request->mini_desc;
            $sCat->description = $request->description;
            $sCat->price = $request->price;
            $from = $request->from;
            $to = $request->to;
            $total = "من" . $from . " الي " . $to;
            $sCat->to = $total;
            $sCat->from = $total;
            $images = array();
            if ($files = $request->file('images')) {
                foreach ($files as $file) {
                    $image_name = md5(rand(100, 200));
                    $ext = strtolower($file->getClientOriginalExtension());
                    $image_full_name = $image_name . '.' . $ext;
                    $upload_path = 'images/subcategory/';
                    $image_url = $upload_path . $image_full_name;
                    $file -> move($upload_path, $image_full_name);
                    $images[] = asset($image_url);
                }
            }
            $sCat->images = implode("|", $images);
            $sCat->save();
            return response()->json([
                'status' => true,
                'message' => 'Sub Category Created',
                'data' => $sCat,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }
}
