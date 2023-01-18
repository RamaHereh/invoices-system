<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $sections = Section::all();
        $products = Product::all();
        return view ('products.products', compact('sections','products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|unique:products|max:255',
            'section_id' => 'required'
        ],[

            'product_name.required' =>'يرجى إدخال اسم المنتج',
            'product_name.unique' =>'اسم المنتج مسجل مسبقاً',
            'section_id.required' =>' يرجى اختيار القسم',

        ]);

        Product::create([
            'product_name' => $request->product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,
        ]);
        session()->flash('add', 'تم اضافة المنتج بنجاح ');
        return redirect('/products');
    }

    public function update(Request $request)
    {
        
       $product = Product::findOrFail($request->pro_id);
       $id_section = Section::where('section_name', $request->section_name)->first()->id;
       $product->update([
       'product_name' => $request->product_name,
       'description' => $request->description,
       'section_id' => $id_section
       ]);

       session()->flash('edit', 'تم تعديل المنتج بنجاح');
       return back();
    }

    public function destroy(Request $request)
    {
        Product::find($request->pro_id)->delete();
        session()->flash('delete', 'تم حذف المنتج بنجاح');
        return back();
    }
}
