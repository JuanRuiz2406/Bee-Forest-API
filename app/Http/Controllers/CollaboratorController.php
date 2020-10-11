<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Uuid; 

class CollaboratorController extends Controller{

    public function register(Request $request){

        $json = $request->input('json', null);
        $params = json_decode($json); // objeto
        $params_array = json_decode($json, true); // array

        if (!empty($params) && !empty($params_array)) {

            $params_array = array_map('trim', $params_array);

            $validate = \Validator::make($params_array, [
                'username' => 'required|unique:collaborators',
                'email' => 'required|email',
                'role' => 'required',
                'password' => 'required',
            ]);

            if ($validate->fails()) {
           
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El colaborador no se ha creado',
                    'data' => $validate->errors()
                );
            } else {

                $pwd = hash('sha256', $params->password);

                $params_array['id'] = Uuid::generate()->string;
                $params_array['password'] = $pwd;
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addCollaborator ?,?,?,?,?,?,?', [
                    $params_array['id'],
                    $params_array['username'],
                    $params_array['password'],
                    $params_array['email'],
                    $params_array['role'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El colaborador se ha registrado correctamente'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request){

        $jwtAuth = new \JwtAuth();

        // Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        // Validar esos datos
        $validate = \Validator::make($params_array, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            // La validación ha fallado
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El colaborador no se ha podido identificar',
                'data' => $validate->errors()
            );
        } else {

            $pwd = hash('sha256', $params->password);

            $signup = $jwtAuth->signup($params->username, $pwd);

            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->username, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }

    public function update(Request $request){

        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        $json = $request->input('json', null);
        $params =  json_decode($json);

        if ($checkToken && !empty($params)) {


            $collaborator = $jwtAuth->checkToken($token, true);
            $params_array =  (array) $params;

            $validate = \Validator::make($params_array, [
                'username' => 'required|unique:collaborators' . $collaborator->id,
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $unique = DB::select('exec pa_selectUserNameCollaborator ? ', [$params->username]);

            if ((count($unique) > 0) && ($collaborator->username != $unique[0]->username)) {
                $data = array(
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'El colaborador ya existe'
                );
            return response()->json($data, $data['code']);
            }

            $pwd = hash('sha256', $params->password);
            $params->password = $pwd;

            unset($params_array['id']);
            unset($params_array['created_at']);

            if($collaborator->username == 'admin'){
                $params_array['username'] = 'admin';
            }

            $params_array['id'] = $collaborator->id;
            $params_array['password'] = $pwd;
            $params_array['updated_at'] = new \DateTime();

            DB::update('exec pa_updateCollaborator ?, ?, ?, ?, ?, ?', [
                            $params_array['id'],
                            $params_array['username'],
                            $params_array['password'],
                            $params_array['email'],
                            $params_array['role'],
                            $params_array['updated_at']
            ]);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'collaborator' => $collaborator,
                'data' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El colaborador no está identificado.'
            );
        }

        return response()->json($data, $data['code']);
    }

   public function detail($id) {

        $collaborator = DB::select('exec pa_selectCollaborator ?', [$id]);

        if (count($collaborator) > 0) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'data' => $collaborator
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El colaborador no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id){

        $collaborator = DB::select('exec pa_selectCollaborator ?', [$id]);

        if (count($collaborator) > 0) {

            if($collaborator[0]->role != 'admin'){

                DB::delete('exec pa_deleteCollaborator ?', [$id]);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => $collaborator
                ];

            }else{

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'message' => 'colaborador admin no se puede eliminar'
                ];
            }

        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El colaborador no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
