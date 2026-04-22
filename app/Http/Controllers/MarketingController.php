<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Subscriber;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterMail;

class MarketingController extends Controller
{
    // Admin: Send newsletter to all subscribers
    public function sendNewsletter(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $subscribers = Subscriber::all();

        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new NewsletterMail($validated['subject'], $validated['content']));
        }

        return response()->json([
            'message' => 'Newsletter sent successfully to ' . $subscribers->count() . ' subscribers.',
        ]);
    }

    // Public: Store contact message
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'message' => $validated['message'],
            'subject' => $request->input('subject', 'New Contact Message'),
        ]);

        return response()->json([
            'message' => 'Message sent successfully',
            'data' => $message
        ], 201);
    }

    // Public: Subscribe to newsletter
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create([
            'email' => $validated['email'],
        ]);

        return response()->json([
            'message' => 'Subscribed successfully',
            'data' => $subscriber
        ], 201);
    }

    // Admin: Get all messages
    public function getMessages()
    {
        return response()->json(Message::latest()->get());
    }

    // Admin: Get all subscribers
    public function getSubscribers()
    {
        return response()->json(Subscriber::latest()->get());
    }
}
