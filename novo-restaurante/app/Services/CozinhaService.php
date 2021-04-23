<?php
namespace App\Services;

use App\Repositories\CozinhaRepositoryInterface;
use App\Models\Cozinha;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use DB;

class CozinhaService
{

    private $cozinhaRepository;

    public function __construct(CozinhaRepositoryInterface $cozinhaRepository)
    {
        $this->cozinhaRepository = $cozinhaRepository;
    }

    public function listarTodas()
    {
        $cozinhas = $this->cozinhaRepository->listarTodas();
        if (count($cozinhas) < 1) {
            return response()->json("Nenhuma cozinha registrada", Response::HTTP_OK);
        } else {
        $cozinhas =DB::table('cozinhas')->paginate(3);
            return response()->json($cozinhas, Response::HTTP_OK);
        }
    }

    public function buscarIndividual($id)
    {
        $cozinha = $this->cozinhaRepository->buscarIndividual($id);
        if (Cozinha::where('id', '=', $id)->count() > 0) {
            return response()->json($cozinha, Response::HTTP_OK);
        } else {
            return response()->json("Cozinha nao encontrada", Response::HTTP_OK);
        }
    }

    public function gravar(Request $request)
    {
        if (Cozinha::where('tipo', $request->input('tipo'))->first()) {
            return response()->json("Impossibilidade de criar cozinha, pois ja foi registrada", Response::HTTP_OK);
        } else {
            $validator = Validator::make($request->all(), [
                'tipo' => 'required|max:20',
                'pratoprincipal' => 'required|max:70'
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            } else {
                $cozinha = $this->cozinhaRepository->gravar($request);
                return response()->json("Cozinha criada com sucesso!", Response::HTTP_CREATED);
            }
        }
    }

    public function atualizar($id, Request $request)
    {
        if (Cozinha::where('id', '=', $id)->count() > 0) {

            $cozinha = $this->cozinhaRepository->atualizar($id, $request);
            return response()->json('Cozinha atualizada', Response::HTTP_OK);
        } else {
            return response()->json('cozinha inexistente', Response::HTTP_OK);
        }
    }

    public function apagar($id)
    {
        $cozinha = $this->cozinhaRepository->buscarIndividual($id);
        if (Cozinha::where('id', '=', $id)->count() > 0) {
            $this->cozinhaRepository->apagar($id);
            return response()->json("Cozinha apagada com sucesso", Response::HTTP_OK);
        } else {
            return response()->json("Cozinha nao encontrada", Response::HTTP_OK);
        }
    }
}
