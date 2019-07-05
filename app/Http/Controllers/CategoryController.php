<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        foreach ($categories as $category) {
            $category->view_category = [
                'href' => 'api/v1/categories/' . $category->id,
                'method' => 'GET'
            ];
        }

        $response = [
            'message' => 'List of all Categories',
            'categories' => $categories
        ];
        return response()->json($response, 200);
    }
}
