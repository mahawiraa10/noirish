@extends('layouts.admin')

@section('title', 'Orders Management')

@section('content')
<div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h2 class="text-xl font-bold text-gray-800">Orders List</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                    <th class="p-4 border-b font-semibold w-16 text-center">#</th>
                    <th class="p-4 border-b font-semibold">Customer</th>
                    <th class="p-4 border-b font-semibold">Date</th>
                    <th class="p-4 border-b font-semibold">Total</th>
                    <th class="p-4 border-b font-semibold text-center">Order Status</th>
                    <th class="p-4 border-b font-semibold text-center">Shipment</th>
                    <th class="p-4 border-b font-semibold text-center">Action</th> {{-- Kolom Khusus Retur/Action --}}
                    <th class="p-4 border-b font-semibold text-right">Manage</th>
                </tr>
            </thead>
            <tbody id="order-table-body" class="text-gray-700 text-sm">
               <tr><td colspan="8" class="text-center p-8 text-gray-400 italic">Loading orders...</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL SHIPMENT --}}
<div id="shipment-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
        
        <form id="shipment-form">
            <div class="flex justify-between items-center border-b p-5 bg-gray-50 rounded-t-lg">
                <h3 id="shipment-modal-title" class="text-lg font-bold text-gray-800">Manage Shipment</h3>
                <button type="button" id="close-shipment-modal-btn" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>

            <div class="p-6 space-y-4">
                <input type="hidden" id="shipment_id" name="shipment_id">
                <input type="hidden" id="order_id" name="order_id">
                {{-- Hidden input untuk menandai apakah ini input resi RETUR atau ORDER BIASA --}}
                <input type="hidden" id="is_return_replacement" name="is_return_replacement" value="0"> 
                <input type="hidden" id="return_request_id" name="return_request_id" value="">

                {{-- Alert Info (Akan muncul jika ini mode Retur) --}}
                <div id="return-alert" class="hidden bg-blue-50 border-l-4 border-blue-500 p-3 mb-2">
                    <p class="text-sm text-blue-700 font-bold">♻️ REPLACEMENT MODE</p>
                    <p class="text-xs text-blue-600">You are entering the tracking number for the <span class="underline">replacement item</span>.</p>
                </div>

                <div>
                    <label for="address" class="block text-sm font-bold text-gray-700 mb-1">Address</label>
                    <textarea id="address" name="address" rows="3" class="w-full border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm cursor-not-allowed shadow-sm p-2.5" readonly></textarea>
                </div>
                
                <div>
                    <label for="tracking_number" class="block text-sm font-bold text-gray-700 mb-1">Tracking Number (Resi)</label>
                    <input type="text" id="tracking_number" name="tracking_number" class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 text-sm focus:border-slate-500 focus:ring-slate-500" placeholder="e.g., JP1234567890" required>
                </div>

                 <div>
                    <label for="status" class="block text-sm font-bold text-gray-700 mb-1">Shipment Status</label>
                    <select id="status" name="status" class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 text-sm focus:border-slate-500 focus:ring-slate-500" required>
                        <option value="pending">Pending</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>

                <div>
                    <label for="cost" class="block text-sm font-bold text-gray-700 mb-1">Shipping Cost (Paid by Customer)</label>
                    <input type="text" id="cost" name="cost" class="w-full border-gray-300 rounded-lg bg-gray-100 text-gray-500 text-sm cursor-not-allowed shadow-sm p-2.5" readonly>
                </div>
            </div>

            <div class="border-t p-5 flex justify-end space-x-2 bg-gray-50 rounded-b-lg">
                <button type="button" id="cancel-shipment-btn" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-100 transition">Cancel</button>
                <button type="submit" id="save-shipment-btn" class="bg-slate-700 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-bold text-sm shadow-md transition">Save Shipment</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tableBody = document.getElementById("order-table-body");
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Modal Elements
        const shipmentModal = document.getElementById("shipment-modal");
        const closeBtn = document.getElementById("close-shipment-modal-btn");
        const cancelBtn = document.getElementById("cancel-shipment-btn");
        const form = document.getElementById("shipment-form");
        const title = document.getElementById("shipment-modal-title");
        const returnAlert = document.getElementById("return-alert");
        
        // Inputs
        const inpShipmentId = document.getElementById("shipment_id");
        const inpOrderId = document.getElementById("order_id");
        const inpTracking = document.getElementById("tracking_number");
        const inpStatus = document.getElementById("status");
        const inpCost = document.getElementById("cost");
        const inpAddress = document.getElementById("address");
        const inpIsReturn = document.getElementById("is_return_replacement");
        const inpReturnReqId = document.getElementById("return_request_id");

        const hideModal = () => shipmentModal.classList.add('hidden');
        closeBtn.addEventListener('click', hideModal);
        cancelBtn.addEventListener('click', hideModal);

        // ========================================
        // 1. LOAD ORDERS
        // ========================================
        async function loadOrders() {
            try {
                const response = await fetch("{{ route('admin.data.orders.index') }}");
                if (!response.ok) throw new Error(`HTTP error ${response.status}`);
                const orders = await response.json();
                
                if (!Array.isArray(orders.data) || orders.data.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center p-8 text-gray-500 bg-gray-50">No orders found.</td></tr>`;
                    return;
                }
                
                tableBody.innerHTML = orders.data.map((o, i) => {
                    const customerName = o.customer ? o.customer.name : 'Guest';
                    const orderDate = o.created_at ? new Date(o.created_at).toLocaleDateString('id-ID') : '-'; 
                    const total = Number(o.total).toLocaleString('id-ID');
                    
                    // --- LOGIKA STATUS ---
                    let statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize bg-gray-100 text-gray-800 border border-gray-200">${o.status}</span>`;
                    if (o.status === 'paid') statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize bg-green-100 text-green-800 border border-green-200">Paid</span>`;
                    else if (o.status === 'pending_payment') statusBadge = `<span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize bg-yellow-100 text-yellow-800 border border-yellow-200">Pending</span>`;

                    // --- LOGIKA SHIPMENT ---
                    const shipmentStatus = o.shipment ? o.shipment.status : 'Not Shipped';
                    let shipBadge = `<span class="text-gray-500 italic text-xs">Waiting</span>`;
                    if(shipmentStatus !== 'Not Shipped') shipBadge = `<span class="font-bold text-gray-700 capitalize">${shipmentStatus}</span>`;

                    // --- LOGIKA RETUR (PENTING) ---
                    // Cek apakah ada retur yang APPROVED (Artinya butuh kirim balik)
                    let returnAction = '-';
                    let hasReplacementNeed = false;
                    let activeReturnReq = null;

                    if (o.return_requests && o.return_requests.length > 0) {
                        // Ambil request terakhir
                        const lastReq = o.return_requests[o.return_requests.length - 1];
                        activeReturnReq = lastReq;

                        if (lastReq.status === 'pending') {
                            returnAction = `<span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded border border-yellow-200">⚠️ Return Pending</span>`;
                        } else if (lastReq.status === 'approved' && lastReq.type === 'return') {
                            // Nah ini dia, APPROVED & RETURN => Butuh Replacement
                            hasReplacementNeed = true;
                            returnAction = `<span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-200 blink-animation">📦 Send Replacement</span>`;
                        } else if (lastReq.status === 'approved' && lastReq.type === 'refund') {
                            returnAction = `<span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded border border-purple-200">💰 Refunded</span>`;
                        }
                    }

                    // Siapkan JSON untuk Modal
                    // Kita inject property tambahan 'needReplacement' ke object order biar gampang di Modal
                    o.needReplacement = hasReplacementNeed;
                    o.activeReturnReq = activeReturnReq;
                    const orderJson = encodeURIComponent(JSON.stringify(o));

                    return `
                        <tr class="border-b hover:bg-gray-50 transition duration-150">
                            <td class="p-4 text-center font-medium text-gray-500">${o.id}</td>
                            <td class="p-4 font-bold text-gray-800">${customerName}</td>
                            <td class="p-4 text-gray-600">${orderDate}</td>
                            <td class="p-4 font-medium text-gray-800">Rp ${total}</td>
                            <td class="p-4 text-center">${statusBadge}</td>
                            <td class="p-4 text-center">${shipBadge}</td>
                            <td class="p-4 text-center">${returnAction}</td>
                            <td class="p-4 text-right space-x-2">
                                <button onclick="openShipmentModal('${orderJson}')" class="text-blue-600 hover:underline font-bold text-sm">
                                    Shipment
                                </button>
                                <button onclick="deleteOrder(${o.id})" class="text-red-600 hover:underline ml-2 font-bold text-sm">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `;
                }).join("");
            } catch (error) {
                console.error("Error:", error);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center p-6 text-red-500 font-medium">Error loading data.</td></tr>`;
            }
        }

        // ========================================
        // 2. OPEN MODAL SHIPMENT (LOGIKA GABUNGAN)
        // ========================================
        window.openShipmentModal = (jsonString) => {
            const order = JSON.parse(decodeURIComponent(jsonString));
            
            form.reset();
            inpOrderId.value = order.id;
            
            // Hitung Ongkir Asli (Total - Subtotal Items)
            let subtotal = 0;
            if (order.items) order.items.forEach(i => subtotal += (Number(i.price) * Number(i.quantity)));
            const shippingCost = Number(order.total) - subtotal;
            inpCost.value = shippingCost > 0 ? `Rp ${shippingCost.toLocaleString('id-ID')}` : 'Free / Included';
            
            // Isi Alamat
            inpAddress.value = order.customer?.profile?.address || order.customer?.address || 'No address provided';

            // RESET LOGIKA
            returnAlert.classList.add('hidden');
            inpIsReturn.value = "0";
            inpReturnReqId.value = "";
            title.innerText = `Manage Shipment (Order #${order.id})`;

            // --- PERCABANGAN: REPLACEMENT vs NORMAL ---
            if (order.needReplacement) {
                // KASUS 1: KIRIM BARANG PENGGANTI (RETURN)
                returnAlert.classList.remove('hidden');
                title.innerText = `📦 Send Replacement (Order #${order.id})`;
                inpIsReturn.value = "1";
                inpReturnReqId.value = order.activeReturnReq.id;
                
                // Jika admin sudah pernah isi resi pengganti sebelumnya, tampilkan
                // (Note: admin_response biasanya berisi resi kalau replacement)
                inpTracking.value = order.activeReturnReq.admin_response || ''; 
                inpStatus.value = 'shipped'; // Default langsung shipped kalau replacement

            } else {
                // KASUS 2: PENGIRIMAN ORDER BIASA
                const shipment = order.shipment;
                if (shipment) {
                    inpShipmentId.value = shipment.id;
                    inpTracking.value = shipment.tracking_number || '';
                    inpStatus.value = shipment.status || 'pending';
                } else {
                    inpShipmentId.value = '';
                }
            }
            
            shipmentModal.classList.remove('hidden');
        }

        // ========================================
        // 3. SUBMIT FORM
        // ========================================
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById("save-shipment-btn");
            btn.disabled = true;
            btn.innerText = 'Saving...';

            const isReturn = inpIsReturn.value === "1";
            
            // Tentukan Endpoint
            // Jika Return -> Update ReturnRequestController (admin_response)
            // Jika Biasa -> Update ShipmentController (tabel shipments)
            let url = '';
            let body = {};
            let method = 'POST';

            if (isReturn) {
                // LOGIKA SIMPAN REPLACEMENT
                // Kita "numpang" update ke route update return request
                // Kirim status 'approved' (tetap) tapi update 'admin_response' dengan resi baru
                const reqId = inpReturnReqId.value;
                url = "{{ route('admin.data.returns.update', ':id') }}".replace(':id', reqId);
                method = 'PUT';
                body = {
                    status: 'approved', // Status tidak berubah
                    admin_response: inpTracking.value // Simpan Resi di sini
                };
            } else {
                // LOGIKA SIMPAN ORDER BIASA
                const orderId = inpOrderId.value;
                url = `{{ url('/admin/data/orders') }}/${orderId}/shipment`;
                body = {
                    tracking_number: inpTracking.value,
                    status: inpStatus.value
                };
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(body)
                });

                if (!response.ok) throw new Error('Failed to save data.');

                hideModal();
                loadOrders(); // Refresh tabel
                Swal.fire({ icon: 'success', title: 'Saved!', text: 'Shipment info updated.', timer: 1500, showConfirmButton: false });

            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message });
            } finally {
                btn.disabled = false;
                btn.innerText = 'Save Shipment';
            }
        });

        // ========================================
        // 4. DELETE ORDER
        // ========================================
        window.deleteOrder = async (id) => {
            const result = await Swal.fire({
                title: "Delete Order?", text: "This cannot be undone!", icon: "warning",
                showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: "Yes, delete"
            });

            if (result.isConfirmed) {
                try {
                    await fetch(`/admin/data/orders/${id}`, { method: "DELETE", headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                    Swal.fire("Deleted!", "Order removed.", "success");
                    loadOrders();
                } catch (error) {
                    Swal.fire("Error!", "Delete failed", "error");
                }
            }
        }

        loadOrders();
    });
</script>

<style>
    @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
    .blink-animation { animation: blink 2s infinite; }
</style>
@endsection