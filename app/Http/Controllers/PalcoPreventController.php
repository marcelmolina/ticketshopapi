<?php

namespace App\Http\Controllers;

use App\Models\PalcoPrevent;
use App\Models\Moneda;
use App\Models\Preventum;
use App\Models\PalcoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Administración de Palco Preventa
 *
 * APIs para la gestion de la tabla palco_prevent
 */
class PalcoPreventController extends BaseController
{
    /**
     * Lista de la tabla palco_prevent.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $palco_prevent = PalcoPrevent::with("palco_evento")
                    ->with("moneda")
                    ->with("preventum")
                    ->paginate(15);
        return $this->sendResponse($palco_prevent->toArray(), 'Palcos en preventa devueltos con éxito');
    }

  
    /**
     * Agrega un nuevo elemento a la tabla boletas_prevent
     *
     *@bodyParam id_evento int required ID del Evento.
     *@bodyParam id_palco int required ID de la Palco.
     *@bodyParam id_preventa int required ID de la preventa.
     *@bodyParam precio_venta float Precio de venta de la boleta.
     *@bodyParam precio_servicio float Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string required Codigo de la moneda.     
     *
     *@response{
     *       "id_evento" : 2,   
     *       "id_palco" : 2,
     *       "id_preventa" : 2,
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
            'id_palco'=> 'required|integer',
            'id_preventa' => 'required|integer',
            'precio_venta' => 'nullable',
            'precio_servicio' => 'nullable',
            'impuesto' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $palco_evento_search =  PalcoEvento::where('id_evento', $request->input('id_evento'))
                                ->where('id_palco', $request->input('id_palco'))
                                ->first();
        if (!$palco_evento_search) {

            return $this->sendError('El palco por evento indicado no existe');
        }

        $id_palco_evento = $palco_evento_search->id;
        $palco_evento = PalcoEvento::find($id_palco_evento);
        if (is_null($palco_evento)) {
            return $this->sendError('El palco evento indicado no existe');
        }

        $preventa = Preventum::find($request->input('id_preventa'));
        if (is_null($preventa)) {
            return $this->sendError('La preventa indicada no existe');
        }
        
        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }

        $palco_preventa = new PalcoPrevent;

        if(is_null($request->input('precio_venta'))){
            Input::merge(['precio_venta' => 0]);
        }

        if(is_null($request->input('precio_servicio'))){
            Input::merge(['precio_servicio' => 0]);
        }

        if(is_null($request->input('impuesto'))){
            Input::merge(['impuesto' => 0]);
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }


        $palco_preventa->id_palco_evento = $id_palco_evento;
        $palco_preventa->id_preventa = $request->input('id_preventa');
        $palco_preventa->precio_venta = $request->input('precio_venta');
        $palco_preventa->precio_servicio = $request->input('precio_servicio');
        $palco_preventa->impuesto = $request->input('impuesto');
        $palco_preventa->status = $request->input('status');
        $palco_preventa->codigo_moneda = $request->input('codigo_moneda');

        $palco_preventa->save();
              
        return $this->sendResponse($palco_preventa->toArray(), 'Palco en preventa creado con éxito');

    }

    /**
     * Lista un palco en preventa en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\PalcoPrevent  $palcoPrevent
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $palco_preventa = PalcoPrevent::with("palco_evento")
                    ->with("moneda")
                    ->with("preventum")
                    ->where('id_palco_evento','=',$id)
                    ->get();
        if (count($palco_preventa) == 0) {
            return $this->sendError('Palco en preventa no se encuentra');
        }
        return $this->sendResponse($palco_preventa->toArray(), 'Palco en preventa devuelto con éxito');
    }


    /**
     * Actualiza un elemento a la tabla palco_prevent
     * [Se filtra por el ID del Palco - Evento]
     *@bodyParam id_preventa int required ID de la preventa.
     *@bodyParam precio_venta float Precio de venta de la boleta.
     *@bodyParam precio_servicio float Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string required Codigo de la moneda.     
     *
     *@response{
     *       "id_preventa" : 1,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : 10,
     *       "status" : 0,
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PalcoPrevent  $palcoPrevent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_preventa' => 'required|integer',
            'precio_venta' => 'nullable',
            'precio_servicio' => 'nullable',
            'impuesto' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $palco_evento = PalcoEvento::find($id);
        if (is_null($palco_evento)) {
            return $this->sendError('El palco evento indicado no existe');
        }

        $preventum = Preventum::find($input['id_preventa']);
        if (is_null($preventum)) {
            return $this->sendError('La preventa indicada no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        $palco_preventa_search = PalcoPrevent::find($id);
        if (is_null($palco_preventa_search)) {
            return $this->sendError('Palco en preventa no encontrado');
        }

        if(is_null($input['impuesto'])){
            $palco_preventa_search->impuesto  = 0;
        }else{
            $palco_preventa_search->impuesto  = $input['impuesto'];
        }

        if(is_null($input['precio_servicio'])){
            $palco_preventa_search->precio_servicio  = 0;
        }else{
            $palco_preventa_search->precio_servicio  = $input['precio_servicio'];
        }

        if(is_null($input['precio_venta'])){
            $palco_preventa_search->precio_venta  = 0;
        }else{
            $palco_preventa_search->precio_venta  = $input['precio_venta'];
        }

        if(is_null($input['status'])){
            $palco_preventa_search->status  = 0;
        }else{
            $palco_preventa_search->status  = $input['status'];
        }


        $palco_preventa_search->id_preventa = $input['id_preventa'];
        $palco_preventa_search->codigo_moneda = $input['codigo_moneda'];

        $palco_preventa_search->save();
        return $this->sendResponse($palco_preventa_search->toArray(), 'Palco en preventa actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla palco_prevent
     *
     * [Se filtra por el ID del Palco - Evento]
     *
     * @param  \App\Models\PalcoPrevent  $palcoPrevent
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $palco_preventa = PalcoPrevent::find($id);
            if (is_null($palco_preventa)) {
                return $this->sendError('Palco en preventa no encontrado');
            }
            $palco_preventa->delete();
            return $this->sendResponse($palco_preventa->toArray(), 'Palco en preventa eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El palco en preventa no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
