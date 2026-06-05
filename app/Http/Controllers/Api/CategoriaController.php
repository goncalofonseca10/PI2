<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse {
        try{
            $categorias = Auth::user()->categorias()->orderBy('created_at', 'desc')->get();
            return response()->json(['status' => 1, 'data' => $categorias], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        if($validator -> fails()){
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        try{
            $categoria = new Categoria();
            $categoria->nome = $request->nome;
            $categoria->descricao = $request->descricao;
            $categoria->user_id = Auth::id();
            $categoria->save();

            return response()->json(['status' => 1, 'data' => $categoria], 201);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        if($validator -> fails()){
            return response()->json(['status' => 0, 'errros' => $validator->errors()], 422);
        }
        try{
            $categoria = Categoria::where('id', $id)->where('user_id', Auth::id())->first();

            if(!$categoria){
                return response()->json(['status' => 0, 'error' => 'Categoria não encontrada'], 404);
            }

            $categoria->nome = $request->nome;
            $categoria->descricao = $request->descricao;
            $categoria->save();

            return response()->json(['status' => 1, 'data' => $categoria], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse {
        try{
            $categoria = Categoria::where('id', $id)->where('user_id', Auth::id())->first();

            if(!$categoria){
                return response()->json(['status' => 0, 'error' => 'Categoria não encontrada'], 404);
            }
            $categoria->delete();
            return response()->json(['status' => 1, 'message' => 'Categoria removida com sucesso'], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }
}
