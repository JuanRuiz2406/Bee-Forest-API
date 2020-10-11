<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use Uuid; //Generamos ID unico para cada registro

class ProviderController extends Controller {


    public function __construct(){ $this->middleware('api.auth'); }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $providers = DB::select('exec pa_readProviders');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $providers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {
          
              // Validar datos
              $validate = \Validator::make($params_array, [
                'name'      => 'required',
                'surname'   => 'required',
                'telephone' => 'required',
                'direction' => 'required',
                'email'     => 'required|email|unique:providers',
                'startDay' => 'required',
                'finalDay' => 'required',
                'StartTime' => 'required',
                'finalTime' => 'required',
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El proveedor no se ha guardado',
                    'data' => $validate->errors()
                );

            } else {

                $params_array['id'] = Uuid::generate()->string;
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addProvider ?,?,?,?,?,?,?,?,?,?,?,?', [
                    $params_array['id'],
                    $params_array['name'],
                    $params_array['surname'],
                    $params_array['telephone'],
                    $params_array['direction'],
                    $params_array['email'],
                    $params_array['startDay'],
                    $params_array['finalDay'],
                    $params_array['StartTime'],
                    $params_array['finalTime'],
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

        return response()->json($data, $data['code']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  
    public function show($id){

        $provider = DB::select('exec pa_selectProvider ?', [$id]);
       
        if (count($provider) > 0) {
            $data = [
                'code' => 200,
                'status' => 'Provider encontrado correctamente',
                'data' => $provider
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'Cliente no encontrado'
            ];
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
 
    public function update($id, Request $request){
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
           
            $validate = Validator::make($params_array, [
                'name'      => 'required',
                'surname'   => 'required',
                'telephone' => 'required',
                'direction' => 'required',
                'email'     => 'required|email|',
                'startDay'  => 'required',
                'finalDay'  => 'required',
                'StartTime' => 'required',
                'finalTime' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'No se ha actualizado el proovedor, faltan datos',
                    'data'      => $validate->errors()
                ];
            } else {

                $unique = DB::select('exec pa_selectProvider ?', [$params->email]);

                if ((count($unique) > 0) && (strtoupper($id) != $unique[0]->id)) {
 
                    $data = array(
                        'code' => 404,
                        'status' => 'error',
                        'data' => 'El cliente ya existe'
                    );
                    return response()->json($data, $data['code']);
                }
            
                unset($params_array['id']);
                unset($params_array['created_at']);

                $params_array['id'] = $id;
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateProvider ?,?,?,?,?,?,?,?,?,?,?', [
                    $params_array['id'],
                    $params_array['name'],
                    $params_array['surname'],
                    $params_array['telephone'],
                    $params_array['direction'],
                    $params_array['email'],
                    $params_array['startDay'],
                    $params_array['finalDay'],
                    $params_array['StartTime'],
                    $params_array['finalTime'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'data'      => $params_array
                ];
            }
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'data'      => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        if (isset($id)) {

            $provider = DB::select('exec pa_selectProviderById ?', [$id]);

            if (count($provider) > 0) {

                DB::delete('exec pa_deleteProvider ?', [$id]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => 'Se elimino correctamente'
                ];
            } else {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'data' => 'No se elimino correctamente'
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'data' => 'No se encontro el proveedor'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
