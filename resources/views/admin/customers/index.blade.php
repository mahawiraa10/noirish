@extends('layouts.admin') {{-- Asumsi layout admin kamu --}}

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Customers</h1>
        {{-- Tombol "Add New" bisa kamu tambahin di sini nanti --}}
    </div>

    {{-- 
      Container Alpine.js
      1. 'customers': nyimpen data dari API
      2. 'isLoading': status loading
      3. 'fetchCustomers()': fungsi buat ngambil data
    --}}
    <div x-data="{ 
            customers: [], 
            isLoading: true,
            fetchCustomers() {
                fetch('{{ route('admin.data.customers.index') }}') {{-- Ambil data dari API --}}
                    .then(res => res.json())
                    .then(data => {
                        this.customers = data;
                        this.isLoading = false;
                    })
                    .catch(err => {
                        console.error('Gagal fetch customers:', err);
                        this.isLoading = false;
                    });
            }
         }"
         x-init="fetchCustomers()">

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3 px-6 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-6 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-6 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="py-3 px-6 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    
                    {{-- Tampilan Loading --}}
                    <tr x-show="isLoading">
                        <td colspan="4" class="py-4 px-6 text-center text-slate-500">
                            Loading data...
                        </td>
                    </tr>

                    {{-- Tampilan Data --}}
                    <template x-for="customer in customers" :key="customer.id">
                        <tr>
                            <td class="py-4 px-6 whitespace-nowrap text-sm font-medium text-slate-900" x-text="customer.name"></td>
                            <td class="py-4 px-6 whitespace-nowrap text-sm text-slate-600" x-text="customer.email"></td>
                            <td class="py-4 px-6 whitespace-nowrap text-sm text-slate-600" x-text="new Date(customer.created_at).toLocaleDateString('id-ID')"></td>
                            <td class="py-4 px-6 whitespace-nowrap text-sm font-medium">
                                {{-- ====================================================== --}}
                                {{-- !! INI LINK CRM-NYA !! --}}
                                {{-- ====================================================== --}}
                                <a :href="`/admin/customers/${customer.slug}`" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline">
                                    View Details
                                </a>
                                {{-- Tambahin tombol edit/delete di sini nanti --}}
                            </td>
                        </tr>
                    </template>

                    {{-- Tampilan Kalo Data Kosong --}}
                    <tr x-show="!isLoading && customers.length === 0">
                        <td colspan="4" class="py-4 px-6 text-center text-slate-500">
                            No customers found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection