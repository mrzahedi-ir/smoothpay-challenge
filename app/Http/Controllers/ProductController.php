<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        foreach ($products as $product) {
            $product->view_product = [
                'href' => 'api/v1/categories/' . $product->id,
                'method' => 'GET'
            ];
            foreach ($product->categories as $category) {
                $product->category .= $category->name . ', ';
            }
        }

        $response = [
            'message' => 'List of all Products',
            'products' => $products
        ];
        return response()->json($response, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'  => 'required',
            'SKU'   => 'required',
            'price' => 'required',
            'categories' => 'required',
        ]);

        $name = $request->input('name');
        $SKU = $request->input('SKU');
        $price = $request->input('price');
        $categories = $request->input('categories');

        $product = new Product([
            'name'  => $name,
            'SKU'   => $SKU,
            'price' => $price,
        ]);
        if ($product->save()) {
            $product->categories()->sync($categories);
            $product->view_product = [
                'href' => 'api/v1/products/' . $product->id,
                'method' => 'GET'
            ];
            $message = [
                'message' => 'Product created',
                'product' => $product
            ];
            return response()->json($message, 201);
        }

        $response = [
            'msg' => 'Error during creation'
        ];
        return response()->json($response, 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::where('id', $id)->firstOrFail();
        foreach ($product->categories as $category) {
            $product->category .= $category->name . ', ';
        }

        $response = [
            'message' => 'Product details',
            'product' => $product
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'  => 'required',
            'SKU'   => 'required',
            'price' => 'required',
            'categories' => 'required',
        ]);

        $name = $request->input('name');
        $SKU = $request->input('SKU');
        $price = $request->input('price');
        $categories = $request->input('categories');

        $product = Product::findOrFail($id);

        $product->name = $name;
        $product->SKU = $SKU;
        $product->price = $price;

        if (!$product->update()) {
            return response()->json(['message' => 'Error during updating'], 404);
        }

        $product->categories()->sync($categories);
        $product->view_product = [
            'href' => 'api/v1/products/' . $product->id,
            'method' => 'GET'
        ];

        $response = [
            'message' => 'Product updated',
            'product' => $product
        ];

        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        $response = [
            'message' => 'Product deleted',
        ];

        return response()->json($response, 200);
    }
}
