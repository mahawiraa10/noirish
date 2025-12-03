@extends('layouts.admin')

@section('title', 'Manage Coupons')

@section('content')
<div class="bg-white shadow rounded-lg p-4">
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-xl font-bold">Coupon Management</h2>
        <button id="addBtn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">+ Add Coupon</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded border">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                    <th class="p-3">Code</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Value / Cap</th>
                    <th class="p-3">Min. Spend</th>
                    <th class="p-3">Expires</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="coupons-table-body">
               <tr><td colspan="6" class="text-center p-4">Loading coupons...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Add/Edit --}}
<div id="coupon-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl">

        <form id="coupon-form">
            <div class="flex justify-between items-center border-b p-4">
                <h3 id="modal-title" class="text-xl font-semibold">Add Coupon</h3>
                <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-4 space-y-4">
                <input type="hidden" id="coupon_id" name="id">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Coupon Code</label>
                        <input type="text" id="code" name="code" placeholder="e.g., HEMAT50" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Coupon Type</label>
                        <select id="type" name="type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                            <option value="percent">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (Rp)</option>
                            <option value="free_shipping">Free Shipping</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <input type="text" id="description" name="description" placeholder="e.g., Diskon 50% Min. Belanja 500rb" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        {{-- !! PERUBAHAN 1: Label & Placeholder lebih informatif !! --}}
                        <label for="value" class="block text-sm font-medium text-gray-700">Value / Max Cap</label>
                        <input type="number" id="value" name="value" placeholder="e.g., 50, 10000, or 0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                        <p class="mt-1 text-xs text-gray-500">
                            - <b>Percent:</b> 50 = 50% <br>
                            - <b>Fixed:</b> 10000 = Potongan Rp 10.000 <br>
                            - <b>Free Ship:</b> 20000 = Max Ongkir 20rb (Isi 0 untuk Unlimited)
                        </p>
                    </div>
                    <div>
                        <label for="min_spend" class="block text-sm font-medium text-gray-700">Min. Spend (Optional)</label>
                        <input type="number" id="min_spend" name="min_spend" placeholder="e.g., 500000" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="max_uses_user" class="block text-sm font-medium text-gray-700">Max Uses per User (Optional)</label>
                        <input type="number" id="max_uses_user" name="max_uses_user" placeholder="1 (untuk pesanan pertama)" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                     <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700">Expires At (Optional)</label>
                        <input type="date" id="expires_at" name="expires_at" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    </div>
                </div>
            </div>

            <div class="border-t p-4 flex justify-end space-x-2">
                <button type="button" id="cancel-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold">Cancel</button>
                <button type="submit" id="save-btn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">Save Coupon</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tableBody = document.getElementById("coupons-table-body");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Elemen Modal
        const modal = document.getElementById("coupon-modal");
        const addBtn = document.getElementById("addBtn");
        const closeModalBtn = document.getElementById("close-modal-btn");
        const cancelBtn = document.getElementById("cancel-btn");
        const methodForm = document.getElementById("coupon-form");
        const modalTitle = document.getElementById("modal-title");
        const methodIdInput = document.getElementById("coupon_id");

        // Helper format tanggal
        function formatToISODate(dateString) {
            if (!dateString) return '';
            try { return dateString.split(' ')[0]; } catch (e) { return ''; }
        }

        // Rute
        const INDEX_URL = "{{ route('admin.data.coupons.index') }}";
        const STORE_URL = "{{ route('admin.data.coupons.store') }}";
        const UPDATE_URL_TEMPLATE = "{{ route('admin.data.coupons.update', ':id') }}";
        const DELETE_URL_TEMPLATE = "{{ route('admin.data.coupons.destroy', ':id') }}";

        // (1) FUNGSI MODAL
        const hideModal = () => modal.classList.add('hidden');
        closeModalBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        // (2) LOAD & RENDER TABEL
        async function loadCoupons() {
            try {
                const response = await fetch(INDEX_URL);
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                const coupons = await response.json();

                if (!Array.isArray(coupons) || coupons.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-gray-500">No coupons found.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = coupons.map((coupon, i) => {
                    let valueText = 'N/A';
                    if(coupon.type === 'percent') {
                        valueText = `${coupon.value}%`;
                    } else if(coupon.type === 'fixed') {
                        valueText = `Rp ${Number(coupon.value).toLocaleString('id-ID')}`;
                    } else if(coupon.type === 'free_shipping') {
                        // !! PERUBAHAN 2: Tampilan Tabel Lebih Jelas !!
                        if (coupon.value > 0) {
                            valueText = `Free Ship (Max Rp ${Number(coupon.value).toLocaleString('id-ID')})`;
                        } else {
                            valueText = 'Free Shipping (Unlimited)';
                        }
                    }

                    const minSpend = coupon.min_spend ? `Rp ${Number(coupon.min_spend).toLocaleString('id-ID')}` : '-';
                    const expires = coupon.expires_at ? formatToISODate(coupon.expires_at) : 'Never';

                    return `
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="p-3 font-medium text-gray-800">${coupon.code}</td>
                            <td class="p-3 text-gray-600">${coupon.type}</td>
                            <td class="p-3 text-gray-600">${valueText}</td>
                            <td class="p-3 text-gray-600">${minSpend}</td>
                            <td class="p-3 text-gray-600">${expires}</td>
                            <td class="p-3 text-right">
                                <button onclick='showEditModal(${JSON.stringify(coupon)})' class="text-blue-600 hover:underline font-semibold">Edit</button>
                                <button onclick="deleteCoupon(${coupon.id})" class="text-red-600 hover:underline ml-3 font-semibold">Delete</button>
                            </td>
                        </tr>
                    `;
                }).join("");
            } catch (error) {
                console.error("🚨 Failed to load coupons:", error);
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center p-4 text-red-500">${error.message}</td></tr>`;
            }
        }

        // (3) BUKA MODAL ADD
        addBtn.addEventListener('click', () => {
            methodForm.reset();
            modalTitle.innerText = 'Add Coupon';
            methodIdInput.value = '';
            modal.classList.remove('hidden');
        });

        // (4) BUKA MODAL EDIT
        window.showEditModal = (coupon) => {
            methodForm.reset();
            modalTitle.innerText = 'Edit Coupon';
            methodIdInput.value = coupon.id;

            document.getElementById('code').value = coupon.code;
            document.getElementById('description').value = coupon.description;
            document.getElementById('type').value = coupon.type;
            document.getElementById('value').value = coupon.value;
            document.getElementById('min_spend').value = coupon.min_spend;
            document.getElementById('max_uses_user').value = coupon.max_uses_user;
            document.getElementById('expires_at').value = formatToISODate(coupon.expires_at);

            modal.classList.remove('hidden');
        }

        // (5) FUNGSI SUBMIT FORM
        methodForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const id = methodIdInput.value;
            const isEdit = !!id;

            const endpoint = isEdit ? UPDATE_URL_TEMPLATE.replace(':id', id) : STORE_URL;
            const method = isEdit ? "PUT" : "POST";

            const formData = new FormData(methodForm);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(endpoint, {
                    method: method,
                    headers: { 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const err = await response.json();
                    if (err.errors) {
                        const errorMessages = Object.values(err.errors).flat().join('<br>');
                        throw new Error(errorMessages);
                    }
                    throw new Error(err.message || 'Request failed');
                }

                hideModal();
                loadCoupons();
                Swal.fire({
                    target: document.body,
                    title: "Success!", 
                    text: `Coupon has been ${isEdit ? 'updated' : 'saved'}.`, 
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


        // (6) DELETE METHOD
        window.deleteCoupon = async (id) => {
            const result = await Swal.fire({
                target: document.body,
                title: "Are you sure?", text: "This will permanently delete the coupon.", icon: "warning",
                showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: "Yes, delete it!"
            });

            if (result.isConfirmed) {
                try {
                    const endpoint = DELETE_URL_TEMPLATE.replace(':id', id);
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
                        text: "The coupon has been deleted.", 
                        icon: "success"
                    });
                    loadCoupons();
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

        // (7) INISIALISASI
        loadCoupons();
    });
</script>
@endsection