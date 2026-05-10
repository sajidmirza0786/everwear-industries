<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array|min:1',
            'attributes.*.size' => 'required|string|max:50',
            'attributes.*.mrp' => 'required|numeric|min:0',
            'attributes.*.selling_price' => 'required|numeric|min:0',
            'attributes.*.stock' => 'required|integer|min:0',
            'attributes.*.status' => 'required|in:enable,disable',
        ]);

        foreach ($request->input('attributes') as $attr) {
            ProductAttribute::create([
                'product_id' => $product->id,
                'size' => $attr['size'],
                'mrp' => $attr['mrp'],
                'selling_price' => $attr['selling_price'],
                'stock' => $attr['stock'],
                'status' => $attr['status'],
            ]);
        }

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Size added successfully.');
    }

    public function update(Request $request, Product $product, ProductAttribute $attribute)
    {
        $request->validate([
            'size' => 'required|string|max:50',
            'mrp' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:enable,disable',
        ]);

        $attribute->update($request->only('size', 'mrp', 'selling_price', 'stock', 'status'));

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Product $product, ProductAttribute $attribute)
    {
        $attribute->delete();

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'Attribute deleted successfully.');
    }
}
