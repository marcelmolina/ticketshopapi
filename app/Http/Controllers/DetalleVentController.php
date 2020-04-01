<?php

namespace App\Http\Controllers;

use App\Models\DetalleVent;
use Illuminate\Http\Request;


/**
 * @group Administración de Detalle Venta
 *
 * APIs para la gestion del detalle de venta
 */
class DetalleVentController extends BaseController
{
    /**
     * Lista de la tabla detalle venta paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $detalle = DetalleVent::with('vent', 'moneda', 'boleta_evento', 'palco_evento')
                            ->paginate(15);

        return $this->sendResponse($detalle->toArray(), 'Detalles de ventas devueltas con éxito');
    }


    /**
     * Lista de la tabla detalle venta.
     *
     * @return \Illuminate\Http\Response
     */
    public function detalleventa_all()
    {
        $detalle = DetalleVent::with('vent', 'moneda', 'boleta_evento', 'palco_evento')
                            ->get();

        return $this->sendResponse($detalle->toArray(), 'Detalles de ventas devueltas con éxito');
    }

    
    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return;
    }

    /**
     * Lista un detalle de venta en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\DetalleVent  $detalleVent
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $detalle = DetalleVent::with('vent', 'moneda', 'boleta_evento', 'palco_evento')->find($id);
        if (is_null($detalle)) {
            return $this->sendError('Detalle de venta no encontrada');
        }
        return $this->sendResponse($detalle->toArray(), 'Detalle de venta devuelta con éxito');
    }

    
    /**
     * 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DetalleVent  $detalleVent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DetalleVent $detalleVent)
    {
        return;
    }

    /**
     * 
     *
     * @param  \App\Models\DetalleVent  $detalleVent
     * @return \Illuminate\Http\Response
     */
    public function destroy(DetalleVent $detalleVent)
    {
        return;
    }
}
