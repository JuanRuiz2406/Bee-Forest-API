<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use App\Helpers\JwtAuth;

class CategoryController extends Controller
{

    public function __construct(){ $this->middleware('api.auth'); }

    //GET ALL
    public function index(){

        $categories = DB::select('exec pa_readCategories');

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'data'      => $categories
        ]);
    }

    //GET ONE
    public function show($name)
    {
        $category = DB::select('select * from categories where name = ?', [$name]);

        if (count($category) > 0) {
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'message'   => 'Categoría encontrada correctamente.',
                'data'      => $category
            ];
        } else {
            $data = [
                'code'      => 404,
                'status'    => 'error',
                'message'   => 'Error, la categoría no existe.'
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
                'name' => 'required|unique:categories',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, el nombre está vacío o ya existe.',
                    'error'     => $validate->errors()
                ];
            } else {

                $params_array['created_at'] = new \DateTime();
                $params_array['updated_at'] = new \DateTime();

                DB::insert('exec pa_addCategory ?,?,?,?', [
                    $params_array['name'],
                    $params_array['description'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Categoría guardada exitosamente.',
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
                'name' => 'required'
            ]);

            $unique = DB::select('exec pa_selectCategoryByName ?', [$params->name]);

            if ((count($unique) > 0) && (strtoupper($id) != $unique[0]->id)) {

                $data = array(
                    'code'      => 404,
                    'status'    => 'error',
                    'message'   => 'El el nombre de la categoría ya existe.'
                );

                return response()->json($data, $data['code']);
            }

            unset($params_array['id']);
            unset($params_array['created_at']);

            $params_array['id'] = $id;
            $params_array['updated_at'] = new \DateTime();

            DB::update('exec pa_updateCategory ?,?,?,?', [
                $params_array['id'],
                $params_array['name'],
                $params_array['description'],
                $params_array['updated_at'],
            ]);

            $data = [
                'code'      => 200,
                'status'    => 'success',
                'message'   => 'Categoría actualizada correctamente.',
                'data'      => $params_array
            ];
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

            $material = DB::select('exec pa_selectCategory ?', [$id]);

            if (count($material) > 0) {

                $delete = DB::delete('exec pa_deleteCategory ?', [$id]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Categoría eliminada correctamente.',
                    'data'      => $delete,
                ];
            } else {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, la categoría no puede ser eliminada, tiene productos asociados.'
                ];
            }
        } else {

            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Categoría no encontrada.'
            ];

        }

        return response()->json($data, $data['code']);
    }
}
