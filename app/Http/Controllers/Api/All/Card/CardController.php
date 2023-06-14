<?php

namespace App\Http\Controllers\Api\All\Card;

use App\Http\Controllers\Controller;
use App\Models\Corder;
use App\Models\Product;
use App\Models\SubCat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Card;

class CardController extends Controller
{
    public $selected_product = [];

    public function index(): JsonResponse
    {
        try {
            $cards = Card::where('user_id', auth()->user()->id)->get();
            $order = Corder::where('user_id', auth()->user()->id)->get();
            foreach ($order as $item) {
                $this->selected_product = explode(',', $item->product_id);
                $item->product = Product::whereIn('id', $this->selected_product)->get();
                $cards->push($item);
            }
            foreach ($cards as $card) {
                $box = SubCat::where('id', $card->box_id)->first();
                $card->box = $box;
            }
            return response()->json([
                'status' => true,
                'message' => 'Cards Get successfully',
                'data' => $cards
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cards Get Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ]);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'box_id' => 'required',
            'quantity' => 'required',
        ]);
        try {
            $sCat = SubCat::where('id', $request->box_id)->first();
            if (!$sCat) return response()->json([
                'status' => false,
                'message' => 'Box Not Found',
                'data' => null
            ]);
            $total = $sCat->price * $request->quantity;
            $card = Card::create([
                'user_id' => auth()->user()->id,
                'box_id' => $request->box_id,
                'quantity' => $request->quantity,
                'total' => $total,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Card Created successfully',
                'data' => [
                    'card' => $card,
                    'box' => $sCat,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Card Creation Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ]);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $card = Card::where('id', $id)->first();
            if (!$card) return response()->json([
                'status' => false,
                'message' => 'Card Not Found',
                'data' => null
            ]);
            $sCat = SubCat::where('id', $request->box_id)->first();
            $total = $sCat->price * $request->quantity;
            $card->update([
                'box_id' => $request->box_id,
                'quantity' => $request->quantity,
                'total' => $total,
            ]);
            return response()->json([
                'status' => true,
                'message' => 'Card Updated successfully',
                'data' => [
                    'card' => $card,
                    'box' => $sCat,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Card Update Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ]);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $card = Card::where('id', $id)->first();
            $card->delete();
            return response()->json([
                'status' => true,
                'message' => 'Card Deleted successfully',
                'data' => $card
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Card Delete Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ]);
        }
    }
}
