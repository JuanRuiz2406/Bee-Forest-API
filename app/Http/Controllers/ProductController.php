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
        $this->middleware('api.auth');
    }

    public function index()
    {

        $products = DB::select('exec pa_readProducts');

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {

        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'categoryId' => 'required',
                'name' => 'required|unique:products',
                'price' => 'required'
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

                //Guardar el Producto
                DB::insert('exec pa_addProduct ?,?,?,?,?,?,?', [
                    $params_array['categoryId'],
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['description'],
                    $params_array['image'],
                    $params_array['created_at'],
                    $params_array['updated_at']
                ]);

                /* Esto pa otro dia
                
                //Buscar el producto
                $product = DB::select('exec pa_selectProductByName ?', [$params->name]);

                //Registrar los materiales en product_material (Tabla pivote)
                if(count($product) > 0){ // si el producto se guardo

                    //recorrmos el array de materials (todos los materiales para crear un producto)
                    foreach ($params_array['materials'] as $key => $value) {

                        //Por cada vuelta buscar por id en materiales
                        $material = DB::select('exec pa_selectMaterial ?',  [$value['id']]);

                        //cantidad de materiales - cantidad de productos por cantidad de materiales da negativo
                        if(($material[0]->amount - ($product[0]->amount * $value['amount'])) > 0){

                            //Insertamos en la tabla intermedia
                            DB::insert('insert into product_material (productId, materialId, amount, created_at, updated_at) values (?,?,?,?,?)', [
                                $product[0]->id,
                                $value['id'],
                                $value['amount'],
                                new \DateTime(),
                                new \DateTime()
                            ]);

                            //Hacemos el update de la nueva contidad en meterilas
                            DB::insert('update materials set amount = ? where id = ?', [
                                $material[0]->amount - ($product[0]->amount * $value['amount']),
                                $value['id'],
                            ]);
                        }else{

                            //Si alguno de los materiales da en negativo la cantidad eliminamos el producto recien creado y retornamos una advertencia
                            $delete = DB::delete('exec pa_deleteProduct ?', [ $product[0]->id ]);

                            $data = [
                                'code' => 400,
                                'status' => 'error',
                                'message' => 'La cantidad de materiales para crear el producto no es suficiente'
                            ];

                            return response()->json($data, $data['code']);

                        }

                    }

                }
                */
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
                'message' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function show($productId)
    {
        $product = DB::select('exec pa_selectProduct ?', $productId);
        if (is_object($product)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'Producto encontrado correctamente',
                'data' => $product
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Producto no encontrado o id de producto no existe'
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
                'categoryId' => 'required',
                'name' => 'required',
                'price' => 'required',
                'amount' => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha actualizado el producto, faltan datos',
                    'data' => $validate->errors()
                ];
            } else {

                $unique = DB::select('exec pa_selectProductByName ?', [$params->name]);

                if ((count($unique) > 0) && (strtoupper($id) != $unique[0]->id)) {

                    $data = array(
                        'code' => 404,
                        'status' => 'error',
                        'message' => 'El el nombre del producto ya existe'
                    );

                    return response()->json($data, $data['code']);
                }

                unset($params_array['id']);
                unset($params_array['created_at']);
                $params_array['updated_at'] = new \DateTime();

                DB::update('exec pa_updateProduct ?,?,?,?,?,?,?', [
                    $id,
                    $params_array['categoryId'],
                    $params_array['name'],
                    $params_array['price'],
                    $params_array['amount'],
                    $params_array['description'],
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
                'message' => 'Envia los datos correctamente'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function destroy($id)
    {
        if (isset($id)) {

            $product = DB::select('exec pa_selectProduct ?', [$id]);

            if (count($product) > 0) {

                $delete = DB::delete('exec pa_deleteProduct ?', [$id]);

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
                'message' => 'No se encontro el producto'
            ];
        }

        return response()->json($data, $data['code']);
    }
}
