@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-xl font-bold mb-4">Customer Messages</h2>
    <div class="grid gap-4">
        @foreach($users as $customer)
        <a href="{{ route('admin.messages.show', $customer->slug) }}" class="block border rounded p-4 hover:bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg">{{ $customer->name }}</h3>
                <p class="text-sm text-gray-500">{{ $customer->email }}</p>
            </div>
            <span class="bg-slate-200 text-slate-800 px-3 py-1 rounded-full text-xs font-bold">
                {{ $customer->contact_messages_count }} msgs
            </span>
        </a>
        @endforeach
    </div>
</div>
@endsection