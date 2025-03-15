<?php 

namespace App\Http\Controllers;  

use Illuminate\Http\Request; 
use App\Services\StockOpnameService; 
use App\Models\Product;

class StockOpnameController extends Controller 
{     
    protected $stockOpnameService;

    public function __construct(StockOpnameService $stockOpnameService)
    {
        $this->stockOpnameService = $stockOpnameService;
    }

    public function index()
    {
        $products = Product::with('stockOpname')->paginate(10);
        return view('stock_opname.index', compact('products'));
    }

    public function store(Request $request)
    {
        $this->stockOpnameService->storeStockOpname($request->all());

        return redirect()->back()->with('success', 'Stock opname berhasil disimpan.');
    }

    public function updateStock(Request $request, $productId)
    {
        $success = $this->stockOpnameService->updateStockToActual($productId);

        if ($success) {
            return redirect()->back()->with('success', 'Stok berhasil diperbarui ke actual stock.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui stok.');
    }
}
