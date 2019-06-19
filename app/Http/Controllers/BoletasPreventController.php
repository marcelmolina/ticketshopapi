<?php

namespace App\Http\Controllers;

use App\Models\BoletasPrevent;
use App\Models\BoletaEvento;
use App\Models\Preventum;
use App\Models\Moneda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;


/**
 * @group Administración de Boletas Preventa
 *
 * APIs para la gestion de la tabla boletas_prevent
 */
class BoletasPreventController extends BaseController
{
     /**
     * Lista de la tabla boletas_prevent.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boleta_preventa = BoletasPrevent::with("boleta_evento")
                    ->with("moneda")
                    ->with("preventum")
                    ->paginate(15);
        return $this->sendResponse($boleta_preventa->toArray(), 'Boletas de la preventa devueltas con éxito');
    }

  

    /**
     * Agrega un nuevo elemento a la tabla boletas_prevent
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_puesto int required ID del puesto.
     *@bodyParam id_preventa int required ID de la preventa.
     *@bodyParam precio_venta float required Precio de venta de la boleta.
     *@bodyParam precio_servicio float Precio del servicio.
     *@bodyParam impuesto float required Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string required Codigo de la moneda.     
     *
     *@response{
     *       "id_evento" : 3,
     *       "id_puesto" : 3,
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
            'id_puesto'=> 'required|integer',
            'id_preventa' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'nullable',
            'impuesto' => 'required',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $boleta_evento_search =  BoletaEvento::where('id_evento', $request->input('id_evento'))
                                ->where('id_puesto', $request->input('id_puesto'))
                                ->first();
        if (!$boleta_evento_search) {

            return $this->sendError('La boleta del evento indicada no existe');
        }

        $id_boleta = $boleta_evento_search->id;

        $boleta_preventa_search = BoletasPrevent::find($id_boleta);
        if (!is_null($boleta_preventa_search)) {
            return $this->sendError('Boleta de la preventa ya se encuentra asignada');
        }
        

        $preventa = Preventum::find($request->input('id_preventa'));
        if (is_null($preventa)) {
            return $this->sendError('La preventa indicada no existe');
        }
        
        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }

        if(is_null($request->input('precio_servicio'))){
            Input::merge(['precio_servicio' => 0]);
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }


        $boleta_preventa = new BoletasPrevent;

        $boleta_preventa->id_boleta = $id_boleta;
        $boleta_preventa->id_preventa = $request->input('id_preventa');
        $boleta_preventa->precio_venta = $request->input('precio_venta');
        $boleta_preventa->precio_servicio = $request->input('precio_servicio');
        $boleta_preventa->impuesto = $request->input('impuesto');
        $boleta_preventa->status = $request->input('status');
        $boleta_preventa->codigo_moneda = $request->input('codigo_moneda');

        $boleta_preventa->save();

              
        return $this->sendResponse($boleta_preventa->toArray(), 'Boleta de la preventa creada con éxito');

    }

    /**
     * Lista una boleta de la preventa en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\BoletasPrevent  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $boleta_preventa = BoletasPrevent::with("boleta_evento")
                    ->with("moneda")
                    ->with("preventum")
                    ->where('id_boleta','=',$id)
                    ->get();
        if (count($boleta_preventa) == 0) {
            return $this->sendError('Boleta de la preventa no se encuentra');
        }
        return $this->sendResponse($boleta_preventa->toArray(), 'Boleta de la preventa devuelta con éxito');
    }

    
    /**
     * Actualiza un elemento a la tabla boletas_prevent.
     *
     * [Se filtra por el ID de la boleta_evento]
     *@bodyParam id_preventa int required ID de la prevent.
     *@bodyParam precio_venta float required Precio de eventa de la boleta del evento.
     *@bodyParam precio_servicio float Precio del servicio.
     *@bodyParam impuesto float required Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string required Codigo de la moneda.     
     *
     *@response{            
     *       "id_preventa" : 2,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : 0,
     *       "status" : null,
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BoletasPrevent  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [            
            'id_preventa' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'nullable',
            'impuesto' => 'required',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'required' 
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $preventa = Preventum::find($input['id_preventa']);
        if (is_null($preventa)) {
            return $this->sendError('La preventa indicada no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }

        $boleta_preventa_search = BoletasPrevent::find($id);
        if (is_null($boleta_preventa_search)) {
            return $this->sendError('Boleta de la preventa no encontrado');
        }

        if(is_null($input['status'])){
            $boleta_preventa_search->status  = 0;
        }else{
            $boleta_preventa_search->status  = $input['status'];
        }

        if(is_null($input['precio_servicio'])){
            $boleta_preventa_search->precio_servicio  = 0;
        }else{
            $boleta_preventa_search->precio_servicio  = $input['precio_servicio'];
        }

        $boleta_preventa_search->impuesto = $input['impuesto'];
        $boleta_preventa_search->id_preventa = $input['id_preventa'];
        $boleta_preventa_search->precio_venta = $input['precio_venta'];
        $boleta_preventa_search->precio_servicio = $input['precio_servicio'];

        $boleta_preventa_search->save();
        return $this->sendResponse($boleta_preventa_search->toArray(), 'Boleta de la preventa actualizada con éxito');
    }

    
    /**
     * Elimina un elemento de la tabla boletas_prevent
     *
     * [Se filtra por el ID boleta_evento]
     *
     * @param  \App\Models\BoletasPrevent $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $boleta_preventa = BoletasPrevent::find($id);
            if (is_null($boleta_preventa)) {
                return $this->sendError('Boleta de la preventa no encontrada');
            }
            $boleta_preventa->delete();
            return $this->sendResponse($boleta_preventa->toArray(), 'Boleta de la preventa eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La boleta de la preventa no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
