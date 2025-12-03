<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Tampilkan halaman chat
    public function index()
    {
        $messages = [];
        
        // Kalau user login, ambil history chat-nya
        if (Auth::check()) {
            $messages = ContactMessage::where('user_id', Auth::id())
                                      ->orderBy('created_at', 'asc') // Urutkan dari pesan terlama ke terbaru
                                      ->get();
            
                                
            ContactMessage::where('user_id', Auth::id())
                          ->where('is_admin_reply', true) // Cuma pesan dari admin
                          ->where('is_read', false)
                          ->update(['is_read' => true]);
        }

        return view('contact.index', compact('messages'));
    }

    // Kirim pesan baru
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to send a message.');
        }

        ContactMessage::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_admin_reply' => false, // Ini pesan dari customer
        ]);

        return redirect()->back()->with('success', 'Message sent!');
    }
}