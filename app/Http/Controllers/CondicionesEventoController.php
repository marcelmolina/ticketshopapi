<?php

namespace App\Http\Controllers;

use App\Models\CondicionesEvento;
use App\Models\Condicion;
use App\Models\Evento;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Condiciones - Evento
 *
 * APIs para la gestion de la tabla asociativa condiciones_evento
 */
class CondicionesEventoController extends BaseController
{
    

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'edit', 'update', 'destroy']]);
    }

    /**
     * Listado de las condiciones por eventos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evento_condicion = CondicionesEvento::with('evento')
                            ->with('condicion')
                            ->paginate(15);
        return $this->sendResponse($evento_condicion->toArray(), 'Condiciones por evento devueltos con éxito');
    }

    

    /**
     * Agrega un nuevo elemento a la tabla condiciones_evento
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam id_condiciones int required Id de la condicion.
     *@response{
     *  "id_evento": 3,
     *  "id_condiciones": 1,      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento' => 'required|integer', 
            'id_condiciones' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $condicion = Condicion::find($request->input('id_condiciones'));
        if (is_null($condicion)) {
            return $this->sendError('La condición indicada no existe');
        }

        $condicion_evento_search = $this->condicion_evento_search($request->input('id_evento'), $request->input('id_condiciones'));

        if(count($condicion_evento_search) != 0){
           return $this->sendError('Condicion por evento ya existe'); 
        }

        $condicion_evento = CondicionesEvento::create($request->all());        
        return $this->sendResponse($condicion_evento->toArray(), 'Condicion por evento creado con éxito');
    }

    /**
     * Lista las condiciones por un evento en especifico 
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\CondicionesEvento  $condicionesEvento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $condicion_evento = CondicionesEvento::with("evento")->with("condicion")->where('id_evento','=',$id)->get();
        if (count($condicion_evento) == 0) {
            return $this->sendError('El evento no contiene condiciones asociadas');
        }
        return $this->sendResponse($condicion_evento->toArray(), 'Condiciones por evento devueltas con éxito');
    }

    

    /**
     * Actualiza un elemeto de la tabla condiciones_evento 
     *
     * [Se filtra por el ID del evento]
     *@bodyParam id_condicion_old int required Id de la condicion (La cual se quiere editar).
     *@bodyParam id_condicion_new int required Id de la condicion (Id nuevo de la condicion).
     *@response{
     *  "id_condicion_old": 1,
     *  "id_condicion_new": 2,      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CondicionesEvento  $condicionesEvento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [            
            'id_condicion_old' => 'required|integer', 
            'id_condicion_new' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $condicion_evento_search = $this->condicion_evento_search($id, $input['id_condicion_old']);
        if(count($condicion_evento_search) != 0){

            $evento = Evento::find($id);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            $condicion = Condicion::find($input['id_condicion_new']);
            if (is_null($condicion)){
                return $this->sendError('La condición indicada no existe');
            }

            $condicion_evento_search2 = $this->condicion_evento_search($id, $input['id_condicion_new']);
            
            if(count($condicion_evento_search2) != 0){
                return $this->sendError('Condición por evento ya existe'); 
            }

        }else{
             return $this->sendError('No se encuentran condiciones por evento');
        }

        CondicionesEvento::where('id_evento','=',$id)
                            ->where('id_condiciones','=', $input['id_condicion_old'])
                            ->update(['id_condiciones' => $input['id_condicion_new']]);  
        
        $condicion_evento = $this->condicion_evento_search($id, $input['id_condicion_new']);
                            
        return $this->sendResponse($condicion_evento->toArray(), 'Condicion por evento actualizada con éxito');

    }

    /**
     * Elimina los elemento de la tabla condiciones_evento
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\CondicionesEvento  $condicionesEvento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $condicion_evento = CondicionesEvento::where('id_evento','=',$id)->get();
        if (count($condicion_evento) == 0) {
            return $this->sendError('El evento no contiene condiciones asociadas');
        }
        CondicionesEvento::where('id_evento','=',$id)->delete();
        return $this->sendResponse($condicion_evento->toArray(), 'Condiciones por evento eliminadas con éxito');
    }


    public function condicion_evento_search($id_evento, $id_condicion){

        $search = CondicionesEvento::where('id_evento','=',$id_evento)
                                     ->where('id_condiciones','=', $id_condicion)->get();
        return $search;
    }
}
