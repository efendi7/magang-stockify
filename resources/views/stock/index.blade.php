@extends('layouts.app')

@section('title', 'Laporan Stok Barang')

@section('content')
<div class="container mx-auto px-4 min-h-screen bg-cover bg-center mt-16">
    <h1 class="text-3xl font-extrabold my-6 text-slate-600 text-center">Laporan Stok Barang</h1>

    {{-- Notifikasi Kesalahan --}}
    @if ($errors->any())
        <div class="bg-red-600 border border-red-400 text-white px-4 py-3 rounded-lg relative mb-6" role="alert">
            <strong class="font-bold">Ada kesalahan!</strong>
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('laporan.stok.filter') }}" class="mb-6 flex flex-wrap gap-4 bg-white p-4 rounded-lg shadow">
        <select name="category" class="border p-2 rounded-lg">
            <option value="">Semua Kategori</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}">
        <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}">

        <button type="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">Filter</button>
    </form>

    {{-- Tabel Stok Barang --}}
    <div class="overflow-x-auto rounded-lg shadow-lg bg-white bg-opacity-50">
        <table class="min-w-full bg-white bg-opacity-50 rounded-lg shadow overflow-hidden border border-gray-300">
            <thead class="bg-gray-800 bg-opacity-70 text-white">
                <tr>
                    <th class="py-3 px-4 text-left border border-gray-300">Produk</th>
                    <th class="py-3 px-4 text-left border border-gray-300">Kategori</th>
                    <th class="py-3 px-4 text-center border border-gray-300">Stok Awal</th>
                    <th class="py-3 px-4 text-center border border-gray-300">Barang Masuk</th>
                    <th class="py-3 px-4 text-center border border-gray-300">Barang Keluar</th>
                    <th class="py-3 px-4 text-center border border-gray-300 bg-blue-100 text-blue-700">Stock Opname Masuk</th>
                    <th class="py-3 px-4 text-center border border-gray-300 bg-red-100 text-red-700">Stock Opname Keluar</th>
                    <th class="py-3 px-4 text-center border border-gray-300 font-bold">Stok Akhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-300">
                @foreach ($stok as $item)
                    <tr class="hover:bg-gray-100 transition">
                        <td class="py-3 px-4 border border-gray-300">{{ $item->name }}</td>
                        <td class="py-3 px-4 border border-gray-300">{{ $item->category->name }}</td>
                        <td class="py-3 px-4 text-center border border-gray-300">{{ $item->stok_awal }}</td>
                        <td class="py-3 px-4 text-center border border-gray-300">{{ $item->barang_masuk ?? 0 }}</td>
                        <td class="py-3 px-4 text-center border border-gray-300">{{ $item->barang_keluar ?? 0 }}</td>
                        <td class="py-3 px-4 text-center border border-gray-300 bg-blue-50 text-blue-700">{{ $item->stock_opname_masuk ?? 0 }}</td>
                        <td class="py-3 px-4 text-center border border-gray-300 bg-red-50 text-red-700">{{ $item->stock_opname_keluar ?? 0 }}</td>
                        <td class="py-3 px-4 text-center font-semibold border border-gray-300">{{ $item->stok_akhir }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-6 flex justify-center">
        {{ $stok->appends(request()->input())->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
