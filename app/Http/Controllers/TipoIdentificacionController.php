<?php

namespace App\Http\Controllers;

use App\Models\TipoIdentificacion;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Tipo Identificacion
 *
 * APIs para la gestion de la tabla tipo identifiacion
 */
class TipoIdentificacionController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla tipo identificacion paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $tipo_identifiacion = TipoIdentificacion::paginate(15);
        return $this->sendResponse($tipo_identifiacion->toArray(), 'Tipos de identificacion devueltos con éxito');
    }


    /**
     * Lista de los tipos de identificacion.
     *
     * @return \Illuminate\Http\Response
     */
    public function tipo_identificacion_all()
    {
        
        $tipo_identifiacion = TipoIdentificacion::get();
        return $this->sendResponse($tipo_identifiacion->toArray(), 'Tipos de identificacion devueltos con éxito');
    }


    /**
     * Buscar Tipo de identificacion por descripción.
     *@bodyParam descripcion string Descripcion del Tipo de identificacion.
     *@response{
     *    "descripcion" : "cedula",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarTipoIdentificacion(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["descripcion"]) && $input["descripcion"] != null){
            
            $input = $request->all();
            $tipo_identifiacion = \DB::table('tipo_identificacion')
                ->where('tipo_identificacion.descripcion','like', '%'.strtolower($input["descripcion"]).'%')
                ->select('tipo_identificacion.id','tipo_identificacion.code','tipo_identificacion.descripcion')
                ->get();
            return $this->sendResponse($tipo_identifiacion->toArray(), 'Todos los tipos de identificacion filtrados');
       }else{
            
            $tipo_identifiacion = \DB::table('tipo_identificacion')                
                ->select('tipo_identificacion.id','tipo_identificacion.code','tipo_identificacion.descripcion')
                ->get();
            return $this->sendResponse($tipo_identifiacion->toArray(), 'Todos los tipos de identificacion devueltos'); 
       }
        
    }

   
    /**
     * Agrega un nuevo elemento a la tabla tipo identifiacion
     *@bodyParam code string required Código del tipo de identifiacion.
     *@bodyParam descripcion string Descripcion del tipo de identifiacion.
     *@response {      
     *  "code": "NA",
     *  "descripcion": "No aplica"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'descripcion' => 'nullable|string'             
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $tipo_identifiacion=TipoIdentificacion::create($request->all());        
        return $this->sendResponse($tipo_identifiacion->toArray(), 'Tipo de identifiacion creado con éxito');
    }

    /**
     * Lista un tipo de identifiacion en especifico 
     *
     * [Se filtra por el ID]
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $tipo_identifiacion = TipoIdentificacion::find($id);
        if (is_null($tipo_identifiacion)) {
            return $this->sendError('Tipo de identifiacion no encontrado');
        }
        return $this->sendResponse($tipo_identifiacion->toArray(), 'Tipo de identifiacion devuelto con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla tipo_identifiacion 
     *
      *@bodyParam code string required Código del tipo de identifiacion.
     *@bodyParam descripcion string Descripcion del tipo de identifiacion.
     *@response {      
     *  "code": "NA",
     *  "descripcion": "No aplica"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoIdentificacion  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',           
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

        $tipo_identifiacion = TipoIdentificacion::find($id);

        if (is_null($tipo_identifiacion)) {
            return $this->sendError('Tipo de identifiacion no encontrado');
        }

        $tipo_identifiacion->code = $input['code'];              
        $tipo_identifiacion->descripción = $input['descripción'];
        $tipo_identifiacion->save();
         
        return $this->sendResponse($tipo_identifiacion->toArray(), 'Tipo de identifiacion actualizado con éxito');
    }

     /**
     * Elimina un elemento de la tabla tipo_identifiacion
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoIdentificacion  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            
            $tipo_identifiacion= TipoIdentificacion::find($id);
            if (is_null($tipo_identifiacion)) {
                return $this->sendError('Tipo de identifiacion no encontrado');
            }
            $tipo_identifiacion->delete();

            return $this->sendResponse($tipo_identifiacion->toArray(), 'Tipo de identifiacion eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Tipo de identifiacion no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
