<footer class="bg-stone-50 text-slate-600 border-t border-stone-200 mt-24 pt-20 pb-12">
    <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-8">
        
        {{-- ====================================== --}}
        {{-- Kolom 1: Brand & Navigasi (Lebar 5/12) --}}
        {{-- ====================================== --}}
        <div class="md:col-span-5 space-y-8">
            <div>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-6">Explore</h3>
                <div class="grid grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    <ul class="space-y-3">
                        <li><a href="{{ route('catalogue.index') }}" class="hover:text-slate-900 transition-colors duration-200">Catalogue</a></li>
                        <li><a href="{{ route('new-arrivals') }}" class="hover:text-slate-900 transition-colors duration-200">New Arrivals</a></li>
                        <li><a href="{{ route('faq.index') }}" class="hover:text-slate-900 transition-colors duration-200">FAQ</a></li>
                    </ul>
                    <ul class="space-y-3">
                        <li><a href="{{ route('about.index') }}" class="hover:text-slate-900 transition-colors duration-200">About Us</a></li>
                        <li><a href="{{ route('order.track.form') }}" class="hover:text-slate-900 transition-colors duration-200">Track Order</a></li>
                        <li><a href="{{ route('contact.index') }}" class="hover:text-slate-900 transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- ====================================== --}}
        {{-- Kolom 2: Contact Info (Lebar 3/12) --}}
        {{-- ====================================== --}}
        <div class="md:col-span-3">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-6">Contact</h3>
            <ul class="space-y-4 text-sm">
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-slate-400 mr-3 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                    <span class="leading-relaxed">{{ $settings['store_address'] ?? 'Address not set' }}</span>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-slate-400 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                    <a href="tel:{{ $settings['contact_phone'] ?? '' }}" class="hover:text-slate-900 transition-colors duration-200">
                        {{ $settings['contact_phone'] ?? 'Phone not set' }}
                    </a>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-slate-400 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                    <a href="mailto:{{ $settings['contact_email'] ?? '' }}" class="hover:text-slate-900 transition-colors duration-200">
                        {{ $settings['contact_email'] ?? 'Email not set' }}
                    </a>
                </li>
            </ul>
        </div>
        
        {{-- ====================================== --}}
        {{-- Kolom 3: Slogan & Social (Lebar 4/12) --}}
        {{-- ====================================== --}}
        <div class="md:col-span-4">
            {{-- Ubah Judul --}}
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-6">Our Philosophy</h3>
            
            {{-- SLOGAN BARU (Ganti form subscribe jadi ini) --}}
            <div class="mb-8">
                <blockquote class="text-lg font-serif italic text-slate-700 leading-relaxed border-l-2 border-slate-300 pl-4">
                    "Elevate your everyday style with pieces that speak confidence. Quality is not just an act, it is a habit."
                </blockquote>
            </div>
            
            {{-- Social Icons --}}
            <div class="flex items-center space-x-6">
                <a href="#" class="text-slate-400 hover:text-slate-900 transition-colors duration-300">
                    <span class="sr-only">Instagram</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 0 1 1.772 1.153 4.902 4.902 0 0 1 1.153 1.772c.247.636.416 1.363.465 2.427.048 1.024.06 1.378.06 3.808v.193c0 2.43-.012 2.784-.06 3.808-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 0 1-1.153 1.772 4.902 4.902 0 0 1-1.772 1.153c-.636.247-1.363.416-2.427.465-1.024.048-1.378.06-3.808.06s-2.784-.012-3.808-.06c-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 0 1-1.772-1.153 4.902 4.902 0 0 1-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.048-1.024-.06-1.378-.06-3.808v-.193c0-2.43.012-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 0 1 1.153-1.772A4.902 4.902 0 0 1 5.47 2.525c.636-.247 1.363-.416 2.427-.465C8.93 2.013 9.284 2 11.685 2h.63Zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.467.398.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 0 0-.748-1.15 3.098 3.098 0 0 0-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058ZM12 6.865a5.135 5.135 0 1 1 0 10.27 5.135 5.135 0 0 1 0-10.27Zm0 1.802a3.333 3.333 0 1 0 0 6.666 3.333 3.333 0 0 0 0-6.666Zm5.338-3.205a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z" clip-rule="evenodd" />
                    </svg>
                </a>
                <a href="#" class="text-slate-400 hover:text-slate-900 transition-colors duration-300">
                    <span class="sr-only">X (Twitter)</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>
    
    {{-- Copyright --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-8 mt-16 py-8 border-t border-stone-200">
        <div class="flex flex-col md:flex-row justify-between items-center text-xs text-slate-400">
            <p>&copy; <script>document.write(new Date().getFullYear());</script> {{ $settings['store_name'] ?? config('app.name', 'Noirish') }}. All rights reserved.</p>
            <div class="mt-4 md:mt-0 flex space-x-6">
                <a href="{{ route('privacy-policy') }}" class="hover:text-slate-600 transition">Privacy Policy</a>
                <a href="{{ route('terms-of-service') }}" class="hover:text-slate-600 transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>