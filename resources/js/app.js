import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/* ================================== */
/* JAVASCRIPT UNTUK SEARCH BAR ANIMASI */
/* ================================== */

// Tunggu sampe semua halaman (DOM) ke-load
document.addEventListener("DOMContentLoaded", () => {
    
    // Cari elemen-elemennya
    const searchContainer = document.querySelector('.search-container');
    const searchBtn = document.querySelector('.search-btn');
    const searchInput = document.querySelector('.search-input');

    // Cek dulu kalo tombolnya ada di halaman ini
    if (searchBtn) {
        
        // Pas tombol search di-klik
        searchBtn.addEventListener('click', (event) => {
            event.preventDefault(); // Biar form gak ke-submit
            
            // Toggle class 'active' di container-nya
            searchContainer.classList.toggle('active');
            
            // Kalo sekarang active, langsung fokus ke field input
            if (searchContainer.classList.contains('active')) {
                searchInput.focus();
            }
        });
    }

    // (Opsional) Kalo di-klik di luar area search, tutup lagi
    document.addEventListener('click', (event) => {
        // Cek dulu kalo container-nya ada, dan
        // yang di-klik BUKAN bagian dari container itu
        if (searchContainer && !searchContainer.contains(event.target)) {
            searchContainer.classList.remove('active');
        }
    });
});
