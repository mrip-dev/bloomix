<?php 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable',
            'subject' => 'nullable',
            'message' => 'nullable',
        ]);

        ContactMessage::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully'
        ], 201);
    }
}
