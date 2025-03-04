<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use App\Services\ProductAttributeService;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    protected $productAttributeService;

    public function __construct(ProductAttributeService $productAttributeService)
    {
        $this->productAttributeService = $productAttributeService;
    }

    public function index()
    {
        $attributes = $this->productAttributeService->getAllProductAttributes();
        return view('product_attributes.index', ['productAttributes' => $attributes]);
    }

    public function create()
    {
        $products = $this->productAttributeService->getAllProducts();
        return view('product_attributes.create', compact('products'));
    }

    public function store(Request $request)
    {
        \Log::info('Store method hit', ['request' => $request->all()]); // Cek apakah method terpanggil
    
        $validatedData = $request->validate([
            'attribute_name' => 'required|string|max:255',
            'attribute_value' => 'required|string|max:255',
            'product_id' => 'required|exists:products,id', // Pastikan product_id ada di tabel products
        ]);
    
        $this->productAttributeService->createProductAttribute($validatedData);
    
        return redirect()->route('product_attributes.index')->with('success', 'Product attribute successfully added!');
    }

    public function edit(ProductAttribute $productAttribute)
    {
        return view('product_attributes.edit', compact('productAttribute'));
    }

    public function update(Request $request, ProductAttribute $productAttribute)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'required|string|max:255',
        ]);

        $this->productAttributeService->updateProductAttribute($productAttribute->id, $request->all());

        return redirect()->route('product_attributes.index')->with('success', 'Product attribute successfully updated!');
    }

    public function destroy(ProductAttribute $productAttribute)
    {
        $this->productAttributeService->deleteProductAttribute($productAttribute->id);
        return redirect()->route('product_attributes.index')->with('success', 'Product attribute successfully deleted!');
    }
}
