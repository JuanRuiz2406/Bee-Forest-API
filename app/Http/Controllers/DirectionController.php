<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DirectionController extends Controller {

    public function __construct() { $this->middleware('api.auth'); }

    //GET ALL
    public function indexByClient( $id ) {

        $directions = DB::select('exec pa_readDirections ?', [$id]);

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'data'      => $directions
        ]);
    }

    //GET ONE
    public function show($id){

        $direction = DB::select('exec pa_selectDirection ?', [$id]);

        if (count($direction) > 0) {
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'message'   => 'Dirección encontrada correctamente.',
                'data'      => $direction
            ];
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Error, la direcion no existe.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //POST
    public function store( Request $request ){

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'clientId'  => 'required',
                'country'   => 'required',
                'province'  => 'required',
                'city'      => 'required',
                'direction' => 'required',
            ]);

            if ($validate->fails()) {

                $data = array(
                    'status'    => 'error',
                    'code'      => 404,
                    'message'   => 'Error, hay campos vacíos.',
                    'data'      => $validate->errors()
                );

            } else {

                $params_array[ 'created_at' ] = new \DateTime();
                $params_array[ 'updated_at' ] = new \DateTime();

                DB::insert( 'exec pa_addDirection ?,?,?,?,?,?,?,?', [
                    $params_array['clientId'],
                    $params_array['country'],
                    $params_array['province'],
                    $params_array['city'],
                    $params_array['zipCode'],
                    $params_array['direction'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Dirección registrada correctamente.',
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

        return response()->json( $data, $data['code'] );
    }

    //UPDATE
    public function update ($id, Request $request ) {

        $json = $request -> input( 'json', null );
        $params = json_decode( $json );
        $params_array = json_decode( $json, true );

        if ( !empty( $params_array ) ) {

            $validate = \Validator::make($params_array, [
                'clientId'  => 'required',
                'country'   => 'required',
                'province'  => 'required',
                'city'      => 'required',
                'direction' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'    => 400,
                    'status'  => 'error',
                    'message' => 'Error, hay campos vacíos.',
                    'data'    => $validate->errors()
                ];

            } else {

                unset($params_array['id']);
                unset($params_array['created_at']);
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateDirection ?,?,?,?,?,?,?,?', [
                    $id,
                    $params_array['clientId'],
                    $params_array['country'],
                    $params_array['province'],
                    $params_array['city'],
                    $params_array['zipCode'],
                    $params_array['direction'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'   => 200,
                    'status' => 'success',
                    'message'=> 'Dirección actualizada correctamente.',
                    'data'   => $params_array
                ];
            }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'No has ingresado ningún dato'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //DELETE
    public function destroy($id)
    {
        if (isset($id)) {

            $direction = DB::select('exec pa_selectDirection ?', [$id]);

                if (count($direction) > 0) {

                    $delete = DB::delete('exec pa_deleteDirection ?', [$id]);
                    $data = [
                        'code'      => 200,
                        'status'    => 'success',
                        'message'   => 'Dirección eliminada correctamente.'
                    ];
                } else {
                    $data = [
                        'code'      => 400,
                        'status'    => 'error',
                        'message'   => 'Dirección no encontrada.'
                    ];
                }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Dirección no encontrada.'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
