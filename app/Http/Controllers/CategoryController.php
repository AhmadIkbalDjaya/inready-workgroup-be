<?php

namespace App\Http\Controllers;

use App\Http\Resources\IdNameResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::orderBy("name", "asc")->get();
        return response()->json([
            "data" => IdNameResource::collection($categories),
        ], 200);
    }
}
