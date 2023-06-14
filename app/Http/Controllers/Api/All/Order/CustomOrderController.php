<?php

namespace App\Http\Controllers\Api\All\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Corder;

class CustomOrderController extends Controller
{
    public $selected_product = [];

    public function index(): JsonResponse
    {
        try {
            $order = Corder::where('user_id', 'like', '%' . auth()->user()->id . '%')->get();
            foreach ($order as $item) {
                $this->selected_product = explode(',', $item->product_id);
                $item->product = Product::whereIn('id', $this->selected_product)->get();
            }
            return response()->json([
                'status' => true,
                'message' => 'Order List',
                'data' => $order
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
            $order = Corder::where('id', $id)->first();
            if (!$order) return response()->json([
                'status' => false,
                'message' => 'Order Not Found',
                'data' => null
            ]);
            $this->selected_product = explode(',', $order->product_id);
            $order->product = Product::whereIn('id', $this->selected_product)->get();
            return response()->json([
                'status' => true,
                'message' => 'Order Get Successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Order Get Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
        ]);

        try {
            $order = new Corder();
            $order->user_id = auth()->user()->id;
            $order->total_price = $request->total_price;
            $order->quantity = $request->quantity;
            $order->product_id = implode(',', $request->product_id);
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Order Placed Successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'product_id' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
        ]);
        try {
            $order = Corder::where('id', $id)->first();
            if (!$order) return response()->json([
                'status' => false,
                'message' => 'Order Not Found',
                'data' => null
            ]);
            $order->user_id = auth()->user()->id;
            $order->total_price = $request->total_price;
            $order->quantity = $request->quantity;
            $order->product_id = implode(',', $request->product_id);
            $order->save();
            return response()->json([
                'status' => true,
                'message' => 'Order Updated Successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Order Update Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $order = Corder::where('id', $id)->first();
            if (!$order) return response()->json([
                'status' => false,
                'message' => 'Order Not Found',
                'data' => null
            ]);
            $order->delete();
            return response()->json([
                'status' => true,
                'message' => 'Order Deleted Successfully',
                'data' => $order,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Order Delete Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error',
            ]);
        }
    }

}
