<?php 

namespace App\Http\Controllers;  

use Illuminate\Http\Request; 
use App\Models\Product; 
use App\Models\StockOpname;   
use App\Models\ActivityLog;

class StockOpnameController extends Controller 
{     
    public function index()     
    {         
        $products = Product::with('stockOpname')->get();
        return view('stock_opname.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'recorded_stock' => 'required|integer|min:0',
            'actual_stock' => 'required|integer|min:0',
            'difference' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);
    
        $stockOpname = StockOpname::updateOrCreate(
            ['product_id' => $product->id],
            [
                'recorded_stock' => $request->recorded_stock,
                'actual_stock' => $request->actual_stock,
                'difference' => $request->difference,
                'updated_at' => now(),
            ]
        );

        // Simpan log aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'role' => auth()->user()->role, 
            'action' => "Melakukan stock opname untuk produk: {$product->name}",
            'properties' => json_encode([
                'product_name' => $product->name,
                'recorded_stock' => $request->recorded_stock,
                'actual_stock' => $request->actual_stock,
                'difference' => $request->difference,
            ]),
        ]);
    
        return redirect()->back()->with('success', 'Stock opname berhasil disimpan.');
    }

   
}
