<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use Uuid; //Generamos ID unico para cada registro

class CollaboratorController extends Controller{
    
    public function register(Request $request)
    {

        // Recorger los datos del colaborador por post
        $json = $request->input('json', null);
        $params = json_decode($json); // objeto
        $params_array = json_decode($json, true); // array

        if (!empty($params) && !empty($params_array)) {

            // Limpiar datos
            $params_array = array_map('trim', $params_array);

            // Validar datos
            $validate = \Validator::make($params_array, [
                'username' => 'required|unique:collaborators',
                'email' => 'required|email',
                'role' => 'required',
                'password' => 'required',
            ]);

            if ($validate->fails()) {
                // La validación ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El colaborador no se ha creado',
                    'data' => $validate->errors()
                );
            } else {

                // Validación pasada correctamente
                // Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                $params_array['id'] = Uuid::generate()->string;
                $params_array['password'] = $pwd;
                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('INSERT 
                                INTO collaborators (id, username, password, email, role, created_at, updated_at) 
                                VALUES (?,?,?,?,?,?,?)', [
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
                    'message' => 'El colaborador se ha registrado correctamente',
                    'data' => $params_array
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'data' => 'Los datos enviados no son correctos'
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
            // Cifrar la password
            $pwd = hash('sha256', $params->password);

            // Devolver token o datos
            $signup = $jwtAuth->signup($params->username, $pwd);

            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->username, $pwd, true);
            }
        }

        return response()->json($signup, 200);
    }

    public function update(Request $request){

        // Comprobar si el colaborador está identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        // Recoger los datos por post
        $json = $request->input('json', null);
        $params =  json_decode($json);

        if ($checkToken && !empty($params)) {

            // Optener colaborador identificado
            $collaborator = $jwtAuth->checkToken($token, true);
            $params_array =  (array) $params;
            // Validar datos
            $validate = \Validator::make($params_array, [
                'username' => 'required|unique:collaborators' . $collaborator->id,
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $unique = DB::select('select username, email from collaborators where username = ? ', [$params->username]);

            if (count($unique) > 0) {
                $data = array(
                    'code' => 404,
                    'status' => 'error',
                    'data' => 'El usuario ya existe'
                );
            return response()->json($data, $data['code']);
            }
           
            //En angular validar si se modifica o no la contraseña
            $pwd = hash('sha256', $params->password);
            $params->password = $pwd;

            // Quitar los campos que no quiero actualizar
            unset($params_array['id']);
            unset($params_array['created_at']);
            
            if($collaborator->username == 'admin'){
                $params_array['username'] = 'admin';
            }

            $params_array['id'] = $collaborator->id;
            $params_array['password'] = $pwd;
            $params_array['updated_at'] = new \DateTime();

            DB::update('UPDATE collaborators SET email = ?, username = ?, role = ?, password = ?, updated_at = ? 
                        WHERE id = ?', [
                            $params_array['email'],
                            $params_array['username'],
                            $params_array['role'],
                            $params_array['password'],
                            $params_array['updated_at'],
                            $params_array['id']
            ]);

            // Devolver array con resultado
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
                'data' => 'El colaborador no está identificado.'
            );
        }

        return response()->json($data, $data['code']);
    }

   public function detail($id) {

        $collaborator = DB::select('select CONVERT(nvarchar(36), collaborators.id) AS id, username, email, role from collaborators where id = ?', [$id]);

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
                'data' => 'El colaborador no existe.'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id){

        $collaborator = DB::select('select CONVERT(nvarchar(36), collaborators.id) AS id, username, email, role from collaborators where id = ?', [$id]);

        if (count($collaborator) > 0) {
           
            if($collaborator[0]->username != 'admin'){
               
                DB::delete('delete from collaborators where id=?', [$id]);

                // Devolver algo
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'data' => $collaborator
                ];

            }else{

                $data = [
                    'code' => 404,
                    'status' => 'error',
                    'data' => 'Usuario admin no se puede eliminar'
                ];
            }
   
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'data' => 'El colaborador no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

}
