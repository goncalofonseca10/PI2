<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Kit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $kitsDoUtilizador = Auth::user()->kits()->pluck('id');

            $query = Item::whereIn('kit_id', $kitsDoUtilizador);

            if ($request->has('kit_id')) {
                $query->where('kit_id', $request->kit_id);
            }

            if ($request->has('categoria_id')) {
                $query->where('categoria_id', $request->categoria_id);
            }

            if ($request->has('comprado')) {
                $query->where('comprado', $request->boolean('comprado'));
            }

            $items = $query->with('categoria')->orderBy('created_at', 'desc')->get();

            return response()->json(['status' => 1, 'data' => $items], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function create(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'quantidade' => 'nullable|integer|min:1',
            'kit_id' => 'required|integer|exists:kits,id',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        try {
            $kit = Kit::where('id', $request->kit_id)->where('user_id', Auth::id())->first();

            if (!$kit) {
                return response()->json(['status' => 0, 'error' => 'Kit não encontrado'], 404);
            }

            $item = new Item();
            $item->nome = $request->nome;
            $item->descricao = $request->descricao;
            $item->quantidade = $request->quantidade ?? 1;
            $item->comprado = $request->input('comprado', false);
            $item->kit_id = $request->kit_id;
            $item->categoria_id = $request->categoria_id;
            $item->save();

            $item->load('categoria');

            return response()->json(['status' => 1, 'data' => $item], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'quantidade' => 'nullable|integer|min:1',
            'comprado' => 'nullable|boolean',
            'categoria_id' => 'nullable|integer|exists:categorias,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        try {
            $kitsDoUtilizador = Auth::user()->kits()->pluck('id');
            $item = Item::whereIn('kit_id', $kitsDoUtilizador)->where('id', $id)->first();

            if (!$item) {
                return response()->json(['status' => 0, 'error' => 'Item não encontrado'], 404);
            }

            $item->nome = $request->nome;
            $item->descricao = $request->descricao;
            $item->quantidade = $request->quantidade ?? $item->quantidade;
            $item->comprado = $request->has('comprado') ? $request->boolean('comprado') : $item->comprado;
            $item->categoria_id = $request->categoria_id;
            $item->save();

            $item->load('categoria');

            return response()->json(['status' => 1, 'data' => $item], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function delete(string $id): \Illuminate\Http\JsonResponse
    {
        try {
            $kitsDoUtilizador = Auth::user()->kits()->pluck('id');
            $item = Item::whereIn('kit_id', $kitsDoUtilizador)->where('id', $id)->first();

            if (!$item) {
                return response()->json(['status' => 0, 'error' => 'Item não encontrado'], 404);
            }

            $item->delete();

            return response()->json(['status' => 1, 'message' => 'Item eliminado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }
}