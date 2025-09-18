<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    // Show all messages
    public function index()
    {
        $messages = ContactUs::with('user')->latest()->get();
        return response()->json(['success' => true, 'data' => $messages]);
    }

    // Store a new message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $message = ContactUs::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message submitted successfully!',
            'data' => $message,
        ], 201);
    }

    // Show a single message
    public function show($id)
    {
        $message = ContactUs::with('user')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $message]);
    }

    // Delete a message
    public function destroy($id)
    {
        $message = ContactUs::findOrFail($id);
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
        ]);
    }
}
