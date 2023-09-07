<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function show(){
        $categories = Category::latest()->get();
        return response()->json([
            'categories' => $categories
        ], 200);
    }


    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = 1;

        $image = $request->file('image');
        if ($image){
            $name = uniqid();
            $ext = $image->getClientOriginalExtension();
            $image_name = $name.'.'.$ext;
            $upload_path = public_path('backend/upload/category/'.$image_name);
            Image::make($image)->resize(500,333)->save($upload_path);
            $category->image = $image_name;
        }

        $category->save();

        return response()->json([
            'message' => 'Category Successfully Added'
        ], 200);
    }


    public function edit($id)
    {
        $category = Category::find($id);
        return response()->json([
            'category' => $category
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:categories,name,'.$id,
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::find($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        $image = $request->file('image');
        if ($image){
            $name = uniqid();
            $ext = $image->getClientOriginalExtension();
            $image_name = $name.'.'.$ext;
            $upload_path = public_path('backend/upload/category/'.$image_name);
            Image::make($image)->resize(500,333)->save($upload_path);
            if ($category->image != '' && file_exists(public_path('backend/upload/category/'.$category->image))){
                unlink(public_path('backend/upload/category/'.$category->image));
            }
            $category->image = $image_name;
        }

        $category->save();

        return response()->json([
            'message' => 'Category Successfully Updated.'
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category->image != '' && file_exists(public_path('backend/upload/category/'.$category->image))){
            unlink(public_path('backend/upload/category/'.$category->image));
        }
        $category->delete();
        return response()->json([
            'message' => 'Category Successfully Deleted.'
        ], 200);

    }
}
