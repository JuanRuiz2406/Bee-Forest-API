<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use App\Helpers\JwtAuth;

class OrderController extends Controller
{
    public function __construct(){ $this->middleware('api.auth'); }

    //GET ALL
    public function index(){

        $orders = DB::select('exec pa_readOrders');

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'data'      => $orders
        ]);
    }

    //GET ONE
    public function show($id)
    {
        $order = DB::select('exec pa_selectOrder ?', [$id]);

        if (count($order) > 0) {
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'message'   => 'Pedido encontrado correctamente.',
                'data'      => $order
            ];
        } else {
            $data = [
                'code'      => 404,
                'status'    => 'error',
                'message'   => 'El pedido no existe.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //POST
    public function store(Request $request){

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'collaboratorId'    => 'required',
                'clientId'          => 'required',
                'ShippingId'        => 'required',
                'creationDate'      => 'required',
                'deliveryDate'      => 'required',
                'discount'          => 'required',
                'totalPrice'        => 'required',
                'status'            => 'required',

            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, hay campos vacíos.',
                    'error'     => $validate->errors()
                ];
            } else {
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addOrder ?,?,?,?,?,?,?,?,?,?', [
                    $params_array['collaboratorId'],
                    $params_array['clientId'],
                    $params_array['ShippingId'],
                    $params_array['creationDate'],
                    $params_array['deliveryDate'],
                    $params_array['discount'],
                    $params_array['totalPrice'],
                    $params_array['status'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Pedido guardado exitosamente.',
                    'data'      => $params_array
                ];
            }

        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'No has ingresado ningún dato.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //UPDATE
    public function update($id, Request $request){

        $json = $request->input('json', null);
        $params_array = json_decode($json, true);
        $params = json_decode($json);

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'id'                => 'id',
                'collaboratorId'    => 'required',
                'clientId'          => 'required',
                'ShippingId'        => 'required',
                'creationDate'      => 'required',
                'discount'          => 'required',
                'totalPrice'        => 'required',
                'status'            => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, hay campos vacíos.',
                    'error'     => $validate->errors()
                ];
            } else {

                unset($params_array['deliveryDate']);
                unset($params_array['creationDate']);
                unset($params_array['created_at']);

                $params_array['updated_at'] = new \DateTime();
                DB::update('exec pa_updateOrder ?,?,?,?,?,?,?,?', [
                    $params_array['id'],
                    $params_array['collaboratorId'],
                    $params_array['clientId'],
                    $params_array['ShippingId'],
                    $params_array['discount'],
                    $params_array['totalPrice'],
                    $params_array['status'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Pedido actualizado correctamente.',
                    'data'      => $params_array
                ];
            }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'No has ingresado ningún dato.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //DELETE
    public function destroy($id) {
        if (isset($id)) {

            $orden = DB::select('exec pa_selectOrder ?', [$id]);

            if (count($orden) > 0) {

                $delete = DB::delete('exec pa_deleteOrder ?', [$id]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Pedido eliminado correctamente.',
                    'data'      => $delete,
                ];
            } else {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Pedido no encontrado.'
                ];
            }
        } else {

            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Pedido no encontrado.'
            ];

        }

        return response()->json($data, $data['code']);
    }
}
