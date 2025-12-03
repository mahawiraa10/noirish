<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // Halaman List User yang kirim pesan
    public function index()
    {
        // Ambil user yang pernah kirim pesan
        $users = User::whereHas('contactMessages')->withCount('contactMessages')->get();
        return view('admin.messages.index', compact('users'));
    }

    // Halaman Chat Detail dengan User tertentu
    public function show(User $user)
    {
        $messages = ContactMessage::where('user_id', $user->id)
                                  ->orderBy('created_at', 'asc')
                                  ->get();

        ContactMessage::where('user_id', $user->id)
                      ->where('is_admin_reply', false) // Cuma pesan dari user
                      ->where('is_read', false)
                      ->update(['is_read' => true]);
        
        return view('admin.messages.show', compact('user', 'messages'));
    }

    // Admin Membalas Pesan
    public function reply(Request $request, User $user)
    {
        $request->validate(['message' => 'required']);

        ContactMessage::create([
            'user_id' => $user->id,
            'message' => $request->message,
            'is_admin_reply' => true, // Tandai sebagai balasan admin
        ]);

        return redirect()->back();
    }
}