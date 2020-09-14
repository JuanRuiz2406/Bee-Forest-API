<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use App\Helpers\JwtAuth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        $products = DB::select('select * from v_ListaProductosMielCremada');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {
        // Recoger datos por Promotion
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        //var_dump($params); die();

        if (!empty($params_array)) {
            // Conseguir usuario identificado
            $collaborator = $this->getIdentity($request);

            // Validar los datos
            $validate = \Validator::make($params_array, [
                'categoryId' => 'required',
                'name' => 'required|unique:products',
                'price' => 'required',
                'amount' => 'required',
                'image' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el producto, faltan datos',
                    'data' => $validate->errors()
                ];
            } else {

                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::select('exec pa_saveProduct ?,?,?,?,?,?,?,?', [
                    $params_array['categoryId'],
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['amount'],
                    $params_array['description'],
                    $params_array['image'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'Envia los datos correctamente'
            ];
        }

        // Devolver respuesta
        return response()->json($data, $data['code']);
    }

    private function getIdentity($request)
    {
        $jwtAuth = new JwtAuth();
        $token = $request->header('Authorization', null);
        $client = $jwtAuth->checkToken($token, true);

        return $client;
    }
}
