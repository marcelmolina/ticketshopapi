<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\PuntoVentum;
use App\Models\PuntoventaEvento;
use Illuminate\Support\Facades\Input;
use Validator;
/**
 * @group Administración de Punto de Venta Evento
 *
 * APIs para la gestion de la tabla puntoventa_evento
 */
class PuntoventaEventoController extends BaseController
{
    /**
     * Listado de los puntos de venta por evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $puntoventa_evento = PuntoventaEvento::with('punto_ventum')
                            ->with('evento')
                            ->paginate(15);
        return $this->sendResponse($puntoventa_evento->toArray(), 'Puntos de venta por eventos devueltos con éxito');
    }


    /**
     * Agrega un nuevo elemento a la tabla puntoventa_evento 
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam id_puntoventa int required Id del punto de venta. 
     * @response {
     *  "id_evento": 1,
     *  "id_puntoventa": 1,      
     * }  
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento' => 'required|integer', 
            'id_puntoventa' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

         $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $pto_venta = PuntoVentum::find($request->input('id_puntoventa'));
        if (is_null($pto_venta)) {
            return $this->sendError('El punto de venta indicado no existe');
        }

        $ptoventa_event_search = PuntoventaEventoController::ptoventa_event_search($request->input('id_evento'), $request->input('id_puntoventa'));

        if(count($ptoventa_event_search) != 0){
           return $this->sendError('Punto de venta por evento ya existe'); 
        }

        $ptoventa_event = PuntoventaEvento::create($request->all());        
        return $this->sendResponse($ptoventa_event->toArray(), 'Punto de venta por evento creado con éxito');
    }

    /**
     * Lista de los punto de venta por evento en especifico
     * [Se filtra por el ID del evento]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ptoventa_event = PuntoventaEvento::where('id_evento','=',$id)->get();
        if (count($ptoventa_event) == 0) {
            return $this->sendError('Puntos de venta por evento no encontrados');
        }
        return $this->sendResponse($ptoventa_event->toArray(), 'Puntos de venta por evento devueltos con éxito');
    }


    /**
     * Actualiza un elemeto de la tabla puntoventa_evento 
     *
     * [Se filtra por el ID del evento]
     *@bodyParam id_puntoventa_old int required Id del punto de venta (El cual se quiere editar).
     *@bodyParam id_puntoventa_new int required Id del punto de venta (Id nuevo de la cuponera).
     * @response {
     *  "id_puntoventa_old": 1,
     *  "id_puntoventa_new": 2,      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [            
            'id_puntoventa_old' => 'required|integer', 
            'id_puntoventa_new' => 'required|integer',     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $ptoventa_event_search = PuntoventaEventoController::ptoventa_event_search($id, $input['id_puntoventa_old']);
        if(count($ptoventa_event_search) != 0){

            $evento = Evento::find($id);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            $pto_venta = PuntoVentum::find($input['id_puntoventa_new']);
            if (is_null($pto_venta)){
                return $this->sendError('El punto de venta indicado no existe');
            }

            $ptoventa_event_search2 = PuntoventaEventoController::ptoventa_event_search($id, $input['id_puntoventa_new']);
            
            if(count($ptoventa_event_search2) != 0){
                return $this->sendError('Punto de venta por evento ya existe'); 
            }

        }else{
            return $this->sendError('Puntos de venta por evento no se encuentra');
        }

        PuntoventaEvento::where('id_evento','=',$id)
                            ->where('id_puntoventa','=', $input['id_puntoventa_old'])
                            ->update(['id_puntoventa' => $input['id_puntoventa_new']]);  
        
        $ptoventa_event = PuntoventaEventoController::ptoventa_event_search($id, $input['id_puntoventa_new']);
                            
        return $this->sendResponse($ptoventa_event->toArray(), 'Punto de venta por evento actualizado con éxito');
    }

    /**
     * Elimina todos los elemento de la tabla puntoventa_evento
     *
     * [Se filtra por el ID del evento]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $ptoventa_event = PuntoventaEvento::where('id_evento','=',$id)->get();
        if (count($ptoventa_event) == 0) {
            return $this->sendError('Puntos de venta por evento no encontrados');
        }
        PuntoventaEvento::where('id_evento','=',$id)->delete();
        return $this->sendResponse($ptoventa_event->toArray(), 'Puntos de venta por evento eliminados con éxito');
    }

    public function ptoventa_event_search($id_evento, $id_puntoventa){

        $search = PuntoventaEvento::where('id_evento','=',$id_evento)
                                     ->where('id_puntoventa','=', $id_puntoventa)->get();
        return $search;
    }
}
