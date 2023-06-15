<?php

namespace App\Http\Controllers\Api\All\Contact;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Illuminate\Support\Facades\Validator;

class ContactUsController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $user = auth()->user();
            $contacts = ContactUs::where('user_id', $user->id)->get();
            return response()->json([
                'status' => true,
                'message' => 'All Contact User',
                'contacts' => $contacts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], $e->getCode());
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:255|min:10',
            'email' => 'required|email',
            'phone' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'data' => $validator->errors()
            ], 422);
        }
        try {
            $contact = new ContactUs();
            $contact->user_id = auth()->user()->id;
            $contact->email = $request->input('email');
            $contact->phone = $request->input('phone');
            $contact->message = $request->input('message');
            $contact->save();

            return response()->json([
                'status' => true,
                'message' => 'Message sent successfully',
                'data' => $contact
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => env('API_DEBUG') ? $e->getMessage() : 'Server Error'
            ], $e->getCode());
        }
    }
}

