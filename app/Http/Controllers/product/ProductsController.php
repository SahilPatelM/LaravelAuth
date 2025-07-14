<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductsController extends Controller
{
    //
    public function index()
    {
        $category = Categorie::all();
        return view('products.index', compact('category'));
    }

    public function list()
    {
        $query = Product::with(['category', 'images']);

        return DataTables::of($query)
            ->addColumn('category', function ($product) {
                return $product->category ? $product->category->name : '—';
            })
            ->addColumn('thumbnail', function ($product) {
                $thumb = $product->images->where('tag', 'thumbnail')->first();
                if ($thumb) {
                    return '<img src="' . asset('images/products/' . $thumb->image_path) . '" width="50" height="50" style="object-fit:cover; border-radius: 6px;">';
                }
                return '—';
            })
            ->rawColumns(['thumbnail', 'category']) // to allow HTML rendering
            ->make(true);
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:200',
            'category_id' => 'required',
            'price' => 'required|numeric|min:0',
            'thumbnail_image' => 'required|image|mimes:jpg,jpeg,png,gif,bmp,webp',
            'images' => 'array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif,bmp,webp'
        ]);

        // Create the product
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'in_stock' => 1,
            'vendor_id' => Auth()->user()->id,
        ]);

        if ($request->hasFile('thumbnail_image')) {
            $imageName = time() . '.' . $request->thumbnail_image->extension();
            $request->thumbnail_image->move(public_path('images/products'), $imageName);
            ProductImage::create([
                'image_path' => $imageName,
                'product_id' => $product->id,
                'tag' => 'thumbnail'
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images/products'), $imageName);

                ProductImage::create([
                    'image_path' => $imageName,
                    'product_id' => $product->id,
                    'tag' => 'gallery'
                ]);
            }
        }

        return response()->json(['success' => true, 'product' => $product, 'message' => 'Product created successfully!']);
    }

    public function edit($id)
    {
        $product = Product::with(['category', 'images'])->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,bmp,webp',
        ]);

        $category = Categorie::findOrFail($id);
        $category->name = $request->name;

        if ($request->hasFile('image')) {
            // Delete old image file if exists
            if ($category->image) {
                $oldImagePath = public_path('images/categories/' . $category->image);
                if (file_exists($oldImagePath)) {
                    @unlink($oldImagePath);
                }
            }

            // Store new image
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
            $category->image = $imageName;
        }

        $category->save();

        return response()->json(['success' => true, 'category' => $category, 'message' => 'Category updated successfully!']);
    }

    public function removeImage($id)
    {
        $image = ProductImage::findOrFail($id);
        $path = public_path('images/products/' . $image->image_path);

        if (file_exists($path)) {
            @unlink($path);
        }

        $image->delete();

        return response()->json(['message' => 'Image deleted successfully.']);
    }

    // In Destroy
    public function destroy(Request $request)
    {

        $product = Product::findOrFail($request->id);
        $productImages = ProductImage::where('product_id', $product->id)->get();
        // Delete image file if exists
        if (!empty($productImages)) {
            foreach ($productImages as $image) {
                $imagePath = public_path('images/products/' . $image->image_path);
                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }
            }
        }
        ProductImage::where('product_id', $product->id)->delete();
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted successfully!']);
    }
}