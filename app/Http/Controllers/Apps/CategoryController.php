<?php

namespace App\Http\Controllers\Apps;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //
    public function index()
    {
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(10);

        // return inertia
        return inertia('Apps/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return Inertia::render('Apps/Categories/Create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image'         => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'          => 'required|unique:categories',
            'description'   => 'required'
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        // create category
        $category = Category::create([
            'image'         => $image->hashName(),
            'name'          => $request->name,
            'description'   => $request->description
        ]);

        // redirect
        return redirect()->route('apps.categories.index');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Apps/Categories/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, [
            'image'         => 'image|mimes:jpeg,jpg,png|max:2000',
            'name'          => 'required|unique:categories,name,' . $category->id,
            'description'   => 'required'
        ]);

        // check if request has image
        if ($request->hasFile('image')) {
            // upload image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            // delete old image
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            // update category
            $category->update([
                'image'         => $image->hashName(),
                'name'          => $request->name,
                'description'   => $request->description
            ]);
        } else {
            // update category
            $category->update([
                'name'          => $request->name,
                'description'   => $request->description
            ]);
        }

        // redirect
        return redirect()->route('apps.categories.index');
    }

    public function destroy($id)
    {
        // find by id
        $category = Category::findOrFail($id);

        // remove image
        Storage::disk('local')->delete('public/categories/' . basename($category->image));

        // delete
        $category->delete();

        // redirect
        return redirect()->route('apps.categories.index');
    }
}
