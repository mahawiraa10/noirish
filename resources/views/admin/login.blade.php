<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Noirish</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-100 flex flex-col items-center justify-center min-h-screen p-4 font-sans text-gray-700">
    <div class="w-full max-w-md"> 
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800">NOIRISH</h1> 
            <p class="text-slate-500">Admin Panel Access</p> 
        </div>
        <div class="bg-white p-8 rounded-xl shadow-lg border border-stone-300"> 
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Login to continue</h2>
            <form action="{{ route('admin.login') }}" method="POST" class="space-y-5"> 
                @csrf
                @if ($errors->any()) <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-md relative text-sm">Email or Password is Wrong!</div> @endif
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-600 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required class="w-full px-4 py-2.5 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500 transition duration-200" value="{{ old('email') }}">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-600 mb-1">Password</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 border border-stone-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-stone-300 focus:border-stone-500 transition duration-200">
                </div>
                <button type="submit" class="w-full bg-slate-700 text-white px-4 py-3 rounded-lg font-semibold hover:bg-slate-800 transition duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">Sign In</button>
            </form>
        </div>
        <p class="text-center text-xs text-slate-400 mt-6">&copy; {{ date('Y') }} Noirish. All rights reserved.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>