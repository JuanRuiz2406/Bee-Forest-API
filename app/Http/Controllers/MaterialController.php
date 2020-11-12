<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Uuid;

class MaterialController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth');
    }

    //GET ALL
    public function index()
    {

        $materials = DB::select('exec pa_readMaterials');

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'data'      => $materials
        ]);
    }

    //GET ONE
    public function show($materialId)
    {

        $material = DB::select('exect pa_selectMaterial', $materialId);

        if (is_object($material)) {
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'message'   => 'Material encontrado correctamente.',
                'data'      => $material
            ];
        } else {
            $data = [
                'code'      => 404,
                'status'    => 'error',
                'message'   => 'Error, el material no existe.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    //POST
    public function store(Request $request)
    {

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params) && !empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'providerId' => 'required',
                'name'       => 'required|unique:materials',
                'price'      => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'status'    => 'error',
                    'code'      => 404,
                    'message'   => 'Error, hay campos vacíos o no cumplen los requisitos',
                    'data'      => $validate->errors()
                );
            } else {

                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addMaterial ?,?,?,?,?,?', [
                    $params_array['providerId'],
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['description'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Material registrado correctamente.',
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
    public function update(Request $request, $id)
    {

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'providerId'    => 'required',
                'name'          => 'required|unique:materials,name,' . $id,
                'price'         => 'required',
                'amount'        => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, hay campos vacíos.',
                    'data'      => $validate->errors()
                ];
            } else {

                unset($params_array['id']);
                unset($params_array['created_at']);
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateMaterial ?,?,?,?,?,?,?', [
                    $id,
                    $params_array['providerId'],
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['amount'],
                    $params_array['description'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Material actualizado correctamente.',
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
    public function destroy($id)
    {
        if (isset($id)) {

            $material = DB::select('exec pa_selectMaterial ?', [$id]);

            if (count($material) > 0) {

                $delete = DB::delete('exec pa_deleteMaterial ?', [$id]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Material eliminado correctamente.',
                    'data'      => $delete,
                ];
            } else {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Material no encontrado.'
                ];
            }
        } else {

            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Material no encontrado.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request, $id)
    {
        // Recoger la imagen de la petición
        $image = $request->file('file0');

        // Validar imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        // Guardar la imagen
        if (!$image || $validate->fails()) {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        } else {
            $image_name = time() . $image->getClientOriginalName();

            \Storage::disk('materials')->put($image_name, \File::get($image));

            DB::update('UPDATE materials SET image = ? WHERE id = ? ', [$image_name, $id]);
            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }

        // Devolver datos
        return response()->json($data, $data['code']);
    }

    public function getImage($filename)
    {
        // Comprobar si existe el fichero
        $isset = \Storage::disk('materials')->exists($filename);

        if ($isset) {
            // Conseguir la imagen
            $file = \Storage::disk('materials')->get($filename);

            // Devolver la imagen
            return new Response($file, 200);
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
