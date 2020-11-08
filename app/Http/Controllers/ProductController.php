<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // Con esto podemos hacer consultas por sql
use App\Helpers\JwtAuth;

class ProductController extends Controller
{

    public function __construct(){ $this->middleware('api.auth',['except' => ['getImage']]); }

    //GET ALL
    public function index()
    {

        $products = DB::select('exec pa_readProducts');

        return response()->json([
            'code'      => 200,
            'status'    => 'success',
            'data'      => $products
        ]);
    }

    //GET ONE
    public function show($productId)
    {
        $product = DB::select('exec pa_selectProduct ?', $productId);
        if (is_object($product)) {
            $data = [
                'code'      => 200,
                'status'    => 'success',
                'message'   => 'Producto encontrado correctamente.',
                'data'      => $product
            ];
        } else {
            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Error, el producto no existe.'
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

        if (!empty($params_array)) {

            $validate = \Validator::make($params_array, [
                'categoryId'    => 'required',
                'name'          => 'required|unique:products',
                'price'         => 'required'
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Error, hay campos vacíos o no cumplen los requisitos.',
                    'data'      => $validate->errors()
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
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Producto registrado correctamente.',
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
                'categoryId'    => 'required',
                'name'          => 'required|unique:products,name,'.$id,
                'price'         => 'required',
                'amount'        => 'required',
            ]);

            if ($validate->fails()) {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'No se ha actualizado el producto, faltan datos',
                    'data'      => $validate->errors()
                ];
            } else {

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
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Producto actualizado correctamente.',
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

            $product = DB::select('exec pa_selectProduct ?', [$id]);

            if (count($product) > 0) {

                $delete = DB::delete('exec pa_deleteProduct ?', [$id]);

                $data = [
                    'code'      => 200,
                    'status'    => 'success',
                    'message'   => 'Producto eliminado correctamente.',
                    'data'      => $delete,
                ];
            } else {
                $data = [
                    'code'      => 400,
                    'status'    => 'error',
                    'message'   => 'Producto no encontrado.'
                ];
            }
        } else {

            $data = [
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'Producto no encontrado.'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request, $id){
        // Recoger la imagen de la petición
        $image = $request->file('file0');
        
        // Validar imagen
        $validate = \Validator::make($request->all(), [
           'file0' => 'required|image|mimes:jpg,jpeg,png,gif' 
        ]);
    
        // Guardar la imagen
        if(!$image || $validate->fails()){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            ];
        }else{
            $image_name = time().$image->getClientOriginalName();
            
            \Storage::disk('products')->put($image_name, \File::get($image));
        
            DB::update('UPDATE products SET image = ? WHERE id = ? ', [$image_name, $id]);
            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            ];
        }
        
        // Devolver datos
        return response()->json($data, $data['code']);
    }
    
    public function getImage($filename){
        // Comprobar si existe el fichero
        $isset = \Storage::disk('products')->exists($filename);
        
        if($isset){
            // Conseguir la imagen
            $file = \Storage::disk('products')->get($filename);
            
            // Devolver la imagen
            return new Response($file, 200);
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            ];
        }
        
        return response()->json($data, $data['code']);
    }
}
