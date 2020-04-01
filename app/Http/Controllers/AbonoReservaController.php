<?php

namespace App\Http\Controllers;

use App\Models\AbonoReserva;
use App\Models\BoletaReserva;
use App\Models\PalcoReserva;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Abono a las Reservas
 *
 * APIs para la gestión de los abonos a las reservas de las boletas o palcos
 */
class AbonoReservaController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
    }

    /**
     * Lista de la tabla de abonos a las reservas paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abonos = AbonoReserva::with("boleta_reserva", "palco_reserva")                    
                  ->paginate(15);

        return $this->sendResponse($abonos->toArray(), 'Abonos a las reservas devueltos con éxito');
    }


    /**
     * Lista de la tabla de abonos a la boleta reservada.
     *
     * @return \Illuminate\Http\Response
     */
    public function abonos_boleta($id_boleto_reserva)
    {
        $abonos = AbonoReserva::with("boleta_reserva")                    
                  ->where('id_boleto_reserva', $id_boleto_reserva)
                  ->get();

        return $this->sendResponse($abonos->toArray(), 'Abonos a las reservas a la boleta devueltas con éxito');
    }

    /**
     * Lista de la tabla de abonos al palco reservado.
     *
     * @return \Illuminate\Http\Response
     */
    public function abonos_palco($id_palco_reserva)
    {
        $abonos = AbonoReserva::with("palco_reserva")                    
                  ->where('id_palco_reserva', $id_palco_reserva)
                  ->get();

        return $this->sendResponse($abonos->toArray(), 'Abonos a las reservas de palco devueltos con éxito');
    }

   

    /**
     * Agrega un nuevo elemento a la tabla AbonoReserva.
     *
     *@bodyParam id_boleto_reserva int ID de la BoletaReservada.
     *@bodyParam id_palco_reserva int ID del PalcoReservado.
     *@bodyParam monto_abono float Monto abonado a la reserva.
     *@bodyParam codigo_moneda string required Codigo de la moneda.
     *@response{
     *    "id_boleto_reserva" : null,
     *    "id_palco_reserva" : 1,
     *    "monto_abono" : 100,
     *    "codigo_moneda" : "USD",
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_boleto_reserva' => 'nullable|integer',            
            'id_palco_reserva' => 'nullable|integer',
            'monto_abono' => 'nullable|numeric',
            'codigo_moneda' => 'required|string'
            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if((!is_null($request->input('id_boleto_reserva')) && !is_null($request->input('id_palco_reserva'))) || (is_null($request->input('id_boleto_reserva')) && is_null($request->input('id_palco_reserva')))){
            return $this->sendError('Debe especificar una boleta reservada o un palco reservado asignada al abono');
        }

        if(!is_null($request->input('id_boleto_reserva'))){
            $boletareservada = BoletaReserva::find($request->input('id_boleto_reserva'));
            if (is_null($boletareservada)) {
                return $this->sendError('La boleta reservada indicada no existe');
            }
        }

        if(!is_null($request->input('id_palco_reserva'))){
            $palcoreserva = PalcoReserva::find($request->input('id_palco_reserva'));
            if (is_null($palcoreserva)) {
                return $this->sendError('El palco reservado indicada no existe');
            }
        }

        $abonos = new AbonoReserva();
        $abonos->id_boleto_reserva = $request->input('id_boleto_reserva');
        $abonos->id_palco_reserva = $request->input('id_palco_reserva');
        $abonos->monto_abono = $request->input('monto_abono');
        $abonos->codigo_moneda = $request->input('codigo_moneda'); 
        $abonos->save();

        return $this->sendResponse($abonos->toArray(), 'Abono registrao con éxito');
    }

    /**
     * Lista un Abono en especifico 
     *
     * @param  \App\Models\AbonoReserva  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $abono = AbonoReserva::with("boleta_reserva", "palco_reserva")->find($id);
        if (is_null($abono)) {
            return $this->sendError('El abono indicado no existe');
        }
        return $this->sendResponse($abono->toArray(), 'Abono devuelto con éxito');
    }

   

   /**
     * Actualiza un elemento a la tabla Abono Reserva.
     * [Se filtra por el ID]
     *
     *@bodyParam id_boleto_reserva int ID de la BoletaReservada.
     *@bodyParam id_palco_reserva int ID del PalcoReservado.
     *@bodyParam monto_abono float Monto abonado a la reserva.
     *@bodyParam codigo_moneda string required Codigo de la moneda.
     *@response{
     *    "id_boleto_reserva" : null,
     *    "id_palco_reserva" : 1,
     *    "monto_abono" : 100,
     *    "codigo_moneda" : "USD",
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AbonoReserva  $abonoReserva
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_boleto_reserva' => 'nullable|integer',            
            'id_palco_reserva' => 'nullable|integer',
            'monto_abono' => 'nullable|numeric',
            'codigo_moneda' => 'required|string'
            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $abono = AbonoReserva::find($id);
        if (is_null($abono)) {
            return $this->sendError('El abono indicado no existe');
        }

        if((!is_null($request->input('id_boleto_reserva')) && !is_null($request->input('id_palco_reserva'))) || (is_null($request->input('id_boleto_reserva')) && is_null($request->input('id_palco_reserva')))){
            return $this->sendError('Debe especificar una boleta reservada o un palco reservado asignada al abono');
        }

        if(!is_null($request->input('id_boleto_reserva'))){
            $boletareservada = BoletaReserva::find($request->input('id_boleto_reserva'));
            if (is_null($boletareservada)) {
                return $this->sendError('La boleta reservada indicada no existe');
            }
        }

        if(!is_null($request->input('id_palco_reserva'))){
            $palcoreserva = PalcoReserva::find($request->input('id_palco_reserva'));
            if (is_null($palcoreserva)) {
                return $this->sendError('El palco reservado indicada no existe');
            }
        }

        
        $abono->id_boleto_reserva = $request->input('id_boleto_reserva');
        $abono->id_palco_reserva = $request->input('id_palco_reserva');
        $abono->monto_abono = $request->input('monto_abono');
        $abono->codigo_moneda = $request->input('codigo_moneda'); 
        $abono->update();

        return $this->sendResponse($abono->toArray(), 'Abono actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla AbonoReserva
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\AbonoReserva  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $abono = AbonoReserva::find($id);
            if (is_null($abono)) {
                return $this->sendError('Abono no encontrado');
            }
            AbonoReserva::find($id)->delete();
            return $this->sendResponse($abono->toArray(), 'Abono de reserva eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El abono de reserva no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
