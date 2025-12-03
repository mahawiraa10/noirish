@extends('layouts.admin')

@section('title', 'Chat with ' . $user->name)

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="bg-slate-100 p-4 border-b flex justify-between items-center">
        <h2 class="font-bold text-lg">Chat with {{ $user->name }}</h2>
        <a href="{{ route('admin.messages.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Back</a>
    </div>

    <div class="p-6 h-96 overflow-y-auto flex flex-col space-y-4" id="admin-chat-container">
        @foreach($messages as $msg)
            <div class="flex {{ $msg->is_admin_reply ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[70%] p-3 rounded-lg shadow-sm {{ $msg->is_admin_reply ? 'bg-slate-700 text-white' : 'bg-gray-100 text-gray-800' }}">
                    <p class="text-sm">{{ $msg->message }}</p>
                    <p class="text-[10px] mt-1 text-right opacity-70">{{ $msg->created_at->format('d M H:i') }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="p-4 border-t bg-gray-50">
        <form action="{{ route('admin.messages.reply', $user->slug) }}" method="POST" class="flex gap-2">
            @csrf
            <input type="text" name="message" class="flex-grow border rounded px-4 py-2" placeholder="Reply..." required>
            <button type="submit" class="bg-slate-700 text-white px-6 py-2 rounded hover:bg-slate-800">Send</button>
        </form>
    </div>
</div>
<script>
    var container = document.getElementById("admin-chat-container");
    container.scrollTop = container.scrollHeight;
</script>
@endsection