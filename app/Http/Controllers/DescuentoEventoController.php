<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Moneda;
use App\Models\TipoDescuento;
use App\Models\DescuentoEvento;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Administración de Descuento - Evento
 *
 * APIs para la gestion de la tabla asociativa descuento_evento
 */
class DescuentoEventoController extends BaseController
{
    /**
     * Listado de los descuentos por evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evento_descuento = DescuentoEvento::with('evento')
                            ->with('tipo_descuento')
                            ->with('codigo_moneda')
                            ->paginate(15);
        return $this->sendResponse($evento_descuento->toArray(), 'Descuentos por evento devueltos con éxito');
    }

  
    /**
     * Agrega un nuevo elemento a la tabla evento_cuponera
     *
     *@bodyParam nombre string required Nombre.
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam fecha_hora_inicio date Fecha de inicio.
     *@bodyParam fecha_hora_fin date Fecha de finalización.
     *@bodyParam status int Status del descuento. Defaults 0
     *@bodyParam tipo_descuento int required Id del Tipo de Descuento.
     *@bodyParam porcentaje int Porcentaje de descuento.
     *@bodyParam monto double Monto del descuento. Defaults 0
     *@bodyParam cantidad_compra int Cantidad de compra.
     *@bodyParam cantidad_paga int Cantidad de pago.
     *@bodyParam codigo_moneda string required Id de Codigo de Moneda.
     *
     *@response{
     *  "nombre": "Descuento",
     *  "id_evento": 3,
     *  "fecha_hora_inicio" : "2019/05/05",
     *  "fecha_hora_fin" : null,
     *  "status" : 0,
     *  "tipo_descuento": 0,
     *  "porcentaje" : 0,
     *  "monto" : 0,
     *  "cantidad_compra" : null,
     *  "cantidad_paga" : null,
     *  "codigo_moneda" : "USD"      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|max:100', 
            'id_evento' => 'required|integer',
            'tipo_descuento' => 'required|integer',
            'codigo_moneda' => 'required',
            'fecha_hora_inicio' => 'nullable|date|date_format:Y-m-d', 
            'fecha_hora_fin' => 'nullable|date|date_format:Y-m-d',
            'status' => 'nullable|integer',
            'porcentaje' => 'nullable',
            'monto' => 'nullable|integer',
            'cantidad_compra' => 'nullable|integer',
            'cantidad_paga' => 'nullable|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $tipo_des = TipoDescuento::find($request->input('tipo_descuento'));
        if (is_null($tipo_des)) {
            return $this->sendError('El tipo de descuento indicado no existe');
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }

        if(is_null($request->input('tipo_descuento'))){
            Input::merge(['tipo_descuento' => 0]);
        }

        if(is_null($request->input('porcentaje'))){
            Input::merge(['porcentaje' => 0]);
        }

        if(is_null($request->input('monto'))){
            Input::merge(['monto' => 0]);
        }

        $descuento_evento = DescuentoEvento::create($request->all());        
        return $this->sendResponse($descuento_evento->toArray(), 'Descuento del evento creado con éxito');
    }

    /**
     * Lista de un descuento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evento_descuento = DescuentoEvento::with('evento')
                            ->with('tipo_descuento')
                            ->with('codigo_moneda')
                            ->where('id','=',$id)
                            ->get();
        if (count($evento_descuento) == 0) {
            return $this->sendError('El descuento no se encuentra');
        }
        return $this->sendResponse($evento_descuento->toArray(), 'Descuento por evento devuelto con éxito');
    }


    /**
     * Actualiza un elemento a la tabla evento_cuponera
     *
     *@bodyParam nombre string required Nombre.
     *@bodyParam id_evento int required Id del evento.
     *@bodyParam fecha_hora_inicio date Fecha de inicio.
     *@bodyParam fecha_hora_fin date Fecha de finalización.
     *@bodyParam status int Status del descuento. Defaults 0
     *@bodyParam tipo_descuento int required Id del Tipo de Descuento.
     *@bodyParam porcentaje int Porcentaje de descuento.
     *@bodyParam monto double Monto del descuento. Defaults 0
     *@bodyParam cantidad_compra int Cantidad de compra.
     *@bodyParam cantidad_paga int Cantidad de pago.
     *@bodyParam codigo_moneda string required Id de Codigo de Moneda.
     *
     *@response{
     *  "nombre": "Descuento FULL",
     *  "id_evento": 4,
     *  "fecha_hora_inicio" : null,
     *  "fecha_hora_fin" : null,
     *  "status" : 1,
     *  "tipo_descuento": 0,
     *  "porcentaje" : 0,
     *  "monto" : 100,
     *  "cantidad_compra" : null,
     *  "cantidad_paga" : null,
     *  "codigo_moneda" : "USD"      
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DescuentoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required|max:100', 
            'id_evento' => 'required|integer',
            'tipo_descuento' => 'required|integer',
            'codigo_moneda' => 'required',
            'fecha_hora_inicio' => 'nullable|date|date_format:Y-m-d', 
            'fecha_hora_fin' => 'nullable|date|date_format:Y-m-d',
            'status' => 'nullable|integer',
            'porcentaje' => 'nullable',
            'monto' => 'nullable|integer',
            'cantidad_compra' => 'nullable|integer',
            'cantidad_paga' => 'nullable|integer'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $evento = Evento::find($input['id_evento']);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $tipo_des = TipoDescuento::find($input['tipo_descuento']);
        if (is_null($tipo_des)) {
            return $this->sendError('El tipo de descuento indicado no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        $descuento_search = DescuentoEvento::find($id);
        if (is_null($descuento_search)) {
            return $this->sendError('Descuento no encontrado');
        }

        
        if(is_null($input['status'])){
            $descuento_search->status  = 0;
        }else{
            $descuento_search->status  = $input['status'];
        } 

        if(is_null($input['tipo_descuento'])){
            $descuento_search->tipo_descuento  = 0;
        }else{
            $descuento_search->tipo_descuento  = $input['tipo_descuento'];
        }

        if(is_null($input['porcentaje'])){
            $descuento_search->porcentaje  = 0;
        }else{
            $descuento_search->porcentaje  = $input['porcentaje'];
        }

        if(is_null($input['monto'])){
            $descuento_search->monto  = 0;
        }else{
            $descuento_search->monto  = $input['monto'];
        }

        $descuento_search->nombre = $input['nombre'];
        $descuento_search->id_evento = $input['id_evento'];
        $descuento_search->fecha_hora_inicio = $input['fecha_hora_inicio'];
        $descuento_search->fecha_hora_fin = $input['fecha_hora_fin'];
        $descuento_search->cantidad_compra = $input['cantidad_compra'];
        $descuento_search->cantidad_paga = $input['cantidad_paga'];
        $descuento_search->codigo_moneda = $input['codigo_moneda'];
        $descuento_search->save();
        return $this->sendResponse($descuento_search->toArray(), 'Descuento actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla descuento_evento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\DescuentoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $descuento = DescuentoEvento::find($id);
            if (is_null($descuento)) {
                return $this->sendError('Descuento evento no encontrado');
            }
            $descuento->delete();
            return $this->sendResponse($descuento->toArray(), 'Descuento evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El descuento evento no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
