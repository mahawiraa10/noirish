@extends('layouts.store')

@section('title', 'About')

@section('content')
    {{-- Section About --}}
    <section class="py-24 bg-white">
        {{-- Container gua ubah jadi max-w-4xl biar teksnya gak melebar banget --}}
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            
            <div data-aos="fade-up">
                {{-- Garis Aksen --}}
                <span class="block w-16 h-1 bg-black mb-6"></span>
                
                {{-- Judul --}}
                <h1 class="text-3xl md:text-4xl font-bold text-black mb-8">
                    About NOIRISH
                </h1>
                
                {{-- Konten Teks --}}
                <div class="space-y-6 text-lg text-gray-600 leading-relaxed">
                    <p>
                        NOIRISH was born from a passion to bring elegant, minimalist, and authentic style to every individual. We believe that every piece of clothing has a story — and we want to help you write it with confidence.
                    </p>
                    
                    <p>
                        Built with attention to detail and quality, each of our collections combines comfort with timeless modern designs. We want every customer to have a pleasant, easy, and meaningful shopping experience.
                    </p>
                </div>
            </div>

        </div>
    </section>
@endsection