<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $perPage = request('per_page', 10);
        $products = Product::with('category')
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('admin/produk/Index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('admin/products/Create', [
            'categories' => $categories,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : (object) [],
        ]);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $data = $request->all();

        // Generate slug
        $data['slug'] = Str::slug($request->name);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = '/storage/' . $path;
        }

        // Convert checkbox values to boolean
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_active'] = $request->boolean('is_active');

        Product::create($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('admin/products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'errors' => session('errors') ? session('errors')->getBag('default')->getMessages() : (object) [],
        ]);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->all();

        $validated = Validator::make($data, [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048', // 2MB Max
        ]);

        if ($validated->fails()) {
            $errorMessages = implode(', ', $validated->errors()->all());

            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Validasi gagal! ' . $errorMessages)
                ->withInput();
        }

        $validatedData = $validated->validated();

        // Generate slug
        $validatedData['slug'] = Str::slug($validatedData['name']);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
            }

            $path = $request->file('image')->store('products', 'public');
            $validatedData['image'] = '/storage/' . $path;
        }

        // Convert checkbox values to boolean
        $validatedData['is_featured'] = $request->boolean('is_featured');
        $validatedData['is_active'] = $request->boolean('is_active');

        $product->fill($validatedData);

        $product->save();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete product image if it exists
        if ($product->image && Storage::disk('public')->exists(str_replace('/storage/', '', $product->image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $product->image));
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
