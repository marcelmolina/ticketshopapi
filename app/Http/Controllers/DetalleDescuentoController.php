<?php

namespace App\Http\Controllers;

use App\Models\DetalleDescuento;
use App\Models\Tribuna;
use App\Models\Localidad;
use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use App\Models\Moneda;
use App\Models\DescuentoEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
/**
 * @group Administración de Detalle de Descuento
 *
 * APIs para la gestion de detalle_descuento
 */
class DetalleDescuentoController extends BaseController
{
    /**
     * Lista de la tabla detalle_descuento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $detalles = DetalleDescuento::with('descuento_evento')
                        ->with('tribuna')
                        ->with('localidad')
                        ->with('boleta_evento')
                        ->with('palco_evento')
                        ->with('moneda')
                        ->paginate(15);
        return $this->sendResponse($detalles->toArray(), 'Detalle de los descuento devueltos con éxito');
    }



    /**
     * Agrega un nuevo elemento a la tabla detalle_descuento
     *
     * Puede ser asignado a un palco del evento o a un boleto no a ambos, si viene solo la       * tribuna va a involucrar el descuento a toda la tribuna, si viene solo localidad debe       * involucrar la tribuna.
     *
     *@bodyParam id_descuento string required ID del descuento.
     *@bodyParam id_tribuna int ID de la tribuna.
     *@bodyParam id_localidad int ID de la localidad.
     *@bodyParam id_evento int ID del evento.
     *@bodyParam id_puesto int required Id del puesto.
     *@bodyParam id_palco int required Id del palco.
     *@bodyParam porcentaje float Porcentaje del descuento.
     *@bodyParam monto float Monto del descuento.
     *@bodyParam status int Estado del descuento.
     *@bodyParam codigo_moneda string required Codigo moneda.    
     *@response{
     *       "id_descuento" : 1,
     *       "id_tribuna" : 1,
     *       "id_localidad": null,
     *       "id_evento": null,
     *       "id_puesto" : null,
     *       "id_palco" : null,
     *       "porcentaje": 5,
     *       "monto": 130000,
     *       "status": 1,
     *       "codigo_moneda": "USD"
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_descuento' => 'required',
            'id_tribuna' => 'nullable|integer',
            'id_localidad' => 'nullable|integer',
            'id_evento' => 'nullable|integer',
            'id_puesto' => 'nullable|integer',
            'id_palco' => 'nullable|integer',
            'porcentaje' => 'nullable|numeric|between:0,9999999.999',
            'monto' => 'nullable|numeric|between:0,9999999.999', 
            'status' => 'nullable|integer', 
            'codigo_moneda' => 'required|string'     
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $detalle_evt = DescuentoEvento::find($request->input('id_descuento'));
        if (is_null($detalle_evt)) {
            return $this->sendError('El descuento evento indicado no existe');
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }

        if(is_null($request->input('status'))){
                Input::merge(['status' => 0]);
            }

        if(is_null($request->input('monto'))){
            Input::merge(['monto' => 0]);
        }

        if(is_null($request->input('porcentaje'))){
            Input::merge(['porcentaje' => 0]);
        }

        $detalle = new DetalleDescuento();

        if($request->input('id_tribuna') != null){

            $tribuna = Tribuna::find($request->input('id_tribuna'));
            if (is_null($tribuna)) {
                return $this->sendError('La tribuna indicada no existe');
            }
            if($request->input('id_localidad') != null){

                $localidad = Localidad::find($request->input('id_localidad'));
                if (is_null($localidad)) {
                    return $this->sendError('La localidad indicada no existe');
                }

            }

            $detalle->id_tribuna = $request->input('id_tribuna');
            $detalle->id_localidad = $request->input('id_localidad');
            $detalle->id_boleta_evento = null;
            $detalle->id_palco_evento = null;
        }


        if($request->input('id_evento') != null && $request->input('id_puesto') != null){
            
            $boleta_evento = BoletaEvento::where('id_evento',$request->input('id_evento'))
                                        ->where('id_puesto',$request->input('id_puesto'))
                                        ->get();
            if (is_null($boleta_evento)) {
                return $this->sendError('La boleta evento indicada no existe');
            }

            $detalle->id_tribuna = null;
            $detalle->id_localidad = null;
            $detalle->id_palco_evento = null;
            $detalle->id_boleta_evento = $boleta_evento->id;

        }

        
        if($request->input('id_evento') != null && $request->input('id_palco') != null){
            
            $palco_evento = PalcoEvento::where('id_evento',$request->input('id_evento'))
                                    ->where('id_palco',$request->input('id_palco'))
                                    ->get();
            if (is_null($palco_evento)) {
                return $this->sendError('El palco evento indicado no existe');
            }

            $detalle->id_tribuna = null;
            $detalle->id_localidad = null;
            $detalle->id_boleta_evento = null;
            $detalle->id_palco_evento = $palco_evento->id;

        }
        
        $detalle->id_descuento = $request->input('id_descuento');
        $detalle->codigo_moneda = $request->input('codigo_moneda');
        $detalle->status = $request->input('status');
        $detalle->monto = $request->input('monto');
        $detalle->porcentaje = $request->input('porcentaje');
        
        $detalle->save();
          
        return $this->sendResponse($detalle->toArray(), 'Detalle creado con éxito');
        
    }

    /**
     * Lista un detalle en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\DetalleDescuento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detalle = DetalleDescuento::with('descuento_evento')
                        ->with('tribuna')
                        ->with('localidad')
                        ->with('boleta_evento')
                        ->with('palco_evento')
                        ->with('moneda')
                        ->find($id);                        
        if (is_null($detalle)) {
            return $this->sendError('Detalle no encontrado');
        }
        return $this->sendResponse($detalle->toArray(), 'Detalle devuelto con éxito');
    }

 

    /**
     * Actualiza un elemento a la tabla detalle_descuento.
     *
     * Puede ser asignado a un palco del evento o a un boleto no a ambos, si viene solo la       * tribuna va a involucrar el descuento a toda la tribuna, si viene solo localidad debe       * involucrar la tribuna.
     *
     *@bodyParam id_descuento string required ID del descuento.
     *@bodyParam id_tribuna int ID de la tribuna.
     *@bodyParam id_localidad int ID de la localidad.
     *@bodyParam id_boleta_evento int ID de la boleta_evento.
     *@bodyParam id_palco_evento int required Id del palco_evento.
     *@bodyParam porcentaje float Porcentaje del descuento.
     *@bodyParam monto float Monto del descuento.
     *@bodyParam status int Estado del descuento.
     *@bodyParam codigo_moneda string required Codigo moneda.    
     *@response{
     *       "id_descuento" : 1,
     *       "id_tribuna" : null,
     *       "id_localidad": null,
     *       "id_boleta_evento": null,
     *       "id_palco_evento" : 1,
     *       "porcentaje": 5,
     *       "monto": 130000,
     *       "status": 1,
     *       "codigo_moneda": "USD"
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DetalleDescuento  $detalleDescuento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_descuento' => 'required',
            'id_tribuna' => 'nullable|integer',
            'id_localidad' => 'nullable|integer',
            'id_boleta_evento' => 'nullable|integer',
            'id_palco_evento' => 'nullable|integer',
            'porcentaje' => 'nullable|numeric|between:0,9999999.999',
            'monto' => 'nullable|numeric|between:0,9999999.999', 
            'status' => 'nullable|integer', 
            'codigo_moneda' => 'required|string'    
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $detalle_descuento_search = DetalleDescuento::find($id);
        if (is_null($detalle_descuento_search)) {
            return $this->sendError('Detalle descuento no encontrado');
        }

        $detalle_evt = DescuentoEvento::find($input['id_descuento']);
        if (is_null($detalle_evt)) {
            return $this->sendError('El descuento evento indicado no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicada no existe');
        }


        if($input['id_tribuna'] != null){

            $tribuna = Tribuna::find($input['id_tribuna']);
            if (is_null($tribuna)) {
                return $this->sendError('La tribuna indicada no existe');
            }
            if($input['id_localidad'] != null){

                $localidad = Localidad::find($input['id_localidad']);
                if (is_null($localidad)) {
                    return $this->sendError('La localidad indicada no existe');
                }

            }

            $detalle_descuento_search->id_boleta_evento = null;
            $detalle_descuento_search->id_palco_evento = null;
            $detalle_descuento_search->id_localidad = $input['id_localidad'];
            $detalle_descuento_search->id_tribuna = $input['id_tribuna'];

        }

        if($input['id_boleta_evento'] != null){

            $boleta_evento = BoletaEvento::find($input['id_boleta_evento']);
            
            if (is_null($boleta_evento)) {
                return $this->sendError('La boleta evento indicada no existe');
            }

            $detalle_descuento_search->id_localidad = null;
            $detalle_descuento_search->id_tribuna = null;
            $detalle_descuento_search->id_palco_evento = null;
            $detalle_descuento_search->id_boleta_evento = $input['id_boleta_evento'];
        }

        if($input['id_palco_evento'] != null){

            $palco_evento = PalcoEvento::find($input['id_palco_evento']);
                                
            if (is_null($palco_evento)) {
                return $this->sendError('El palco evento indicado no existe');
            }

            $detalle_descuento_search->id_localidad = null;
            $detalle_descuento_search->id_tribuna = null;
            $detalle_descuento_search->id_boleta_evento = null;
            $detalle_descuento_search->id_palco_evento = $input['id_palco_evento'];
        }


        $detalle_descuento_search->id_descuento = $input['id_descuento'];
        $detalle_descuento_search->porcentaje = (is_null($input['porcentaje'])) ? 0 : $input['porcentaje'];
        $detalle_descuento_search->monto = (is_null($input['monto'])) ? 0 : $input['monto'];
        $detalle_descuento_search->status = (is_null($input['status'])) ? 0 : $input['status'];
        $detalle_descuento_search->codigo_moneda = $input['codigo_moneda'];

        $detalle_descuento_search->save();
        return $this->sendResponse($detalle_descuento_search->toArray(), 'Deatlle del desc actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla detalle_descuento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\DetalleDescuento  $detalleDescuento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       try { 

            $detalle = DetalleDescuento::find($id);
            if (is_null($detalle)) {
                return $this->sendError('Detalle no encontrado');
            }
            $detalle->delete();
            return $this->sendResponse($detalle->toArray(), 'Detalle eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El detalle no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
