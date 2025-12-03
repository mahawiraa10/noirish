@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="bg-white shadow rounded-lg p-4">
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-xl font-bold">Categories</h2>
        <button id="addBtn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">+ Add Category</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded border">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                    <th class="p-3 w-10">#</th>
                    <th class="p-3">Name</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="category-table-body">
               <tr><td colspan="3" class="text-center p-4">Loading categories...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="category-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        
        <form id="category-form">
            <div class="flex justify-between items-center border-b p-4">
                <h3 id="modal-title" class="text-xl font-semibold">Add Category</h3>
                <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-4">
                <input type="hidden" id="category_id" name="category_id"> <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                <input type="text" id="name" name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
            </div>

            <div class="border-t p-4 flex justify-end space-x-2">
                <button type="button" id="cancel-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold">Cancel</button>
                <button type="submit" id="save-btn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">Save</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tableBody = document.getElementById("category-table-body");
        const addBtn = document.getElementById("addBtn");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Elemen Modal
        const modal = document.getElementById("category-modal");
        const closeModalBtn = document.getElementById("close-modal-btn");
        const cancelBtn = document.getElementById("cancel-btn");
        const categoryForm = document.getElementById("category-form");
        const modalTitle = document.getElementById("modal-title");
        const categoryIdInput = document.getElementById("category_id");
        const categoryNameInput = document.getElementById("name");

        // ========================================
        // (1) FUNGSI UNTUK NUTUP MODAL
        // ========================================
        const hideModal = () => modal.classList.add('hidden');
        closeModalBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        // ========================================
        // (2) LOAD CATEGORIES (Fungsi ini gak berubah banyak)
        // ========================================
        async function loadCategories() {
            try {
                const response = await fetch("{{ route('admin.data.categories.index') }}");
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                const categories = await response.json();
                
                if (!Array.isArray(categories) || categories.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="3" class="text-center p-4 text-gray-500">No categories found.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = categories.map((c, i) => `
                    <tr class="border-t hover:bg-gray-50 transition">
                        <td class="p-3 font-semibold text-gray-700">${i + 1}</td>
                        <td class="p-3 font-medium text-gray-800">${c.name}</td>
                        <td class="p-3 text-right">
                            <button onclick='showCategoryModal(${JSON.stringify(c)})' class="text-blue-600 hover:underline font-semibold">Edit</button>
                            <button onclick="deleteCategory(${c.id})" class="text-red-600 hover:underline ml-3 font-semibold">Delete</button>
                        </td>
                    </tr>
                `).join("");
            } catch (error) {
                console.error("🚨 Failed to load categories:", error);
                tableBody.innerHTML = `<tr><td colspan="3" class="text-center p-4 text-red-500">${error.message || 'Failed to load data'}</td></tr>`;
            }
        }

        // ========================================
        // (3) FUNGSI UTAMA: BUKA MODAL (ADD / EDIT)
        // ========================================
        window.showCategoryModal = (category = null) => {
            categoryForm.reset(); // Selalu reset form-nya
            categoryIdInput.value = '';

            if (category) {
                // Mode EDIT
                modalTitle.innerText = `Edit Category (ID: ${category.id})`;
                categoryNameInput.value = category.name;
                categoryIdInput.value = category.id; // Set ID buat form submit
            } else {
                // Mode ADD
                modalTitle.innerText = 'Add Category';
            }
            
            modal.classList.remove('hidden'); // TAMPILKAN MODAL
            categoryNameInput.focus(); // Fokus ke input nama
        }
        
        addBtn.addEventListener('click', () => showCategoryModal());
        window.editCategory = showCategoryModal; // Bikin global

        // ========================================
        // (4) FUNGSI SUBMIT FORM (ADD / EDIT)
        // ========================================
        categoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const categoryId = categoryIdInput.value;
            const categoryName = categoryNameInput.value.trim();
            const isEdit = !!categoryId;
            
            if (!categoryName) {
                Swal.fire({ target: document.body, title: 'Error', text: 'Category Name is required!', icon: 'error' });
                return;
            }

            const endpoint = isEdit 
                ? "{{ route('admin.data.categories.update', ':id') }}".replace(':id', categoryId) 
                : "{{ route('admin.data.categories.store') }}";
            
            const method = isEdit ? "PUT" : "POST";

            try {
                const response = await fetch(endpoint, {
                    method: method,
                    headers: { 
                        'Content-Type': 'application/json', // Kirim sebagai JSON
                        'Accept': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify({ name: categoryName }) // Kirim data nama
                });

                if (!response.ok) {
                    const err = await response.json();
                    if (err.errors) {
                        const errorMessages = Object.values(err.errors).flat().join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(err.message || 'Request failed');
                }
                
                hideModal(); // Tutup modal
                loadCategories(); // Refresh tabel
                Swal.fire({
                    target: document.body,
                    title: "Success!", 
                    text: `Category has been ${isEdit ? 'updated' : 'added'}.`, 
                    icon: "success"
                });

            } catch (error) {
                Swal.fire({
                    target: document.body,
                    title: "Error!", 
                    html: error.message, // pake html biar <br> nya jalan
                    icon: "error"
                });
            }
        });

        // ========================================
        // (5) DELETE CATEGORY (Fungsi ini gak berubah, biarin pake Swal)
        // ========================================
        window.deleteCategory = async (id) => {
            const result = await Swal.fire({
                target: document.body,
                title: "Are you sure?", text: "You won't be able to revert this!", icon: "warning",
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: "Yes, delete it!"
            });
            if (result.isConfirmed) {
                try {
                    const endpoint = "{{ route('admin.data.categories.destroy', ':id') }}".replace(':id', id);
                    const response = await fetch(endpoint, {
                        method: "DELETE",
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    if (!response.ok) {
                        const err = await response.json();
                        throw new Error(err.message || 'Request failed');
                    }
                    Swal.fire({
                        target: document.body,
                        title: "Deleted!", 
                        text: "The category has been deleted.", 
                        icon: "success"
                    });
                    loadCategories();
                } catch (error) {
                     Swal.fire({
                        target: document.body,
                        title: "Error!", 
                        text: error.message, 
                        icon: "error"
                    });
                }
            }
        }

        // ========================================
        // (6) INISIALISASI
        // ========================================
        loadCategories(); // Load kategori pas halaman dibuka
    });
</script>
@endsection