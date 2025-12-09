@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <a href="{{ route('admin.export.sales') }}" 
       class="px-4 py-2 bg-green-600 text-white rounded-lg shadow hover:bg-green-700 transition">
       Download Sales Report
    </a>
</div>

{{-- 1. SUMMARY BOXES (ATAS) --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    
    {{-- SALES BULANAN --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-slate-700">
        <p class="text-sm font-medium text-gray-500">Sales (This Month)</p>
        <p class="text-2xl font-bold text-gray-800 mt-1" id="monthly-sales">
            Rp {{ number_format($monthlySales ?? 0, 0, ',', '.') }}
        </p>
    </div>

    {{-- TOTAL PRODUCTS --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-green-500">
        <p class="text-sm font-medium text-gray-500">Total Products</p>
        <p class="text-2xl font-bold text-gray-800 mt-1" id="total-products">
            {{ $totalProducts ?? 0 }}
        </p>
    </div>

    {{-- ACTIVE CUSTOMERS --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-yellow-500">
        <p class="text-sm font-medium text-gray-500">Active Customers (This Month)</p>
        <p class="text-2xl font-bold text-gray-800 mt-1" id="active-customers">
            {{ $activeCustomers ?? 0 }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Users with > 1 purchase</p>
    </div>

    {{-- NEW CUSTOMERS --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-indigo-500">
        <p class="text-sm font-medium text-gray-500">New Customers (Today)</p>
        <p class="text-2xl font-bold text-gray-800 mt-1" id="new-customers">
            {{ $newCustomers ?? 0 }}
        </p>
    </div>
</div>

{{-- 2. INSIGHTS BOXES (TENGAH - DIGABUNG JADI SATU BARIS) --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    
    {{-- Top Product --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-indigo-500">
        <p class="text-sm font-medium text-gray-500">Top Product (This Month)</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 truncate" id="top-product">
            {{ $topProductName ?? 'Loading...' }}
        </p>
    </div>

    {{-- Top Category --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-purple-500">
        <p class="text-sm font-medium text-gray-500">Top Category (This Month)</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 truncate" id="top-category">
            {{ $topCategoryName ?? 'Loading...' }}
        </p>
    </div>

    {{-- Gender (DIPINDAHKAN KE SINI) --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-pink-500">
        <p class="text-sm font-medium text-gray-500">Dominant Gender</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 capitalize truncate" id="user-gender">
            {{ $userGender ?? 'Not set' }}
        </p>
    </div>

    {{-- City (DIPINDAHKAN KE SINI) --}}
    <div class="bg-white p-5 rounded-lg shadow border-l-4 border-blue-500">
        <p class="text-sm font-medium text-gray-500">Top City</p>
        <p class="text-2xl font-bold text-gray-800 mt-1 truncate" id="user-city">
            {{ $userCity ?? 'Not set' }}
        </p>
    </div>

</div>

{{-- 3. SALES CHART (BAWAH) --}}
<div class="bg-white p-6 rounded-lg shadow relative h-96">
    <h3 class="text-lg font-semibold mb-4">Sales Performance</h3>
    <canvas id="salesChart"></canvas>
</div>

{{-- 4. SCRIPT UPDATE REALTIME --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function renderChart(data) {
        const ctx = document.getElementById('salesChart')?.getContext('2d');
        if (!ctx) return;
        if (window.mySalesChart instanceof Chart) window.mySalesChart.destroy();

        window.mySalesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Monthly Sales (Rp)',
                    data: data.values,
                    backgroundColor: 'rgba(121, 113, 234, 0.2)',
                    borderColor: '#334155', 
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    async function loadDashboardData() {
        try {
            // Load summary data
            const summaryResponse = await fetch("{{ route('admin.dashboard.summary') }}");
            if (!summaryResponse.ok) throw new Error(`Summary fetch failed`);
            const summary = await summaryResponse.json();
            
            // Update Text Elements
            document.getElementById('monthly-sales').innerText = `Rp ${Number(summary.monthlySales || 0).toLocaleString('id-ID')}`;
            document.getElementById('active-customers').innerText = summary.activeCustomers || 0; 
            document.getElementById('total-products').innerText = summary.totalProducts || 0;
            document.getElementById('new-customers').innerText = summary.newCustomers || 0;

            document.getElementById('top-product').innerText = summary.topProductName || 'N/A';
            document.getElementById('top-category').innerText = summary.topCategoryName || 'N/A';
            
            // Update Gender & City (Ajax)
            document.getElementById('user-gender').innerText = summary.userGender || 'Not set';
            document.getElementById('user-city').innerText = summary.userCity || 'Not set';
            
            // Update Chart
            const chartResponse = await fetch("/api/sales/stats");
            const chartData = await chartResponse.json();
            if(chartData && chartData.labels) renderChart(chartData);

        } catch (error) {
            console.error("Dashboard data error:", error);
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        loadDashboardData();
    });
</script>
@endsection