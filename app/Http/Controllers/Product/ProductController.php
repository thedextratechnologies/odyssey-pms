<?php
namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Models\{Product, ProductAddon};
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function index() {
        $products = Product::with('addons')->orderBy('family')->orderBy('base_price')->get();
        $addons   = ProductAddon::whereNull('product_id')->get();
        return view('products.index', compact('products','addons'));
    }

    public function store(Request $request) {
        $v = $request->validate([
            'family'          => 'required|in:orbit,apex,nova',
            'variant'         => 'required|string|max:100',
            'description'     => 'nullable|string',
            'capacity_persons'=> 'required|integer|min:1',
            'door_type'       => 'nullable|string',
            'base_price'      => 'required|numeric|min:0',
        ]);
        Product::create($v);
        return back()->with('success', 'Product added.');
    }

    public function update(Request $request, Product $product) {
        $v = $request->validate([
            'variant'         => 'required|string|max:100',
            'description'     => 'nullable|string',
            'base_price'      => 'required|numeric|min:0',
            'is_active'       => 'boolean',
        ]);
        $product->update($v);
        return back()->with('success', 'Product updated.');
    }

    public function storeAddon(Request $request) {
        $v = $request->validate([
            'name'       => 'required|string|max:100',
            'category'   => 'required|string',
            'price'      => 'required|numeric|min:0',
            'unit'       => 'required|string',
            'product_id' => 'nullable|exists:products,id',
        ]);
        ProductAddon::create($v);
        return back()->with('success', 'Add-on created.');
    }

    public function getAddons(Request $request) {
        $addons = ProductAddon::where('is_active', true)
            ->where(fn($q) => $q->whereNull('product_id')->orWhere('product_id', $request->product_id))
            ->get();
        return response()->json($addons);
    }
}
