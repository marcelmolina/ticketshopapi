<?php

namespace App\Http\Controllers;

use App\Models\BoletaEvento;
use App\Models\Evento;
use App\Models\Moneda;
use App\Models\Puesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;


/**
 * @group Administración de Boleta Evento
 *
 * APIs para la gestion de la tabla boleta_evento
 */
class BoletaEventoController extends BaseController
{
    /**
     * Lista de la tabla boleta_evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boleta_evento = BoletaEvento::with("evento")
                    ->with("puesto")
                    ->with("codigo_moneda")
                    ->paginate(15);

        return $this->sendResponse($boleta_evento->toArray(), 'Boletas de los eventos devueltos con éxito');
    }

  
    /**
     * Agrega un nuevo elemento a la tabla boleta_evento
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_puesto int required ID del puesto.
     *@bodyParam precio_venta float required Precio de venta de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string Codigo de la moneda.     
     *
     *@response{
     *       "id_evento" : 2,
     *       "id_puesto" : 2,
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
            'id_puesto' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'nullable'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $puesto = Puesto::find($request->input('id_puesto'));
        if (is_null($puesto)) {
            return $this->sendError('El puesto indicado no existe');
        }

        if(!is_null($request->input('codigo_moneda'))){
            $moneda = Moneda::find($request->input('codigo_moneda'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicado no existe');
            }
        }else{
            Input::merge(['codigo_moneda' => null]);
        }

        if(is_null($request->input('impuesto'))){
            Input::merge(['impuesto' => 0]);
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }

        
        $boleta_evento = BoletaEvento::create($request->all());        
        return $this->sendResponse($boleta_evento->toArray(), 'Boleta del evento creada con éxito');
    }

    /**
     * Lista una boleta de evento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\BoletaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $boleta_evento = BoletaEvento::with("evento")
                    ->with("puesto")
                    ->with("codigo_moneda")
                    ->where('id','=',$id)
                    ->get();
        if (count($boleta_evento) == 0) {
            return $this->sendError('La boleta de evento no se encuentra');
        }
        return $this->sendResponse($boleta_evento->toArray(), 'La boleta del evento devuelta con éxito');
    }


    /**
     * Actualiza un elemento a la tabla boleta_evento.
     *
     * [Se filtra por el ID]
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_puesto int required ID del puesto.
     *@bodyParam precio_venta float required Precio de eventa de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string Codigo de la moneda.     
     *
     *@response{
     *       "id_evento" : 2,
     *       "id_puesto" : 2,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : null,
     *       "status" : null,
     *       "codigo_moneda" : "USD"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BoletaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_evento'=> 'required|integer',
            'id_puesto' => 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'nullable' 
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($input['id_evento']);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $puesto = Puesto::find($input['id_puesto']);
        if (is_null($puesto)) {
            return $this->sendError('El puesto indicado no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        $boleta_evento_search = BoletaEvento::find($id);
        if (is_null($boleta_evento_search)) {
            return $this->sendError('Boleta de evento no encontrado');
        }


        if(is_null($input['impuesto'])){
            $boleta_evento_search->impuesto  = 0;
        }else{
            $boleta_evento_search->impuesto  = $input['impuesto'];
        }

        if(is_null($input['status'])){
            $boleta_evento_search->status  = 0;
        }else{
            $boleta_evento_search->status  = $input['status'];
        }

        if(is_null($input['codigo_moneda'])){
            $boleta_evento_search->codigo_moneda  = null;
        }else{
            $boleta_evento_search->codigo_moneda  = $input['codigo_moneda'];
        }

        $boleta_evento_search->id_evento = $input['id_evento'];
        $boleta_evento_search->id_puesto = $input['id_puesto'];
        $boleta_evento_search->precio_venta = $input['precio_venta'];
        $boleta_evento_search->precio_servicio = $input['precio_servicio'];

        $boleta_evento_search->save();
        return $this->sendResponse($boleta_evento_search->toArray(), 'Boleta del evento actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla boleta_evento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\BoletaEvento  $boletaEvento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $boleta_evento = BoletaEvento::find($id);
            if (is_null($boleta_evento)) {
                return $this->sendError('Boleta del evento no encontrado');
            }
            $boleta_evento->delete();
            return $this->sendResponse($boleta_evento->toArray(), 'Boleta del evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La boleta del evento no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
