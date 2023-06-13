<?php

namespace App\Http\Controllers\Api\All\Card;

use App\Http\Controllers\Controller;
use App\Models\SubCat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Card;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $cards = Card::where('user_id', auth()->user()->id)->get();
            foreach ($cards as $card) {
                if ($card->type == 'box') {
                    $box = SubCat::where('id', $card->box_id)->first();
                    $card->box = $box;
                } else {
                    $card->box = 'custom';
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Cards Get successfully',
                'data' => [
                    'cards' => $cards,
                ]
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
        try {
            if ($request->type == 'box') {
                $sCat = SubCat::where('id', $request->box_id)->first();
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
            } else {
                $card = Card::create([
                    'user_id' => auth()->user()->id,
                    'total' => $request->total,
                    'type' => 'custom',
                ]);
                return response()->json([
                    'status' => true,
                    'message' => 'Card Created successfully',
                    'data' => $card
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Card Creation Failed',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ]);
        }
    }
}
