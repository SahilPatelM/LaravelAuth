<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoriesController extends Controller
{
    //
    public function index()
    {
        return view('categories.index');
    }

    public function list()
    {
        return DataTables::of(Categorie::query())->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,bmp,webp',
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images/categories'), $imageName);
        } else {
            return response()->json(['success' => false, 'message' => 'Image is required'], 400);
        }

        // Create the category
        $category = Categorie::create([
            'name' => $request->name,
            'image' => $imageName,
            'vendor_id' => Auth()->user()->id,
        ]);

        return response()->json(['success' => true, 'category' => $category, 'message' => 'Category created successfully!']);
    }

    public function edit($id)
    {
        $category = Categorie::findOrFail($id);
        return response()->json($category);
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

    // In Destroy
    public function destroy(Request $request)
    {

        $category = Categorie::findOrFail($request->id);
        // Delete image file if exists
        if ($category->image) {
            $imagePath = public_path('images/categories/' . $category->image);
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }
        $category->delete();

        return response()->json(['success' => true, 'message' => 'Category deleted successfully!']);
    }
}
