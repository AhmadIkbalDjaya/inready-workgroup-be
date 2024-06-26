<?php

namespace App\Http\Controllers;

use App\Models\Work;
use Illuminate\Http\Request;
use App\Http\Requests\PaginateSearchRequest;
use App\Http\Resources\MetaPaginateResource;
use App\Http\Resources\WorkDetailResource;
use App\Http\Resources\WorkResource;
use Illuminate\Support\Facades\Storage;

class WorkController extends Controller
{
    public function index(PaginateSearchRequest $request)
    {
        $page = $request->input('page', 1);
        $perpage = $request->input('perpage', 10);
        $search = $request->input('search', "");

        $works = Work::where("title", "LIKE", "%$search%")->latest()->paginate($perpage, ["*"], 'page', $page);
        return response()->base_response_with_meta(
            WorkResource::collection($works),
            new MetaPaginateResource($works),
        200);
    }

    public function show(Work $work)
    {
        return response()->base_response(new WorkDetailResource($work), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "member_id" => "required|exists:members,id",
            "image" => "nullable|image",
            "link" => "nullable|string",
            "description" => "nullable|string",
            "is_active" => "nullable|boolean",
        ]);
        if ($request->file("image")) {
            $validated["image"] = $request->file("image")->storePublicly("work", "public");
        }
        try {
            $work = Work::create($validated);
            return response()->base_response(new WorkDetailResource($work), 201, "Created", "Karya Berhasil Ditambahkan");
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "member_id" => "required|exists:members,id",
            "image" => "nullable",
            "link" => "nullable|string",
            "description" => "nullable|string",
            "is_active" => "nullable|boolean",
        ]);
        if ($request->file("image")) {
            if ($work->image && Storage::exists($work->image)) {
                Storage::delete($work->image);
            }
            $validated["image"] = $request->file("image")->storePublicly("work", "public");
        } else {
            unset($validated["image"]);
        }
        try {
            $work->update($validated);
            return response()->base_response(new WorkDetailResource($work), 200, "OK", "Karya Berhasil Diedit");
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    public function destroy(Work $work)
    {
        try {
            if ($work->image && Storage::exists($work->image)) {
                Storage::delete($work->image);
            }
            $work->delete();
            return response()->base_response([], 200, "OK", "Karya Berhasil Dihapus");
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }
}
