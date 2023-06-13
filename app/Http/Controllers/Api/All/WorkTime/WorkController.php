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
            foreach ($work as $key => $value) {
                $work[$key]['day'] = Carbon::parse($value['day'])->format('l');
                $work[$key]['date'] = Carbon::parse($value['day'])->format('d');
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
