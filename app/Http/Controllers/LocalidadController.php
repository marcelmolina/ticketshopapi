<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use App\Models\Tribuna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Validator;
/**
 * @group Administración de Localidad
 *
 * APIs para la gestion de la tabla localidad
 */
class LocalidadController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla localidad paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $localidad = Localidad::with('tribuna')->with('filas')->with('palcos')->with('puestos')->paginate(15);
        return $this->sendResponse($localidad->toArray(), 'Localidades devueltas con éxito');
    }


    /**
     * Lista de todas las localidades.
     *
     * @return \Illuminate\Http\Response
     */
    public function localidad_all()
    {
        $localidad = Localidad::with('tribuna')->with('filas')->with('palcos')->with('puestos')->get();

        return $this->sendResponse($localidad->toArray(), 'Localidades devueltas con éxito');
    }
   

    /**
     * Buscar localidades por descripción.
     *@bodyParam nombre string Nombre de la localidad.
     *@response{
     *    "nombre" : "Localidad",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarLocalidad(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $localidades = \DB::table('localidad')
                ->join('tribuna','tribuna.id','=','localidad.id_tribuna')
                ->where('localidad.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('localidad.*', 'tribuna.*')
                ->get();
            return $this->sendResponse($localidades->toArray(), 'Todas las localidades filtradas');
       }else{
            
            $localidades = \DB::table('localidad') 
                ->join('tribuna','tribuna.id','=','localidad.id_tribuna')               
                ->select('localidad.*', 'tribuna.*')
                ->get();
            return $this->sendResponse($localidades->toArray(), 'Todas las localidades devueltas'); 
       }

        
    }


    public function getB64Image($base64_image){  
        $image_service_str = substr($base64_image, strpos($base64_image, ",")+1);
        $image = base64_decode($image_service_str);   
        return $image; 
    }

    public function getB64Extension($base64_image, $full=null){  
        preg_match("/^data:image\/(.*);base64/i",$base64_image, $img_extension);   
        return ($full) ?  $img_extension[0] : $img_extension[1];  
    }

    /**
     * Agrega un nuevo elemento a la tabla localidad
     *@bodyParam nombre string required Nombre de la localidad.
     *@bodyParam id_tribuna int required Id de la tribuna.
     *@bodyParam puerta_acceso string Puerta de acceso de la loccalidad. Defaults to 0
     *@bodyParam imagen string Imagen en formato base64.
     *@bodyParam palco boolean Posee palcos.
     *@bodyParam aforo int Capacidad total de las localidades de un evento.
     *@bodyParam silleteria boolean
     *@bodyParam puestosxpalco int Id Puesto por Palco
     * @response {
     *  "nombre": "Localidad New",
     *  "id_tribuna": 1, 
     *  "puerta_acceso":null,
     *  "imagen": null,
     *  "aforo": 1,
     *  "palco":null, 
     *  "silleteria": true,
     *  "puestosxpalco":1     
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',            
            'id_tribuna' => 'required',
            'puerta_acceso' => 'alpha_num|max:20',
            'imagen' => 'nullable|string',
            'aforo' => 'nullable|int',
            'palco' => 'nullable|boolean',
            'silleteria' => 'nullable|boolean',
            'puestosxpalco' => 'nullable|int'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $tribuna = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna)) {
            return $this->sendError('La Tribuna indicada no existe');
        }

        $urlFile = null;
        if($request->input('imagen') != null || $request->input('imagen') != ""){

            $img = $this->getB64Image($request->input('imagen'));  

            $img_extension = $this->getB64Extension($request->input('imagen'));        
            $img_name = 'image_base64'. time() . '.' . $img_extension; 
            $path = public_path() . "/storage/imagenes/" . $img_name;
            file_put_contents($path, $img);
            $urlFile = env('APP_URL').'storage/imagenes/'. $img_name;

        }


        $localidad = new Localidad();
        $localidad->nombre = $request->input('nombre');
        $localidad->id_tribuna = $request->input('id_tribuna');
        $localidad->puerta_acceso = $request->input('puerta_acceso');
        $localidad->silleteria = $request->input('silleteria');
        $localidad->palco = $request->input('palco');
        $localidad->aforo = $request->input('aforo'); 
        $localidad->puestosxpalco = $request->input('puestosxpalco'); 

        $localidad->save();
        
        return $this->sendResponse($localidad->toArray(), 'Localidad creada con éxito');
    }

    /**
     * Lista de una localidad en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $localidad = Localidad::with('filas')->with('palcos')->with('puestos')->find($id);


        if (is_null($localidad)) {
            return $this->sendError('Localidad no encontrado');
        }


        return $this->sendResponse($localidad->toArray(), 'Localidad devuelta con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla localidad 
     * [Se filtra por el ID de la localidad]
     *
     *@bodyParam nombre string required Nombre de la localidad.
     *@bodyParam id_tribuna int required Id de la tribuna.
     *@bodyParam puerta_acceso string Puerta de acceso de la loccalidad. Defaults to 0
     *@bodyParam imagen string Imagen en formato base64.
     *@bodyParam palco boolean Posee palcos.
     *@bodyParam aforo int Capacidad total de las localidades de un evento.
     *@bodyParam silleteria boolean
     *@bodyParam puestosxpalco int Id Puesto por Palco
     *
     * @response {
     *  "nombre": "Localidad 2",
     *  "id_tribuna": 1, 
     *  "puerta_acceso":"AA12", 
     *  "palco":null,
     *  "aforo": 1,
     *  "silleteria": false,
     *  "puestosxpalco": 1         
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',            
            'id_tribuna' => 'required',
            'puerta_acceso' => 'alpha_num|max:20',
            'aforo' => 'nullable|int',
            'silleteria' => 'nullable|boolean',
            'palco' => 'nullable|boolean',
            'puestosxpalco' => 'nullable|int'
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        $tribuna_search = Tribuna::find($request->input('id_tribuna'));
        if (is_null($tribuna_search)) {
            return $this->sendError('La tribuna indicada no existe');
        }

        $localidad_search = Localidad::find($id);        
        if (is_null($localidad_search)) {
            return $this->sendError('Localidad no encontrada');
        }

        $localidad_search->nombre = $input['nombre'];
        $localidad_search->id_tribuna = $input['id_tribuna'];
        $localidad_search->puerta_acceso = $input['puerta_acceso']; 
        $localidad_search->aforo = $input['aforo'];
        $localidad_search->silleteria = $input['silleteria'];  
        $localidad_search->palco = $input['palco'];  
        $localidad_search->puestosxpalco = $input['puestosxpalco'];         
        $localidad_search->save();

        return $this->sendResponse($localidad_search->toArray(), 'Localidad actualizada con éxito');

    }

    /**
     * Elimina un elemento de la tabla localidad
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {

            $localidad = Localidad::find($id);
            if (is_null($localidad)) {
                return $this->sendError('Localidad no encontrada');
            }
            $localidad->delete();
            return $this->sendResponse($localidad->toArray(), 'Localidad eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La localidad no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }


        
    }
}

