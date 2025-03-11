<?php

namespace App\Repositories;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\StockTransaction;
class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts(): LengthAwarePaginator
    {
        return Product::with('category', 'supplier')->paginate(10);
    }

    public function getProductById($id): ?Product
    {
        return Product::with('category', 'supplier')->find($id);
    }

    public function createProduct(array $data): Product
    {
        return Product::create($data);
    }

    public function updateProduct($id, array $data): bool
    {
        $product = Product::find($id);
        
        if (!$product) {
            return false;
        }
        
        return $product->update($data);
    }

    public function deleteProduct($id): bool
{
    $product = Product::find($id);

    if (!$product) {
        return false;
    }

    // Hapus semua transaksi yang terkait sebelum menghapus produk
    StockTransaction::where('product_id', $id)->delete();

    return $product->delete();
}

    public function getCategories()
    {
        return Category::select('id', 'name')->get();
    }

    public function getSuppliers()
    {
        return Supplier::select('id', 'name')->get();
    }
}
