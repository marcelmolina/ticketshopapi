<?php

namespace App\Http\Controllers;

use App\Models\TipoEvento;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de Tipo Evento
 *
 * APIs para la gestion de la tabla tipo_evento
 */
class TipoEventoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla tipo evento paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
        $tipoEventos = TipoEvento::paginate(15);
        return $this->sendResponse($tipoEventos->toArray(), 'Tipos de eventos devueltos con éxito');
    }


    /**
     * Lista de los tipos de evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function tipo_evento_all()
    {
       
        $tipoEventos = TipoEvento::get();
        return $this->sendResponse($tipoEventos->toArray(), 'Tipos de eventos devueltos con éxito');
    }


    /**
     * Buscar Tipo de evento por descripción.
     *@bodyParam nombre string Nombre del Tipo de evento.
     *@response{
     *    "nombre" : "Tipo de evento",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarTipoEvento(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $tipoEventos = \DB::table('tipo_evento')
                ->where('tipo_evento.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('tipo_evento.id','tipo_evento.nombre')
                ->get();
            return $this->sendResponse($tipoEventos->toArray(), 'Todos los tipos de eventos filtrados');
       }else{
            
            $tipoEventos = \DB::table('tipo_evento')                
                ->select('tipo_evento.id','tipo_evento.nombre')
                ->get();
            return $this->sendResponse($tipoEventos->toArray(), 'Todos los tipos de eventos devueltos'); 
       }
        
    }


    /**
     * Agrega un nuevo elemento a la tabla tipo_evento
     *@bodyParam nombre string required Nombre del tipo de evento.
     * @response {      
     *  "nombre": "Tipo 1"            
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $tipoEvento=TipoEvento::create($request->all());        
         return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento creado con éxito');
    }

    /**
     * Lista de un tipo de evento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $tipoEvento = TipoEvento::find($id);

        if (is_null($tipoEvento)) {
            return $this->sendError('Tipo de evento no encontrado');
        }

        return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento devuelto con éxito');
    }

 

    /**
     * Actualiza un elemeto de la tabla tipo_evento 
     *
     * [Se filtra por el ID]
     *@bodyParam nombre string required Nombre del tipo de evento.
     * @response {
     *  "nombre": "Tipo Evento 1"
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
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

        $tipoEvento = TipoEvento::find($id);
        if (is_null($tipoEvento)) {
            return $this->sendError('Tipo de evento no encontrado');
        }

        $tipoEvento->nombre = $input['nombre'];              
        $tipoEvento->save();
         
        return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla tipo_evento
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $tipoEvento = TipoEvento::find($id);
            if (is_null($tipoEvento)) {
                return $this->sendError('Tipo de evento no encontrado');
            }
            $tipoEvento->delete();
            return $this->sendResponse($tipoEvento->toArray(), 'Tipo de evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El tipo de evento no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }

    
}
