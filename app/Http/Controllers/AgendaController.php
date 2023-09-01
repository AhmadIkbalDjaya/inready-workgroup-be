<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaginateSearchRequest;
use App\Http\Resources\AgendaDetailResource;
use App\Http\Resources\AgendaResource;
use App\Http\Resources\MetaSearchResource;
use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index(PaginateSearchRequest $request)
    {
        $page = $request->input("page", 1);
        $perpage = $request->input("perpage", 10);
        $search = $request->input("search", "");

        $agendas = Agenda::where("title", "LIKE", "%$search%")->paginate($perpage, ["*"], 'page', $page);
        return response()->json([
            "meta" => new MetaSearchResource($agendas),
            "data" => AgendaResource::collection($agendas),
        ], 200);
    }

    public function show(Agenda $agenda)
    {
        return response()->json([
            "data" => new AgendaDetailResource($agenda),
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "location" => "required|string",
            "time" => "required|date",
        ]);
        $validated["user_id"] = 1;
        try {
            $agenda = Agenda::create($validated);
            return response()->json(new AgendaDetailResource($agenda), 201);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, Agenda $agenda)
    {
        $validated = $request->validate([
            "title" => "required|string",
            "location" => "required|string",
            "time" => "required|date",
        ]);
        try {
            $agenda->update($validated);
            return response()->json(new AgendaDetailResource($agenda), 200);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => $th->getMessage(),
            ], 400);
        }
    }

    public function destroy(Agenda $agenda)
    {
        try {
            $agenda->delete();
            return response()->json([
                "success" => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false,
                "message" => $th->getMessage(),
            ], 400);
        }
    }
}
