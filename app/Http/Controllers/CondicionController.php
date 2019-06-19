<?php

namespace App\Http\Controllers;

use App\Models\Condicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Administración de Condiciones
 *
 * APIs para la gestion de condiciones
 */
class CondicionController extends BaseController
{
    /**
     * Lista de la tabla condiciones.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $condiciones = Condicion::paginate(15);

        return $this->sendResponse($condiciones->toArray(), 'Condiciones devueltas con éxito');
    }


      /**
     * Buscar Condición por descripción.
     *@bodyParam nombre string Nombre de la condición.
     *@response{
     *    "nombre" : "Condicion 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarCondicion(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $condiciones = Condicion::where('descripcion','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($condiciones->toArray(), 'Todas las condiciones filtradas');
       }else{
            
            $condiciones = Condicion::get();
            return $this->sendResponse($condiciones->toArray(), 'Todas las condiciones devueltas'); 
       }

        
    }

  
    /**
     * Agrega un nuevo elemento a la tabla condiciones
     *@bodyParam descripcion string required Condición del evento.     
     *@response{
     *       "descripcion" : "Cerrado"             
     *     }
     *    
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion'=> 'required'                       
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $condicion = Condicion::create($request->all());        
        return $this->sendResponse($condicion->toArray(), 'Condición creada con éxito');
    }

    /**
     * Lista una condición en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Condicion  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $condicion = Condicion::find($id);
        if (is_null($condicion)) {
            return $this->sendError('Condición no encontrado');
        }
        return $this->sendResponse($condicion->toArray(), 'Condición devuelta con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla condiciones 
     *
     * [Se filtra por el ID]
     *@bodyParam descripcion string required Condición del evento.     
     *@response{
     *       "descripcion" : "Abierto"             
     *     }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'descripcion'=> 'required'                       
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $condicion = Condicion::find($id);
        if (is_null($condicion)) {
            return $this->sendError('Condición no encontrado');
        }

        $condicion->descripcion = $input['descripcion'];        
        $condicion->save();

        return $this->sendResponse($condicion->toArray(), 'Condición actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla condiciones
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Condicion  $condicion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $condicion =Condicion::find($id);
            if (is_null($condicion)) {
                return $this->sendError('Condición no encontrada');
            }
            $condicion->delete();
            return $this->sendResponse($condicion->toArray(), 'Condición eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La Condición no se puedo eliminar, el registro esta siendo usado en la tabla condiciones_evento', 'exception' => $e->errorInfo], 400);
        }
    }
}
