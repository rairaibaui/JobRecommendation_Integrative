<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ContactSupportController extends Controller
{
    /**
     * Show the contact support form.
     */
    public function show()
    {
        return view('contact-support');
    }

    /**
     * Handle the contact support form submission.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Log the support request
        Log::info('Support Request Received', [
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'user_id' => Auth::check() ? Auth::id() : null,
            'timestamp' => now(),
        ]);

        // In a production app, you would send an email here
        // Example:
        // Mail::to('support@jobmatcher.com')->send(new SupportRequest($request->all()));

        // You can also save to database if you want to track support tickets
        // \App\Models\SupportTicket::create([...]);

        return redirect()->back()->with('success', 'Thank you for contacting us! We have received your message and will respond within 24-48 hours.');
    }
}
