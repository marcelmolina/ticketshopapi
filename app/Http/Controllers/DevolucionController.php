<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Vent;
use App\Models\Usuario;
use App\Models\DetalleVent;
use App\Models\BoletaEvento;
use App\Models\Preventum;
use App\Models\PalcoEvento;
use App\Models\PuntoVentum;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de Devolución
 *
 * APIs para la gestion de la tabla devolución
 */
class DevolucionController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $devolucion = Devolucion::with('usuario')->with('punto_ventum')->with('detalle_vent')->paginate(15);
        return $this->sendResponse($devolucion->toArray(), 'Devoluciones devueltas con éxito');
    }


    /**
     * Lista de la tabla de todas las ciudades.
     *
     * @return \Illuminate\Http\Response
     */
    public function devolucion_all()
    {
        $devolucion = Devolucion::with('usuario')->with('punto_ventum')->with('detalle_vent')->get();

        return $this->sendResponse($devolucion->toArray(), 'Devoluciones devueltas con éxito');
    }
    
    /**
     * Agrega un nuevo elemento a la tabla devolución
     *
     *@bodyParam fecha date required Fecha de la devolución.
     *@bodyParam id_punto_venta int ID del punto de venta.
     *@bodyParam email_usuario string Email del usuario.
     *@bodyParam identificacion string Identificación.
     *@bodyParam nombre string Nombre del cliente.
     *@bodyParam direccion string Dirección.
     *@bodyParam telefono string Teléfono del cliente.
     *@bodyParam email string Email del cliente.
     *@bodyParam id_venta int required ID de la venta.
     *@bodyParam tipo_identificacion int Tipo de identificación.
     *@bodyParam id_detalle_venta int required ID del detalle de la venta.
     *@bodyParam id_boleta_evento int ID de la boleta evento.
     *@bodyParam id_palco_evento int ID del palco evento.
     * @response {
     *  "fecha": "17/02/2020",
     *  "email_usuario" : null,
     *  "id_punto_venta": 1,
     *  "identificacion": null,
     *  "nombre": "Luis",
     *  "direccion": "Ciudad New",
     *  "telefono": null,
     *  "email": "example@xample.com",
     *  "id_venta": 1,
     *  "tipo_identificacion": null,
     *  "id_detalle_venta": 2,
     *  "id_boleta_evento": 3,
     *  "id_palco_evento": null
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'email_usuario' => 'nullable|string',
            'id_punto_venta' => 'nullable|integer',
            'id_venta' => 'required|integer',
            'id_detalle_venta' => 'required|integer',
            'identificacion' => 'nullable|string',
            'nombre' => 'nullable|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email',
            'tipo_identificacion' => 'nullable',
            'id_boleta_evento' => 'nullable|integer',
            'id_palco_evento' => 'nullable|integer'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(!is_null($request->input('email_usuario'))){
            $user = Usuario::find($request->input('email_usuario'));
            if (is_null($user)) {
                return $this->sendError('Usuario no encontrado');
            }
        }

        if(!is_null($request->input('id_punto_venta'))){
            $punto_venta = PuntoVentum::find($request->input('id_punto_venta'));
            if (is_null($punto_venta)) {
                return $this->sendError('Punto de venta no encontrado');
            }
        }
        
        $venta = Vent::find($request->input('id_venta'));
        if (is_null($venta)) {
            return $this->sendError('Venta no encontrada');
        }

        $detalle = DetalleVent::find($request->input('id_detalle_venta'));
        if (is_null($detalle)) {
            return $this->sendError('Detalle de la venta no encontrado');
        }

        if(!is_null($request->input('id_boleta_evento'))){
            $boleta = BoletaEvento::find($request->input('id_boleta_evento'));
            if (is_null($boleta)) {
                return $this->sendError('Boleta evento no encontrado');
            }
        }

        if(!is_null($request->input('id_palco_evento'))){
            $palco = PalcoEvento::find($request->input('id_palco_evento'));
            if (is_null($palco)) {
                return $this->sendError('Palco evento no encontrado');
            }
        }

        $devolucion = Devolucion::create($request->all());        
        return $this->sendResponse($devolucion->toArray(), 'Devolucion creada con éxito');
        
    }

     /**
     * Lista una devolución en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Devolucion  $id_devolucion
     * @return \Illuminate\Http\Response
     */
    public function show($id_devolucion)
    {
        $devolucion = Devolucion::with('usuario')->with('punto_ventum')->with('detalle_vent')->find($id_devolucion);
        if (is_null($devolucion)) {
            return $this->sendError('Devolución no encontrada');
        }
        return $this->sendResponse($devolucion->toArray(), 'Devolución devuelta con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla devolucion 
     *
     * [Se filtra por el ID]
     *
     *@bodyParam fecha date required Fecha de la devolución.
     *@bodyParam id_punto_venta int ID del punto de venta.
     *@bodyParam email_usuario string Email del usuario.
     *@bodyParam identificacion string Identificación.
     *@bodyParam nombre string Nombre del cliente.
     *@bodyParam direccion string Dirección.
     *@bodyParam telefono string Teléfono del cliente.
     *@bodyParam email string Email del cliente.
     *@bodyParam id_venta int required ID de la venta.
     *@bodyParam tipo_identificacion int Tipo de identificación.
     *@bodyParam id_detalle_venta int required ID del detalle de la venta.
     *@bodyParam id_boleta_evento int ID de la boleta evento.
     *@bodyParam id_palco_evento int ID del palco evento.
     * @response {
     *  "fecha": "17/02/2020",
     *  "email_usuario" : null,
     *  "id_punto_venta": 1,
     *  "identificacion": null,
     *  "nombre": "Luis",
     *  "direccion": "Ciudad New",
     *  "telefono": null,
     *  "email": "example@xample.com",
     *  "id_venta": 1,
     *  "tipo_identificacion": null,
     *  "id_detalle_venta": 2,
     *  "id_boleta_evento": 3,
     *  "id_palco_evento": null
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devolucion  $id_devolucion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_devolucion)
    {
        $validator = Validator::make($request->all(), [
            'fecha' => 'required|date',
            'email_usuario' => 'nullable|string',
            'id_punto_venta' => 'nullable|integer',
            'id_venta' => 'required|integer',
            'id_detalle_venta' => 'required|integer',
            'identificacion' => 'nullable|string',
            'nombre' => 'nullable|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email',
            'tipo_identificacion' => 'nullable',
            'id_boleta_evento' => 'nullable|integer',
            'id_palco_evento' => 'nullable|integer'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(!is_null($request->input('id_boleta_evento')) && !is_null($request->input('id_palco_evento')) ){
            return $this->sendError('Debe especificar si es una boleta o un palco por registro, no ambos.');
        }

        $devolucion = Devolucion::find($id_devolucion);
        if (is_null($devolucion)) {
            return $this->sendError('Devolución no encontrada');
        }
        
        $venta = Vent::find($request->input('id_venta'));
        if (is_null($venta)) {
            return $this->sendError('Venta no encontrada');
        }

        $detalle = DetalleVent::find($request->input('id_detalle_venta'));
        if (is_null($detalle)) {
            return $this->sendError('Detalle de la venta no encontrado');
        }

        if(!is_null($request->input('email_usuario'))){
            $user = Usuario::find($request->input('email_usuario'));
            if (is_null($user)) {
                return $this->sendError('Usuario no encontrado');
            }
        }

        if(!is_null($request->input('id_punto_venta'))){
            $punto_venta = PuntoVentum::find($request->input('id_punto_venta'));
            if (is_null($punto_venta)) {
                return $this->sendError('Punto de venta no encontrado');
            }
        }

        if(!is_null($request->input('id_boleta_evento'))){
            $boleta = BoletaEvento::find($request->input('id_boleta_evento'));
            if (is_null($boleta)) {
                return $this->sendError('Boleta evento no encontrado');
            }

            $devolucion->id_boleta_evento = $request->input('id_boleta_evento');
            $devolucion->id_palco_evento = null;
        }

        if(!is_null($request->input('id_palco_evento'))){
            $palco = PalcoEvento::find($request->input('id_palco_evento'));
            if (is_null($palco)) {
                return $this->sendError('Palco evento no encontrado');
            }

            $devolucion->id_boleta_evento = null;
            $devolucion->id_palco_evento = $request->input('id_palco_evento');
        }

        $input = $request->all();
        $devolucion->fecha = $input['fecha']; 
        $devolucion->email_usuario = $input['email_usuario']; 
        $devolucion->id_punto_venta = $input['id_punto_venta']; 
        $devolucion->id_venta = $input['id_venta']; 
        $devolucion->id_detalle_venta = $input['id_detalle_venta']; 
        $devolucion->identificacion = $input['identificacion'];
        $devolucion->nombre = $input['nombre'];
        $devolucion->direccion = $input['direccion']; 
        $devolucion->telefono = $input['telefono'];
        $devolucion->email = $input['email'];
        $devolucion->tipo_identificacion = $input['tipo_identificacion'];
        

        $devolucion->save();

        return $this->sendResponse($devolucion->toArray(), 'Devolucion actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla devolucion
     *
     * [Se filtra por el ID]
     *

     * @param  \App\Models\Devolucion  $devolucion
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_devolucion)
    {
        try { 

            $devolucion = Devolucion::find($id_devolucion);
            if (is_null($devolucion)) {
                return $this->sendError('Devolucion no encontrado');
            }
            $devolucion->delete();
            return $this->sendResponse($devolucion->toArray(), 'Devolucion eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La devolucion no se puedo eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
