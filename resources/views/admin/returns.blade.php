@extends('layouts.admin')

@section('title', 'Returns Management')

@section('content')
<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Return Requests</h2>
        <span class="text-xs font-bold px-2.5 py-1 rounded bg-slate-200 text-slate-700 uppercase tracking-wide">Live Data</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="p-4 border-b font-semibold w-16 text-center">#</th>
                    <th class="p-4 border-b font-semibold">Order ID</th>
                    <th class="p-4 border-b font-semibold">Customer</th>
                    <th class="p-4 border-b font-semibold">Product</th>
                    <th class="p-4 border-b font-semibold text-center">Type</th>
                    <th class="p-4 border-b font-semibold">Date</th>
                    <th class="p-4 border-b font-semibold text-center">Status</th>
                    <th class="p-4 border-b font-semibold text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="returns-table-body" class="text-gray-700 text-sm">
               <tr><td colspan="8" class="text-center p-8 text-gray-400 italic">Loading return requests...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tableBody = document.getElementById("returns-table-body");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // 1. LOAD RETURNS
        async function loadReturns() {
            try {
                const response = await fetch("{{ route('admin.data.returns.index') }}");
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                const returns = await response.json();
                
                if (!Array.isArray(returns) || returns.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center p-8 text-gray-500 bg-gray-50">No return requests found.</td></tr>`;
                    return;
                }

                tableBody.innerHTML = returns.map((r, i) => {
                    const customerName = r.user?.name || r.order?.customer?.name || 'Unknown User';
                    const productName = r.product?.name || 'Unknown Product';
                    const dateRequested = new Date(r.created_at).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });

                    let statusClass = 'bg-gray-100 text-gray-800';
                    if (r.status === 'approved') statusClass = 'bg-green-100 text-green-800 border border-green-200';
                    else if (r.status === 'rejected') statusClass = 'bg-red-100 text-red-800 border border-red-200';
                    else if (r.status === 'pending') statusClass = 'bg-yellow-100 text-yellow-800 border border-yellow-200';

                    const typeBadge = r.type === 'refund' 
                        ? '<span class="px-2 py-1 bg-purple-100 text-purple-700 border border-purple-200 rounded text-xs font-bold uppercase">Refund</span>' 
                        : '<span class="px-2 py-1 bg-blue-100 text-blue-700 border border-blue-200 rounded text-xs font-bold uppercase">Return</span>';

                    const returnJson = encodeURIComponent(JSON.stringify(r));

                    return `
                        <tr class="border-b hover:bg-gray-50 transition duration-150">
                            <td class="p-4 text-center font-medium text-gray-500">${i + 1}</td>
                            <td class="p-4 font-bold text-gray-800">#${r.order_id}</td>
                            <td class="p-4 font-medium">${customerName}</td>
                            <td class="p-4 text-gray-600 truncate max-w-xs" title="${productName}">${productName}</td>
                            <td class="p-4 text-center">${typeBadge}</td>
                            <td class="p-4 text-gray-500">${dateRequested}</td>
                            <td class="p-4 text-center"><span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize ${statusClass}">${r.status}</span></td>
                            <td class="p-4 text-right space-x-3">
                                <button onclick="openDetailModal('${returnJson}')" class="text-blue-600 hover:text-blue-800 hover:underline font-bold text-sm transition">Review</button>
                                <button onclick="deleteReturn(${r.id})" class="text-red-600 hover:text-red-800 hover:underline font-bold text-sm transition">Delete</button>
                            </td>
                        </tr>
                    `;
                }).join("");
            } catch (error) {
                console.error("Error:", error);
            }
        }
            
        // 2. OPEN DETAIL MODAL
        window.openDetailModal = (jsonString) => {
            const r = JSON.parse(decodeURIComponent(jsonString));
            const customerName = r.user?.name || 'Unknown';
            const productName = r.product?.name || 'Unknown';
            const requestDate = new Date(r.created_at).toLocaleString('en-US', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            
            let imagesHtml = '<p class="text-sm text-gray-400 italic">No evidence uploaded.</p>';
            if (r.images && r.images.length > 0) {
                imagesHtml = `<div class="grid grid-cols-3 gap-2 mt-2">` + 
                    r.images.map(img => `<a href="/storage/${img.image_path}" target="_blank" class="block border rounded-lg overflow-hidden hover:opacity-75 transition shadow-sm"><img src="/storage/${img.image_path}" class="w-full h-20 object-cover"></a>`).join('') + 
                    `</div>`;
            }

            const statusOptions = ['pending', 'approved', 'rejected'].map(s => 
                `<option value="${s}" ${r.status === s ? 'selected' : ''}>${s.charAt(0).toUpperCase() + s.slice(1)}</option>`
            ).join('');

            Swal.fire({
                title: `<span class="text-xl font-bold text-gray-800">Request #${r.id} (${r.type.toUpperCase()})</span>`,
                html: `
                    <div class="text-left space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 grid grid-cols-2 gap-y-4 gap-x-2 text-sm">
                            <div><p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Type</p><p class="font-bold ${r.type === 'refund' ? 'text-purple-600' : 'text-blue-600'} uppercase">${r.type}</p></div>
                            <div><p class="text-xs text-gray-500 uppercase font-bold tracking-wider">Reason</p><p class="italic text-gray-700">"${r.reason}"</p></div>
                            <div class="col-span-2"><p class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Evidence</p>${imagesHtml}</div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Update Status</label>
                            <select id="swal-return-status" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-slate-500 p-2.5 text-sm font-medium mb-3">
                                ${statusOptions}
                            </select>

                            {{-- DYNAMIC FIELD: REJECTED --}}
                            <div id="dynamic-action-area" class="p-3 bg-red-50 border border-red-200 rounded-md hidden">
                                <label class="block text-xs font-bold text-red-700 uppercase mb-1">Rejection Reason (Required)</label>
                                <textarea id="dynamic-input" class="w-full border-red-300 rounded text-sm p-2" rows="2" placeholder="Explain why..."></textarea>
                            </div>
                            
                            {{-- INFO: APPROVED --}}
                            <div id="approved-info" class="p-3 bg-blue-50 border border-blue-200 rounded-md hidden">
                                <p class="text-xs text-blue-700 font-bold">ℹ️ Note:</p>
                                <p class="text-xs text-blue-600">If approved, please go to <a href="/admin/orders" class="underline font-bold">Orders Menu</a> to input the replacement tracking number.</p>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Save Changes',
                confirmButtonColor: '#1f2937', 
                cancelButtonColor: '#9ca3af',
                width: '550px',
                customClass: { popup: '!rounded-xl', confirmButton: 'font-bold uppercase text-sm', cancelButton: 'font-bold uppercase text-sm' },
                
                didOpen: () => {
                    const select = document.getElementById('swal-return-status');
                    const area = document.getElementById('dynamic-action-area');
                    const info = document.getElementById('approved-info');
                    const input = document.getElementById('dynamic-input');

                    const updateUI = () => {
                        const status = select.value;
                        area.classList.add('hidden');
                        info.classList.add('hidden');
                        input.value = ''; 

                        if (status === 'rejected') {
                            area.classList.remove('hidden');
                        } else if (status === 'approved' && r.type === 'return') {
                            info.classList.remove('hidden');
                        }
                    };
                    updateUI();
                    select.addEventListener('change', updateUI);
                },

                // ===========================================
                // !! LOGIC VALIDASI "PENDING" DITAMBAHKAN DI SINI !!
                // ===========================================
                preConfirm: async () => {
                    const newStatus = document.getElementById('swal-return-status').value;
                    const adminResponse = document.getElementById('dynamic-input').value;

                    // 1. CEK: Tidak boleh save kalau masih Pending
                    if (newStatus === 'pending') {
                        Swal.showValidationMessage('You cannot do the action!');
                        return false;
                    }

                    // 2. CEK: Rejected wajib alasan
                    if (newStatus === 'rejected' && !adminResponse.trim()) {
                        Swal.showValidationMessage('Please provide a rejection reason.');
                        return false;
                    }

                    // Jika lolos validasi, baru update
                    return await updateReturnStatus(r.id, newStatus, adminResponse);
                }
            }).then((result) => {
                if (result.isConfirmed) loadReturns();
            });
        }

        // 3. UPDATE FUNCTION
        async function updateReturnStatus(id, status, responseText) {
            try {
                const endpoint = "{{ route('admin.data.returns.update', ':id') }}".replace(':id', id);
                const response = await fetch(endpoint, { 
                    method: "PUT", 
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ status: status, admin_response: responseText }) 
                });
                
                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.message || 'Update failed');
                }

                Swal.fire({ icon: 'success', title: 'Updated!', text: 'Request processed successfully.', timer: 1500, showConfirmButton: false });
                return true;
            } catch (error) {
                Swal.showValidationMessage(error.message);
                return false;
            }
        }

        // 4. DELETE FUNCTION
        window.deleteReturn = async (id) => {
            const result = await Swal.fire({
                title: "Are you sure?", text: "This action cannot be undone!", icon: "warning",
                showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: "Yes, delete it",
                customClass: { popup: '!rounded-xl', confirmButton: 'font-bold', cancelButton: 'font-bold' }
            });

            if (result.isConfirmed) {
                try {
                    const endpoint = "{{ route('admin.data.returns.destroy', ':id') }}".replace(':id', id);
                    await fetch(endpoint, { method: "DELETE", headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                    Swal.fire("Deleted!", "Record has been deleted.", "success");
                    loadReturns();
                } catch (error) {
                    Swal.fire("Error!", "Delete failed", "error");
                }
            }
        }

        loadReturns();
    });
</script>
@endsection