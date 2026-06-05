<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class KitController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse {
        try{
            $kits = Auth::user()->kits()->orderBy('creeated_at', 'desc')->get();
            return response()->json(['statur' => 1, 'kits' => $kits], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' -> $e->getMessage()], 500);
        }
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse {
        $validator = Validator::make($request->all(),  [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        if($validator -> fails()){
            return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
        }

        try{
            $kit = new Kit();
            $kit->nome = $request->nome;
            $kit->descricao = $request->descricao;
            $kit->user_id = Auth::id();
            $kit->save();
            return response()->json(['status' => 1, 'data' => $kit], 201);    
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id): \Illuminate\Http\JsonResponse{
        try{
            $kit = Kit::where('id', $id)->where('user_id', Auth::id())->firts();

            if(!kit){
                return response()->json(['status' => 0, 'error' => 'Kit não encontrado']);
            }

            $kit->load('items.categoria');
            
            return response()->json(['status' => 1, 'data' => $kit], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request,string $id): \Illuminate\Http\JsonResponse{
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|',
        ]);

        if($validator->fails()){
            return response()->json(['status'=> 0, 'errors' => $validator->errors()], 422);
        }
        try{
            $kit = Kit::where('id', $id)->where('user_id', Auth::id())->first();

            if($kit){
                return response()->json(['status'=> 0, 'error' => 'kit não encontrado'], 404);
            }
            $kit->nome = $request->nome;
            $kit->descricao = $request->descricao;
            $kit->save();
            return response()->json(['status' => 1, 'data' => $kit], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): \Illuminate\Http\JsonResponse{
        try{
            $kit = Kit::where('id', $id)->where('user_id', Auth::id())->first();

            if(!$kit){
                return response()->json(['status' => 0, 'error' => 'kit não encontrado'], 404);
            }

            $kit->delete();
            return response()->json(['status'=>1, 'messsage' => 'kit removido com sucesso'], 200);
        }catch(\Exception $e){
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
        }
    }
}
