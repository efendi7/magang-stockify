@extends('layouts.app')

@section('content')
<body class="min-h-screen flex items-center justify-center bg-cover bg-center">
    <div class="bg-white bg-opacity-70 p-6 rounded-2xl shadow-xl w-full max-w-md animate-fadeIn">
        <h1 class="text-xl font-bold text-gray-800 text-center mb-6">Tambah Atribut Produk</h1>
        
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('product_attributes.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="product_id" class="block text-sm font-medium text-gray-800">Pilih Produk</label>
                <select name="product_id" id="product_id" 
                    class="w-full mt-1 p-2 rounded-lg border border-gray-300 text-gray-800 focus:ring-2 focus:ring-blue-500 transition-all" required>
                    <option value="">Pilih Produk</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="attribute_name" class="block text-sm font-medium text-gray-800">Nama Atribut</label>
                <input type="text" name="attribute_name" id="attribute_name"
                    class="w-full mt-1 p-2 rounded-lg border border-gray-300 text-gray-800 focus:ring-2 focus:ring-blue-500 transition-all" 
                    value="{{ old('attribute_name') }}" required>
            </div>
            
            <div>
                <label for="attribute_value" class="block text-sm font-medium text-gray-800">Nilai Atribut</label>
                <input type="text" name="attribute_value" id="attribute_value"
                    class="w-full mt-1 p-2 rounded-lg border border-gray-300 text-gray-800 focus:ring-2 focus:ring-blue-500 transition-all" 
                    value="{{ old('attribute_value') }}" required>
            </div>
            
            <button type="submit" 
                class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition-all transform hover:scale-105">Simpan</button>
        </form>
    </div>
    
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</body>

