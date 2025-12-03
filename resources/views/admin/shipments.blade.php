@extends('layouts.admin')

@section('title', 'Shipping Methods')

@section('content')
<div class="bg-white shadow rounded-lg p-4">
    <div class="flex justify-between mb-4 items-center">
        <h2 class="text-xl font-bold">Shipping Methods</h2>
        <button id="addBtn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">+ Add Method</button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full bg-white rounded border">
            <thead>
                <tr class="bg-gray-100 text-left text-sm font-semibold text-gray-600">
                    <th class="p-3 w-10">#</th>
                    <th class="p-3">Method Name</th>
                    <th class="p-3">Description</th>
                    <th class="p-3">Cost</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="methods-table-body">
               <tr><td colspan="5" class="text-center p-4">Loading methods...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Add/Edit --}}
<div id="method-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        
        <form id="method-form">
            <div class="flex justify-between items-center border-b p-4">
                <h3 id="modal-title" class="text-xl font-semibold">Add Shipping Method</h3>
                <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <div class="p-4 space-y-4">
                <input type="hidden" id="method_id" name="id">
                
                {{-- Method Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Method Name</label>
                    <input type="text" id="name" name="name" placeholder="e.g., JNE Regular" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>

                {{-- Cost --}}
                <div>
                    <label for="cost" class="block text-sm font-medium text-gray-700">Cost (Flat Rate)</label>
                    <input type="number" id="cost" name="cost" value="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
                    <textarea id="description" name="description" rows="3" class_name="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="e.g., Estimasi 2-3 hari kerja"></textarea>
                </div>
            </div>

            <div class="border-t p-4 flex justify-end space-x-2">
                <button type="button" id="cancel-btn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold">Cancel</button>
                <button type="submit" id="save-btn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded font-semibold">Save Method</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tableBody = document.getElementById("methods-table-body");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Elemen Modal
        const modal = document.getElementById("method-modal");
        const addBtn = document.getElementById("addBtn");
        const closeModalBtn = document.getElementById("close-modal-btn");
        const cancelBtn = document.getElementById("cancel-btn");
        const methodForm = document.getElementById("method-form");
        const modalTitle = document.getElementById("modal-title");
        const methodIdInput = document.getElementById("method_id");

        // Rute
        const INDEX_URL = "{{ route('admin.data.shipments.index') }}";
        const STORE_URL = "{{ route('admin.data.shipments.store') }}";
        const UPDATE_URL_TEMPLATE = "{{ route('admin.data.shipments.update', ':id') }}";
        const DELETE_URL_TEMPLATE = "{{ route('admin.data.shipments.destroy', ':id') }}";

        // ========================================
        // (1) FUNGSI MODAL
        // ========================================
        const hideModal = () => modal.classList.add('hidden');
        closeModalBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        // ========================================
        // (2) LOAD & RENDER TABEL
        // ========================================
        async function loadMethods() {
            try {
                const response = await fetch(INDEX_URL);
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                const methods = await response.json();
                
                if (!Array.isArray(methods) || methods.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="5" class="text-center p-4 text-gray-500">No shipping methods found.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = methods.map((method, i) => {
                    const cost = Number(method.cost).toLocaleString('id-ID');
                    return `
                        <tr class="border-t hover:bg-gray-50 transition">
                            <td class="p-3 font-semibold text-gray-700">${i + 1}</td>
                            <td class="p-3 font-medium text-gray-800">${method.name}</td>
                            <td class="p-3 text-gray-600">${method.description || '-'}</td>
                            <td class="p-3 text-gray-600">Rp ${cost}</td>
                            <td class="p-3 text-right">
                                <button onclick='showEditModal(${JSON.stringify(method)})' class="text-blue-600 hover:underline font-semibold">Edit</button>
                                <button onclick="deleteMethod(${method.id})" class="text-red-600 hover:underline ml-3 font-semibold">Delete</button>
                            </td>
                        </tr>
                    `;
                }).join("");
            } catch (error) {
                console.error("🚨 Failed to load methods:", error);
                tableBody.innerHTML = `<tr><td colspan="5" class="text-center p-4 text-red-500">${error.message}</td></tr>`;
            }
        }

        // ========================================
        // (3) BUKA MODAL ADD
        // ========================================
        addBtn.addEventListener('click', () => {
            methodForm.reset();
            modalTitle.innerText = 'Add Shipping Method';
            methodIdInput.value = '';
            modal.classList.remove('hidden');
        });

        // ========================================
        // (4) BUKA MODAL EDIT
        // ========================================
        window.showEditModal = (method) => {
            methodForm.reset();
            modalTitle.innerText = 'Edit Shipping Method';
            methodIdInput.value = method.id;
            
            // Isi form
            document.getElementById('name').value = method.name;
            document.getElementById('description').value = method.description;
            document.getElementById('cost').value = method.cost;

            modal.classList.remove('hidden');
        }

        // ========================================
        // (5) FUNGSI SUBMIT FORM (ADD / EDIT)
        // ========================================
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
                    method: method, // PUT/POST
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
                loadMethods();
                Swal.fire({
                    target: document.body,
                    title: "Success!", 
                    text: `Method has been ${isEdit ? 'updated' : 'saved'}.`, 
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
        // (6) DELETE METHOD
        // ========================================
        window.deleteMethod = async (id) => {
            const result = await Swal.fire({
                target: document.body,
                title: "Are you sure?", text: "This will permanently delete the shipping method.", icon: "warning",
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
                        text: "The shipping method has been deleted.", 
                        icon: "success"
                    });
                    loadMethods(); // Refresh tabel
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
        // (7) INISIALISASI
        // ========================================
        loadMethods();
    });
</script>
@endsection