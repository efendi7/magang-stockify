<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    protected $productService;
    protected $userService;

    public function __construct(ProductService $productService, UserService $userService)
    {
        $this->productService = $productService;
        $this->userService = $userService;
    }

    public function index(Request $request)
    {   
        $query = Product::query()->with(['category', 'supplier']);
    
        // Filter berdasarkan pencarian (jika ada)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cat) use ($search) {
                      $cat->where('name', 'like', "%{$search}%");
                  });
            });
        }
    
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $status = $request->input('status');
    
            $query->where(function ($q) use ($status) {
                if ($status == 'Habis') {
                    $q->where('stock', 0);
                } elseif ($status == 'Warning') {
                    $q->whereColumn('stock', '<', 'minimum_stock')->where('stock', '>', 0);
                } elseif ($status == 'Tersedia') {
                    $q->whereColumn('stock', '>=', 'minimum_stock');
                }
            });
        }
    
        // Ambil data produk dengan pagination dan simpan filter dalam URL
        $products = $query->paginate(10)->appends(request()->query());
        $userRole = $this->userService->getUserRole(auth()->id());
    
        return view('products.index', compact('products', 'userRole'));
    }
    
    public function create()
    {
        $categories = $this->productService->getCategories();
        $suppliers = $this->productService->getSuppliers();
        $userRole = $this->userService->getUserRole(auth()->id());
        return view('products.create', compact('categories', 'suppliers', 'userRole'));
    }

    public function store(Request $request)
    {
        $userRole = $this->userService->getUserRole(auth()->id());
    
        if ($userRole !== 'admin') {
            return redirect()->route('products.index')->with('error', 'You do not have permission to add products.');
        }
    
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            // Proses upload gambar jika ada
            if ($request->hasFile('image')) {
                $validatedData['image'] = $request->file('image')->store('product_images', 'public');
            }
    
            // Set initial_stock sama dengan stock
            $validatedData['initial_stock'] = $validatedData['stock'];
    
            // Gunakan service untuk membuat produk
            $product = $this->productService->createProduct($validatedData);
            
            // Simpan log aktivitas
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'role' => auth()->user()->role, 
                'action' => "Menambahkan produk: {$product->name}",
                'properties' => json_encode([
                    'product_id' => $product->id,
                    'data' => $validatedData,
                ]),
            ]);
    
            return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage(), ['request' => $request->all()]);
            return redirect()->route('products.index')->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }
    
    public function edit(Product $product)
    {
        $categories = $this->productService->getCategories();
        $suppliers = $this->productService->getSuppliers();
        $userRole = $this->userService->getUserRole(auth()->id());
        return view('products.edit', compact('product', 'categories', 'suppliers', 'userRole'));
    }

    public function update(Request $request, Product $product)
    {
        $userRole = $this->userService->getUserRole(auth()->id());

        if ($userRole !== 'admin') {
            return redirect()->route('products.index')->with('error', 'You do not have permission to update products.');
        }

        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'purchase_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock' => 'required|integer',
            'minimum_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Simpan data lama sebelum update
            $oldData = $product->toArray();

            // Update produk
            $this->productService->updateProduct($product->id, $validatedData);

            // Simpan log aktivitas
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'role' => auth()->user()->role, 
                'action' => "Mengedit produk: {$product->name}",
                'properties' => json_encode([
                    'before' => $oldData,
                    'after' => $validatedData,
                ]),
            ]);

            return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            \Log::error('Error updating product: ' . $e->getMessage(), ['request' => $request->all(), 'product_id' => $product->id]);
            return redirect()->route('products.index')->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }

    public function destroy(StockTransaction $transaction)
    {
        // Simpan data transaksi sebelum dihapus untuk log
        $oldData = $transaction->toArray();

        // Jika transaksi sudah diterima, kembalikan stok produk
        if ($transaction->status === 'Diterima') {
            $product = $this->productService->getProductById($transaction->product_id);

            if ($product) {
                if ($transaction->type === 'Masuk') {
                    $product->stock = max(0, $product->stock - $transaction->quantity);
                } elseif ($transaction->type === 'Keluar') {
                    $product->stock += $transaction->quantity;
                }

                $this->productService->updateProductStock($product->id, $product->stock);
            }
        }

        $transaction->delete();
        return redirect()->route('stock_transactions.index')->with('success', 'Transaksi berhasil dihapus.');
    }
    
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        $userRole = $this->userService->getUserRole(auth()->id());
        return view('products.show', compact('product', 'userRole'));
    }

    public function export()
    {
        return $this->productService->exportProducts();
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        try {
            $this->productService->importProducts($request->file('file'));
            return back()->with('success', 'Produk berhasil diimpor!');
        } catch (\Exception $e) {
            \Log::error('Error importing products: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengimpor produk: ' . $e->getMessage());
        }
    }
}