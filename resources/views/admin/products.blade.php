@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="bg-white shadow rounded-lg p-4">
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-xl font-bold">Products</h2>
        <button id="addBtn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">+ Add Product</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded border">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                    <th class="p-3 w-10">#</th>
                    <th class="p-3">Image</th>
                    <th class="p-3">Name</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Price</th>
                    <th class="p-3">Stock (Variants)</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
               <tr><td colspan="7" class="text-center p-4">Loading products...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<div id="product-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
    {{-- UBAH: Tambah max-h-screen dan overflow-y-auto --}}
    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-screen overflow-y-auto">
        
        <form id="product-form" enctype="multipart/form-data">
            <div class="flex justify-between items-center border-b p-4 sticky top-0 bg-white z-10">
                <h3 id="modal-title" class="text-xl font-semibold">Add Product</h3>
                <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" id="product_id" name="product_id"> 
                
                {{-- Kolom Kiri --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>

                    <label for="price" class="block text-sm font-medium text-gray-700 mt-2">Original Price</label>
                    <input type="number" id="price" name="price" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>

                    <label for="category_id" class="block text-sm font-medium text-gray-700 mt-2">Category</label>
                    <select id="category_id" name="category_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                        <option value="">Loading categories...</option>
                    </select>
                    
                    <label for="image" class="block text-sm font-medium text-gray-700 mt-2">Main Image (Thumbnail)</label>
                    <input type="file" id="image" name="image" class="mt-1 block w-full text-sm">
                    <div id="current-image-text" class="mt-1 text-xs text-gray-500"></div>
                </div>
                
                {{-- Kolom Kanan --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="10" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
                </div>

                {{-- Bagian Diskon --}}
                <div class="md:col-span-2 border-t pt-4">
                    <h5 class="text-base font-medium text-gray-800 mb-2">Discount (Optional)</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="discount_price" class="block text-sm font-medium text-gray-700">Discount Price</label>
                            <input type="number" id="discount_price" name="discount_price" placeholder="e.g. 150000" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="discount_start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" id="discount_start_date" name="discount_start_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                        <div>
                            <label for="discount_end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" id="discount_end_date" name="discount_end_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 border-t pt-4">
                    <h5 class="text-base font-medium text-gray-800 mb-2">Product Gallery (Optional)</h5>
                    <label for="gallery_images" class="block text-sm font-medium text-gray-700">Add New Images</label>
                    <input type="file" id="gallery_images" name="gallery_images[]" class="mt-1 block w-full text-sm" multiple>
                    
                    <h6 class="text-sm font-medium text-gray-700 mt-4">Current Gallery Images:</h6>
                    <div id="gallery-preview-container" class="mt-2 flex flex-wrap gap-3">
                        {{-- Thumbnail galeri akan di-load oleh JS di sini --}}
                        <span class="text-xs text-gray-500">No gallery images yet.</span>
                    </div>
                </div>
                <div class="md:col-span-2 border-t pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <h5 class="text-base font-medium text-gray-800">Product Variants (Sizes & Stock)</h5>
                        <button type="button" id="add-variant-btn" class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-2 py-1 rounded font-semibold">+ Add Size</button>
                    </div>
                    <div id="variants-container" class="space-y-2">
                        {{-- Baris varian akan ditambahkan oleh JS --}}
                    </div>
                </div>

            </div>

            <div class="border-t p-4 flex justify-end space-x-2 sticky bottom-0 bg-gray-50 z-10">
                <button type="button" id="cancel-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold">Cancel</button>
                <button type="submit" id="save-btn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">Save</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tableBody = document.getElementById("product-table-body");
        const addBtn = document.getElementById("addBtn");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Elemen Modal
        const modal = document.getElementById("product-modal");
        const closeModalBtn = document.getElementById("close-modal-btn");
        const cancelBtn = document.getElementById("cancel-btn");
        const productForm = document.getElementById("product-form");
        const modalTitle = document.getElementById("modal-title");
        const categorySelect = document.getElementById("category_id");
        const currentImageText = document.getElementById("current-image-text");
        const productIdInput = document.getElementById("product_id");

        // Elemen Varian
        const variantsContainer = document.getElementById("variants-container");
        const addVariantBtn = document.getElementById("add-variant-btn");

        // !! TAMBAHAN: Elemen Galeri !!
        const galleryPreviewContainer = document.getElementById("gallery-preview-container");
        const galleryInput = document.getElementById("gallery_images");

        let categoriesCache = [];

        // ========================================
        // (1) FUNGSI MODAL & FORMATTING
        // ========================================
        const hideModal = () => modal.classList.add('hidden');
        closeModalBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        function formatVariants(variants) {
            if (!variants || variants.length === 0) {
                return `<span class="text-xs text-red-500 font-medium">Out of Stock</span>`;
            }
            return variants.map(v => 
                `<span class="text-xs bg-gray-200 text-gray-700 rounded-full px-2 py-0.5 mr-1">${v.size}: <strong>${v.stock}</strong></span>`
            ).join(' ');
        }
        
        function formatToISODate(dateString) {
            if (!dateString) return '';
            try { return dateString.split(' ')[0]; } catch (e) { return ''; }
        }

        // ========================================
        // (2) LOAD PRODUCTS
        // ========================================
        async function loadProducts() {
            try {
                const response = await fetch("{{ route('admin.data.products.index') }}");
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                const products = await response.json();
                
                if (!Array.isArray(products) || products.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="7" class="text-center p-4 text-gray-500">No products found.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = products.map((p, i) => {
                    const imageUrl = p.image ? `/storage/${p.image}` : `https://placehold.co/100x100/e2e8f0/94a3b8?text=No+Img`;
                        
                    let priceHtml = `Rp ${Number(p.price).toLocaleString('id-ID')}`;
                    if(p.discount_price > 0) {
                        priceHtml = `
                            <span class="text-red-600 font-bold">Rp ${Number(p.discount_price).toLocaleString('id-ID')}</span>
                            <span class="text-xs text-gray-500 line-through">Rp ${Number(p.price).toLocaleString('id-ID')}</span>
                        `;
                    }
                        
                    return `
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="p-3 font-semibold text-gray-700">${i + 1}</td>
                            <td class="p-3">
                                <img src="${imageUrl}" alt="${p.name}" class="w-12 h-12 rounded-md object-cover bg-gray-200">
                                {{-- Hitung jumlah galeri --}}
                                <span class="text-xs text-gray-500">${p.images ? p.images.length : 0} more</span>
                            </td>
                            <td class="p-3 font-medium text-gray-800">${p.name}</td>
                            <td class="p-3 text-gray-600">${p.category ? p.category.name : '-'}</td>
                            <td class="p-3 text-gray-600">${priceHtml}</td>
                            <td class="p-3 text-gray-600 whitespace-nowrap">${formatVariants(p.variants)}</td>
                            <td class="p-3 text-right">
                                <button onclick='showProductModal(${JSON.stringify(p)})' class="text-blue-600 hover:underline font-semibold">Edit</button>
                                <button onclick="deleteProduct(${p.id})" class="text-red-600 hover:underline ml-3 font-semibold">Delete</button>
                            </td>
                        </tr>
                    `;
                }).join("");
            } catch (error) {
                console.error("🚨 Failed to load products:", error);
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center p-4 text-red-500">${error.message}</td></tr>`;
            }
        }

        // ========================================
        // (3) LOAD KATEGORI
        // ========================================
        async function loadCategoryOptions(selectedId = null) {
            try {
                if (categoriesCache.length === 0) {
                    const catResponse = await fetch("{{ route('admin.data.categories.index') }}");
                    if (!catResponse.ok) throw new Error('Failed to load categories');
                    categoriesCache = await catResponse.json(); 
                }
                
                categorySelect.innerHTML = '<option value="">Select Category</option>';
                categoriesCache.forEach(c => {
                    const selected = (c.id == selectedId) ? 'selected' : '';
                    categorySelect.innerHTML += `<option value="${c.id}" ${selected}>${c.name}</option>`;
                });
            } catch (error) {
                console.error("🚨 Failed to load categories:", error);
                categorySelect.innerHTML = '<option value="">Failed to load categories</option>';
            }
        }

        // ========================================
        // (4) LOGIKA VARIAN
        // ========================================
        const addVariantRow = (variant = null) => {
            const index = variantsContainer.children.length; 
            const size = variant ? variant.size : '';
            const stock = variant ? variant.stock : '';
            
            const newRow = `
                <div class="flex space-x-2 variant-row">
                    <div class="flex-1"><input type="text" name="variants[${index}][size]" value="${size}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm" placeholder="Size (e.g., S, M, L)" required></div>
                    <div class="flex-1"><input type="number" name="variants[${index}][stock]" value="${stock}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 text-sm" placeholder="Stock" required min="0"></div>
                    <button type="button" class="bg-red-500 text-white px-2 py-1 rounded font-bold remove-variant-btn mt-1">&times;</button>
                </div>`;
            variantsContainer.insertAdjacentHTML('beforeend', newRow);
        };
        addVariantBtn.addEventListener('click', () => addVariantRow());
        variantsContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-variant-btn')) {
                e.target.closest('.variant-row').remove();
            }
        });

        // ========================================
        // (5) !! TAMBAHAN: LOGIKA GALERI GAMBAR !!
        // ========================================
        const addGalleryThumbnail = (image) => {
            const imageUrl = `/storage/${image.image_path}`;
            const newThumb = `
                <div class="relative w-16 h-16 rounded-md overflow-hidden border" id="image-thumb-${image.id}">
                    <img src="${imageUrl}" class="w-full h-full object-cover">
                    <button type="button" 
                            onclick="deleteGalleryImage(${image.id})"
                            class="absolute top-0 right-0 p-0.5 bg-red-600 text-white rounded-bl-md leading-none hover:bg-red-800">
                        &times;
                    </button>
                </div>
            `;
            galleryPreviewContainer.insertAdjacentHTML('beforeend', newThumb);
        };

        window.deleteGalleryImage = async (imageId) => {
            const result = await Swal.fire({
                target: modal, // Tampilkan di atas modal
                title: "Delete this image?", text: "This action is permanent.", icon: "warning",
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: "Yes, delete it!"
            });
            
            if (result.isConfirmed) {
                const endpoint = "{{ route('admin.data.products.images.destroy', ':id') }}".replace(':id', imageId);
                try {
                    const response = await fetch(endpoint, {
                        method: "DELETE",
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    if (!response.ok) throw new Error('Failed to delete image.');
                    
                    // Hapus thumbnail dari view
                    document.getElementById(`image-thumb-${imageId}`).remove();
                    Swal.fire({ target: modal, title: "Deleted!", icon: "success", timer: 1000 });
                } catch (error) {
                    Swal.fire({ target: modal, title: "Error!", text: error.message, icon: "error" });
                }
            }
        };

        // ========================================
        // (6) BUKA MODAL (ADD / EDIT)
        // ========================================
        window.showProductModal = async (product = null) => {
            productForm.reset(); 
            currentImageText.innerText = '';
            productIdInput.value = '';
            variantsContainer.innerHTML = ''; 
            galleryPreviewContainer.innerHTML = '<span class="text-xs text-gray-500">No gallery images yet.</span>'; // Reset galeri
            galleryInput.value = ''; // Reset input file

            if (product) {
                // Mode EDIT
                modalTitle.innerText = `Edit Product (ID: ${product.id})`;
                document.getElementById('name').value = product.name;
                document.getElementById('price').value = product.price;
                document.getElementById('description').value = product.description;
                productIdInput.value = product.id;
                if (product.image) {
                    currentImageText.innerText = `Current: ${product.image} (Leave empty to keep)`;
                }
                
                document.getElementById('discount_price').value = product.discount_price;
                document.getElementById('discount_start_date').value = formatToISODate(product.discount_start_date);
                document.getElementById('discount_end_date').value = formatToISODate(product.discount_end_date);
                
                // Isi Varian
                if (product.variants && product.variants.length > 0) {
                    product.variants.forEach(v => addVariantRow(v));
                } else { addVariantRow(); }
                
                // !! UBAH: Isi Galeri !!
                if (product.images && product.images.length > 0) {
                    galleryPreviewContainer.innerHTML = ''; // Hapus teks "No images"
                    product.images.forEach(img => addGalleryThumbnail(img));
                }
                
                await loadCategoryOptions(product.category_id);
            } else {
                // Mode ADD
                modalTitle.innerText = 'Add Product';
                addVariantRow(); 
                await loadCategoryOptions();
            }
            
            modal.classList.remove('hidden'); 
        }
        
        addBtn.addEventListener('click', () => showProductModal());
        // window.editProduct = showProductModal; // Sudah ada di 'onclick' tabel

        // ========================================
        // (7) SUBMIT FORM (ADD / EDIT)
        // ========================================
        productForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(productForm);
            const isEdit = !!productIdInput.value;
            
            const endpoint = isEdit 
                ? "{{ route('admin.data.products.update', ':id') }}".replace(':id', productIdInput.value) 
                : "{{ route('admin.data.products.store') }}";
            
            const method = "POST";
            if (isEdit) { formData.append("_method", "PUT"); } 

            try {
                const response = await fetch(endpoint, {
                    method: method,
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: formData // FormData akan handle file uploads
                });

                if (!response.ok) {
                    const err = await response.json();
                    if (err.errors) {
                        let errorMessages = [];
                        for (const key in err.errors) {
                             errorMessages.push(err.errors[key][0]);
                        }
                        throw new Error(errorMessages.join('<br>'));
                    }
                    throw new Error(err.message || 'Request failed');
                }
                
                hideModal(); 
                loadProducts(); // Refresh tabel (otomatis nampilin data galeri baru)
                Swal.fire({
                    target: document.body,
                    title: "Success!", 
                    text: `Product has been ${isEdit ? 'updated' : 'added'}.`, 
                    icon: "success"
                });

            } catch (error) {
                Swal.fire({
                    target: document.body,
                    title: "Error!", 
                    html: error.message, 
                    icon: "error"
                });
            }
        });

        // ========================================
        // (8) DELETE PRODUCT
        // ========================================
        window.deleteProduct = async (id) => {
            // (Tidak ada perubahan di sini)
            const result = await Swal.fire({
                target: document.body,
                title: "Are you sure?", text: "This will delete the product AND all its images/variants!", icon: "warning",
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: "Yes, delete it!"
            });
            if (result.isConfirmed) {
                try {
                    const endpoint = "{{ route('admin.data.products.destroy', ':id') }}".replace(':id', id);
                    const response = await fetch(endpoint, {
                        method: "DELETE",
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                    });
                    if (!response.ok) throw new Error('Failed to delete.');
                    Swal.fire({ target: document.body, title: "Deleted!", icon: "success" });
                    loadProducts();
                } catch (error) {
                     Swal.fire({ target: document.body, title: "Error!", text: error.message, icon: "error" });
                }
            }
        }

        // ========================================
        // (9) INISIALISASI
        // ========================================
        loadProducts(); // Load produk pas halaman dibuka
    });
</script>
@endsection