<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginateSearchRequest;
use App\Http\Resources\ArticleDetailResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\MetaPaginateResource;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(PaginateSearchRequest $request)
    {
        $page = $request->input("page", 1);
        $perpage = $request->input("perpage", 10);
        $search = $request->input("search", "");

        $articles = Article::where("title", "LIKE", "%$search%")->paginate($perpage, ["*"], 'page', $page);
        return response()->json([
            "meta" => new MetaPaginateResource($articles),
            "data" => ArticleResource::collection($articles),
        ], 200);
    }

    public function show(Article $article)
    {
        return response()->json([
            "data" => new ArticleDetailResource($article)
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "category_id" => "required|exists:categories,id",
            "image" => "required|image",
            "content" => "required|string",
        ]);
        $validated["image"] = $request->file("image")->storePublicly("article", "public");
        $validated["user_id"] = 1;
        try {
            $article = Article::create($validated);
            return response()->json(new ArticleDetailResource($article), 201);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "category_id" => "required|exists:categories,id",
            "image" => "nullable|image",
            "content" => "required|string",
        ]);
        if ($request->file("image")) {
            Storage::delete($article->image);
            $validated["image"] = $request->file("image")->storePublicly("article", "public");
        } else {
            unset($validated["image"]);
        }
        try {
            $article->update($validated);
            return response()->json(new ArticleDetailResource($article), 200);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 400);
        }
    }

    public function destroy(Article $article)
    {
        try {
            if (Storage::exists($article->image)) {
                Storage::delete($article->image);
            }
            $article->delete();
            return response()->json([
                "success" => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage()
            ], 400);
        }
    }
}
