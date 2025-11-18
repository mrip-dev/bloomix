<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageNotification;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        // Save in DB
        ContactMessage::create($validated);

        // Send notification email to admin
        Mail::to('admin@site.com')->send(new ContactMessageNotification($validated));
        Mail::to($validated['email'])->send(new ContactMessageNotification($validated));


        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully'
        ], 201);
    }
}
