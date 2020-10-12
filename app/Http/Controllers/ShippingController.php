<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Uuid;

class ShippingController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth');
    }

    public function index()
    {

        $shipping = DB::select('exec pa_readShippings');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $shipping
        ]);
    }

    public function store(Request $request)
    {

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'price' => 'required',
                'description' => 'required',
            ]);

            if ($validate->fails()) {

                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El tipo de envio no se ha guardado',
                    'data' => $validate->errors()
                );
            } else {

                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addShipping ?,?,?,?,?', [
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['description'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Tipo de envio guardado.',
                    'data' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function show($shippingId)
    {
        $shipping = DB::select('exec pa_selectShipping ?', $shippingId);
        if (is_object($shipping)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Tipo de envio encontrado correctamente',
                'data' => $shipping
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Tipo de envio no encontrado o id de producto no existe'
            ];
        }


        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request)
    {
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'name' => 'required',
                'price' => 'required',
                'description' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha actualizado el tipo de envio, faltan datos',
                    'data' => $validate->errors()
                ];
            } else {

                $unique = DB::select('exec pa_selectShippingByName ?', [$params->name]);

                if ((count($unique) > 0) && (strtoupper($id) != $unique[0]->id)) {

                    $data = array(
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'El el nombre del tipo de envio ya existe'
                    );

                    return response()->json($data, $data['code']);
                }

                unset($params_array['id']);
                unset($params_array['created_at']);
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateShipping ?,?,?,?,?', [
                    $id,
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['description'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Tipo de envio actualizado',
                    'data' => $params_array
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        if (isset($id)) {

            $shipping = DB::select('exec pa_selectShipping ?', [$id]);

            if (count($shipping) > 0) {

                $delete = DB::delete('exec pa_deleteShipping ?', [$id]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Se elimino correctamente',
                    'data'  => $delete,
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se elimino correctamente'
                ];
            }
        } else {

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se encontro el tipo de envio'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
