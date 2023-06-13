<?php

namespace App\Http\Controllers\Api\All\WorkTime;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $work = Work::all();
            foreach ($work as $value) {
                $value->day = Carbon::parse('next ' . $value->day)->format('Y-m-d');
                $value->day_name = Carbon::parse($value->day)->format('l');
            }
            return response()->json([
                'status' => true,
                'message' => 'Data Work',
                'data' => $work
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'day' => 'required|string',
            'start' => 'nullable|date_format:H:i:s',
            'end' => 'nullable|date_format:H:i:s'
        ]);
        try {
            $work = new Work();
            $work->day = $request->day;
            $work->start = $request->start ?? Carbon::now()->format('H:i:s');
            $work->end = $request->end ?? Carbon::now()->format('H:i:s');
            $work->save();
            return response()->json([
                'status' => true,
                'message' => 'Data Work Added',
                'data' => $work
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], 500);
        }
    }
}
