<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;

class JwtAuth
{

    public $key;

    public function __construct()
    {
        $this->key = 'Proyecto_Bee_Forest_APIREST_creada_por_el_team_Juan_Marco_Diego_Elvin_2020';
    }

    public function signup($email, $password, $getToken = null)
    {

            $collaborator = DB::select(
                'select *, CONVERT(nvarchar(36), collaborators.id) as id from collaborators where email = :email and password = :password',
                [
                    "email" => $email,
                    "password" => $password
                ]
            );

            // Comprobar si son correctas(objeto)
            if (count($collaborator) > 0) {
                $token = array(
                    'id'      =>      $collaborator[0]->id,
                    'username'   =>   $collaborator[0]->username,
                    'email'    =>     $collaborator[0]->email,
                    'role'    =>      $collaborator[0]->role,
                    'iat'     =>      time(),
                    'exp'     =>      time() + (30 * 24 * 60 * 60) //expiracion de un mes
                );
            
              // Generar el token con los datos del colaborador idenficado
                $jwt = JWT::encode($token, $this->key, 'HS256');
                $decoded = JWT::decode($jwt, $this->key, ['HS256']);
    
                // Devoler los datos decodificados o el token, en función de un parametro
                if (is_null($getToken)) {
                    $data = $jwt;
                } else {
                    $data = $decoded;
                }
            } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto.'
            );
        }

        return $data;
    }

    public function checkToken($jwt, $getIdentity = false)
    {
        $auth = false;
        try {
            $jwt = str_replace('"', '', $jwt);
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);
        } catch (\UnexpectedValueException $e) {
            $auth = false;
        } catch (\DomainException $e) {
            $auth = false;
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->id)) {

            $auth = true;
        } else {
            $auth = false;
        }

        if ($getIdentity) {
            return $decoded;
        }

        return $auth;
    }
}