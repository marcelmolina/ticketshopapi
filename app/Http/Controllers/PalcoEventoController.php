<?php

namespace App\Http\Controllers;

use App\Models\PalcoEvento;
use App\Models\Evento;
use App\Models\Palco;
use App\Models\Moneda;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de Palco Evento
 *
 * APIs para la gestion de la tabla palco_evento
 */
class PalcoEventoController extends BaseController
{
    /**
     * Lista de la tabla palco_evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $palco_evento = PalcoEvento::with("evento")
                    ->with("palco")
                    ->with("moneda")
                    ->paginate(15);

        return $this->sendResponse($palco_evento->toArray(), 'Palcos de eventos devueltos con éxito');
    }

    
   /**
     * Agrega un nuevo elemento a la tabla palco_evento
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_palco int required ID del puesto.
     *@bodyParam precio_venta float required Precio de venta de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string required Codigo de la moneda.     
     *
     *@response{
     *       "id_evento" : 2,
     *       "id_palco" : 2,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : 0,
     *       "status" : 0,
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento'=> 'required|integer',
            'id_palco' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $palco = Palco::find($request->input('id_palco'));
        if (is_null($palco)) {
            return $this->sendError('El palco indicado no existe');
        }

        if(is_null($request->input('impuesto'))){
            Input::merge(['impuesto' => 0]);
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }

        $palco_evento = PalcoEvento::create($request->all());        
        return $this->sendResponse($palco_evento->toArray(), 'Palco de evento creado con éxito');
    }

    /**
     * Lista un palco de evento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\PalcoEvento  $palcoEvento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $palco_evento = PalcoEvento::with("evento")
                    ->with("palco")
                    ->with("moneda")
                    ->where('id','=',$id)
                    ->get();
        if (count($palco_evento) == 0) {
            return $this->sendError('El palco del evento no se encuentra');
        }
        return $this->sendResponse($palco_evento->toArray(), 'El palco del evento devuelto con éxito');
    }

   
    /**
     * Actualiza un elemento a la tabla palco_evento.
     *
     * [Se filtra por el ID]
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_palco int required ID del puesto.
     *@bodyParam precio_venta float required Precio de venta de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string required Codigo de la moneda.     
     *
     *@response{
     *       "id_evento" : 3,
     *       "id_palco" : 2,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : 0,
     *       "status" : 0,
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PalcoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_evento'=> 'required|integer',
            'id_palco' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'required' 
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($input['id_evento']);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $palco = Palco::find($input['id_palco']);
        if (is_null($palco)) {
            return $this->sendError('El palco indicado no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        $palco_evento_search = PalcoEvento::find($id);
        if (is_null($palco_evento_search)) {
            return $this->sendError('Palco de evento no encontrado');
        }

        if(is_null($input['impuesto'])){
            $palco_evento_search->impuesto  = 0;
        }else{
            $palco_evento_search->impuesto  = $input['impuesto'];
        }

        if(is_null($input['status'])){
            $palco_evento_search->status  = 0;
        }else{
            $palco_evento_search->status  = $input['status'];
        }
        $palco_evento_search->codigo_moneda  = $input['codigo_moneda'];
        $palco_evento_search->id_evento = $input['id_evento'];
        $palco_evento_search->id_puesto = $input['id_puesto'];
        $palco_evento_search->precio_venta = $input['precio_venta'];
        $palco_evento_search->precio_servicio = $input['precio_servicio'];

        $palco_evento_search->save();
        return $this->sendResponse($palco_evento_search->toArray(), 'Palco del evento actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla palco_evento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\PalcoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $palco_evento = PalcoEvento::find($id);
            if (is_null($palco_evento)) {
                return $this->sendError('Palco de evento no encontrado');
            }
            $palco_evento->delete();
            return $this->sendResponse($palco_evento->toArray(), 'Palco de evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El palco de evento no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
