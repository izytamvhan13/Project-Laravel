<?php

namespace App\Http\Controllers;

// import model product
use App\Models\Product;

// import return type view
use Illuminate\View\View;

//import return type redirectResponse
use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;
use Storage;

class ProductController extends Controller
{
    /**
     * create 
     * 
     * @return View
     */
    public function index(): View
    {
        // get all product
        $products = Product::latest()->paginate(10);

        // render view with products
        return view('products.index', compact('products'));
    }

    /**
     * create
     * 
     * @return View
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * store
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // validate form
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        // upload image
        $image = $request->file('image');
        $image->storeAs('products', $image->hashName(), 'public');


        // create product
        Product::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock
        ]);

        // redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function show(string $id): View
    {
        //get product by ID 
        $product = Product::findOrFail($id);

        //render view with product
        return view('products.show', compact('product'));
    }

    public function edit(string $id): view
    {
        //get product by ID
        $product = product::findOrFail($id);

        //render view with product
        return view('products.edit', compact('product'));
    }

    /**
     * update
     * 
     * @param mixed $request
     * @param mixed $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        // validate form
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'description' => 'required|min:10',
            'price' => 'required|numeric',
            'stock' => 'required|numeric'
        ]);

        //get product by ID
        $product = Product::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //uploade new image
            $image = $request->file('image');
            $image->storeAs('products', $image->hashName(), 'public');

            //delete old image
            Storage::delete('public/products/' . $product->image);

            //update product with new image
            $product->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

        } else {

            //update product without image
            $product->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);
        }

        //redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    public function destroy($id): RedirectResponse
    {
        //get product by ID
        $product = product::findOrFail($id);

        //delete image
        Storage::delete('public/products/' . $product->image);

        //dlete product 
        $product->delete();

        //redirect to index
        return redirect()->route('products.index')->with(['success' => 'Data Berhasil Dihapus']);
    }


}