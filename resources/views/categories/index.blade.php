@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold my-4">Daftar Kategori</h1>

    <!-- Menampilkan pesan kesalahan validasi -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Ada kesalahan!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <a href="{{ route('categories.create') }}" class="bg-blue-500 text-white py-2 px-4 rounded mb-4 inline-block">Tambah Kategori</a>
    <table class="min-w-full bg-white rounded-lg shadow overflow-hidden">
        <thead class="bg-gray-800 text-white">
            <tr>
                <th class="py-3 px-4 text-left">Nama</th>
                <th class="py-3 px-4 text-left">Deskripsi</th>
                <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr class="border-b">
                    <td class="py-3 px-4">{{ $category->name ?? 'N/A' }}</td>
                    <td class="py-3 px-4">{{ $category->description ?? 'N/A' }}</td>
                    <td class="py-3 px-4 text-center">
                        <div class="inline-flex gap-2">
                            <a href="{{ route('categories.edit', $category->id) }}" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">Edit</a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kategori {{ $category->name }} ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
