@extends('layouts.store')

@section('title', 'Contact Us')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-black p-4 text-white flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold">Customer Support</h1>
                <p class="text-sm text-slate-300">We are here to help you.</p>
            </div>
            <div class="bg-black p-2 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
        </div>

        {{-- KONDISI: SUDAH LOGIN --}}
        @auth
            <div class="p-6 h-96 overflow-y-auto bg-gray-50 flex flex-col space-y-4" id="chat-container">
                @forelse ($messages as $msg)
                    <div class="flex {{ $msg->is_admin_reply ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[75%] p-3 rounded-lg shadow-sm {{ $msg->is_admin_reply ? 'bg-white text-gray-800 border' : 'bg-black text-white' }}">
                            <p class="text-sm">{{ $msg->message }}</p>
                            <p class="text-[10px] mt-1 {{ $msg->is_admin_reply ? 'text-gray-400' : 'text-slate-300' }} text-right">
                                {{ $msg->created_at->format('d M H:i') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p>No messages yet. Start a conversation!</p>
                    </div>
                @endforelse
            </div>

            <div class="p-4 border-t bg-white">
                <form action="{{ route('contact.send') }}" method="POST" class="flex gap-2 items-center">
                    @csrf
                    <input type="text" name="message" class="flex-grow border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:border-black focus:ring-1 focus:ring-slate-500" placeholder="Type your message..." required autocomplete="off">
                    
                    {{-- TOMBOL DIUBAH DI SINI --}}
                    <button type="submit" class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                        Send
                    </button>
                </form>
            </div>

        {{-- KONDISI: BELUM LOGIN (TAMU) --}}
        @else
            <div class="flex flex-col items-center justify-center h-96 bg-gray-50 text-center p-8">
                <div class="bg-white p-4 rounded-full shadow-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Login Required</h2>
                <p class="text-gray-600 mb-6 max-w-md">
                    To ensure the security of your data and track your conversation history, please log in to your account to chat with our support team.
                </p>
                <div class="flex space-x-4">
                    {{-- TOMBOL LOGIN DIUBAH --}}
                    <a href="{{ route('login') }}" class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                        Login
                    </a>
                    
                    {{-- TOMBOL REGISTER DIUBAH --}}
                    <a href="{{ route('register') }}" class="inline-block border border-black text-black px-12 py-3 rounded-full font-bold hover:bg-black hover:text-white transition duration-300 text-xs uppercase tracking-widest">
                        Register
                    </a>
                </div>
            </div>
        @endauth

    </div>
</div>

{{-- Script Auto Scroll (Hanya jalan kalau elemen chat ada) --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatContainer = document.getElementById("chat-container");
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
@endsection