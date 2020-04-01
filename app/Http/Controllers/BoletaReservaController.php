<?php

namespace App\Http\Controllers;

use App\Models\BoletaReserva;
use App\Models\BoletaEvento;
use App\Models\Preventum;
use App\Models\Moneda;
use App\Models\Usuario;
use App\Models\PuntoVentum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;


/**
 * @group Administración de las Boletas Reservadas
 *
 * APIs para la gestion de las  boletas reservadas (Creditos)
 */
class BoletaReservaController extends BaseController
{
    

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

     /**
     * Lista de las boletas reservadas paginadas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boletasreser = BoletaReserva::with('boleta_evento', 'moneda', 'preventum', 'usuario', 'punto_ventum')->paginate(15);
        return $this->sendResponse($boletasreser->toArray(), 'Boletas reservadas devueltas con éxito');
    }


   /**
     * Lista de todas las boletas reservadas.
     *
     * @return \Illuminate\Http\Response
     */
    public function boletasreservadas_all()
    {
        $boletasreser = BoletaReserva::with('boleta_evento', 'moneda', 'preventum', 'usuario', 'punto_ventum')->get();
        return $this->sendResponse($boletasreser->toArray(), 'Boletas reservadas devueltas con éxito');
    }


    

    /**
     * Agrega un nuevo elemento a la tabla Boletas Reservadas.
     *@bodyParam id_boleta int required ID de la boleta evento.
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam precio_venta float Precio venta de la boleta reservada.
     *@bodyParam precio_servicio float Precio servicio de la boleta reservada.     
     *@bodyParam impuesto float Impuesto de la boleta reservada.
     *@bodyParam status int Estatdo de la boleta reservada.
     *@bodyParam email_usuario string Email del usuario (logeado).
     *@bodyParam id_punto_venta int Id del punto de venta.     
     *@bodyParam identificacion int Identificación.
     *@bodyParam razon_nombre string Nombre de la razón.
     *@bodyParam telefono string Teléfono.
     *@bodyParam direccion string Dirección. 
     *@bodyParam email string Email del comprador.
     *@bodyParam email_referido string Email del usuario que refirió la compra.
     *@bodyParam codigo_moneda string required Código de la moneda.
     *@response{
     *    "id_boleta" : 1,
     *    "id_preventa" : null,     
     *    "precio_venta" : 2000,
     *    "precio_servicio" : null,
     *    "impuesto" : null,
     *    "status" : 1,
     *    "email_usuario" : null,
     *    "id_punto_venta" : 1,
     *    "identificacion" : null,
     *    "razon_nombre" : null,
     *    "telefono" : null,
     *    "direccion" : null,
     *    "email" : example@xample.com,
     *    "email_referido" : null,
     *    "codigo_moneda" : "USD",
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_boleta' => 'required|integer',            
            'codigo_moneda' => 'required|string',
            'id_preventa' => 'nullable|integer',
            'precio_venta' => 'nullable|numeric',
            'precio_servicio' => 'nullable|numeric',
            'impuesto' => 'nullable|numeric',
            'status' => 'nullable|integer',
            'email_usuario' => 'nullable|string',
            'id_punto_venta' => 'nullable|integer',
            'identificacion' => 'nullable|string',
            'razon_nombre' => 'nullable|string',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'email' => 'nullable|string',
            'email_referido' => 'nullable|string'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $boletaevento = BoletaEvento::find($request->input('id_boleta'));
        if (is_null($boletaevento)) {
            return $this->sendError('La boleta_evento indicada no existe');
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('Moneda no encontrada');
        }

        if(!is_null($request->input('id_preventa'))){
            $preventa = Preventum::find($request->input('id_preventa'));
            if (is_null($preventa)) {
                return $this->sendError('Preventa no encontrada');
            }
        }

        if(!is_null($request->input('id_punto_venta'))){
            $puntoventa = PuntoVentum::find($request->input('id_punto_venta'));
            if (is_null($puntoventa)) {
                return $this->sendError('Punto venta no encontrado');
            }
        }

        if(!is_null($request->input('email_usuario'))){
            $usuario = Usuario::find($request->input('email_usuario'));
            if (is_null($usuario)) {
                return $this->sendError('Usuario no encontrado');
            }
        }

        $boletarserva = new BoletaReserva();
        $boletarserva->id_boleta = $request->input('id_boleta');
        $boletarserva->codigo_moneda = $request->input('codigo_moneda');
        $boletarserva->id_preventa = $request->input('id_preventa');
        $boletarserva->precio_venta = $request->input('precio_venta');
        $boletarserva->precio_servicio = $request->input('precio_servicio');
        $boletarserva->impuesto = $request->input('impuesto');
        $boletarserva->status = $request->input('status');
        $boletarserva->email_usuario = $request->input('email_usuario');
        $boletarserva->id_punto_venta = $request->input('id_punto_venta');
        $boletarserva->identificacion = $request->input('identificacion');
        $boletarserva->razon_nombre = $request->input('razon_nombre');
        $boletarserva->telefono = $request->input('telefono');
        $boletarserva->direccion = $request->input('direccion');
        $boletarserva->email = $request->input('email');
        $boletarserva->email_referido = $request->input('email_referido');
        $boletarserva->save();

        return $this->sendResponse($boletarserva->toArray(), 'Boleta reservada con éxito');

    }

    /**
     * Lista una boleta reservada en especifico 
     *
     * [Se filtra por el ID de la BoletaEvento]
     *
     * @param  \App\Models\BoletaReserva  $boletaReserva
     * @return \Illuminate\Http\Response
     */
    public function show($id_boleta)
    {
        $boletaevento = BoletaEvento::find($id_boleta);
        if (is_null($boletaevento)) {
            return $this->sendError('La boleta_evento indicada no existe');
        }

        $boletasreser = BoletaReserva::with('boleta_evento', 'moneda', 'preventum', 'usuario', 'punto_ventum', 'abono_reservas')->where('id_boleta', $id_boleta)->get();
        return $this->sendResponse($boletaspre->toArray(), 'Boleta reservada devuelta con éxito');
    }

   

    /**
     * Actualiza un elemento a la tabla BoletaReserva.
     * [Se filtra por el ID de la BoletaEvento]
     *
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam precio_venta float Precio venta de la boleta reservada.
     *@bodyParam precio_servicio float Precio servicio de la boleta reservada.     
     *@bodyParam impuesto float Impuesto de la boleta reservada.
     *@bodyParam status int Estatdo de la boleta reservada.
     *@bodyParam email_usuario string Email del usuario (logeado).
     *@bodyParam id_punto_venta int Id del punto de venta.     
     *@bodyParam identificacion int Identificación.
     *@bodyParam razon_nombre string Nombre de la razón.
     *@bodyParam telefono string Teléfono.
     *@bodyParam direccion string Dirección. 
     *@bodyParam email string Email del comprador.
     *@bodyParam email_referido string Email del usuario que refirió la compra.
     *@bodyParam codigo_moneda string required Código de la moneda.
     *@response{
     *    "id_preventa" : null,
     *    "precio_venta" : 2000,
     *    "precio_servicio" : null,
     *    "impuesto" : null,
     *    "status" : 3,
     *    "email_usuario" : null,
     *    "id_punto_venta" : 1,
     *    "identificacion" : null,
     *    "razon_nombre" : null,
     *    "telefono" : null,
     *    "direccion" : null,
     *    "email" : example@xample.com,
     *    "email_referido" : null,
     *    "codigo_moneda" : "USD",
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BoletaReserva  $id_boleta
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_boleta)
    {
        $validator = Validator::make($request->all(), [
            'codigo_moneda' => 'required|string',
            'id_preventa' => 'nullable|integer',
            'precio_venta' => 'nullable|numeric',
            'precio_servicio' => 'nullable|numeric',
            'impuesto' => 'nullable|numeric',
            'status' => 'nullable|integer',
            'email_usuario' => 'nullable|string',
            'id_punto_venta' => 'nullable|integer',
            'identificacion' => 'nullable|string',
            'razon_nombre' => 'nullable|string',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
            'email' => 'nullable|string',
            'email_referido' => 'nullable|string'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $boletasreser = BoletaReserva::find($id_boleta);
        if(is_null($boletasreser)){
            return $this->sendError('La boleta reservada indicada no existe');
        }
        
        $boletaevento = BoletaEvento::find($id_boleta);
        if (is_null($boletaevento)) {
            return $this->sendError('La boleta_evento indicada no existe');
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('Moneda no encontrada');
        }

        if(!is_null($request->input('id_preventa'))){
            $preventa = Preventum::find($request->input('id_preventa'));
            if (is_null($preventa)) {
                return $this->sendError('Preventa no encontrada');
            }
        }

        if(!is_null($request->input('id_punto_venta'))){
            $puntoventa = PuntoVentum::find($request->input('id_punto_venta'));
            if (is_null($puntoventa)) {
                return $this->sendError('Punto venta no encontrado');
            }
        }

        if(!is_null($request->input('email_usuario'))){
            $usuario = Usuario::find($request->input('email_usuario'));
            if (is_null($usuario)) {
                return $this->sendError('Usuario no encontrado');
            }
        }
        
        $boletasreser->codigo_moneda = $request->input('codigo_moneda');
        $boletasreser->id_preventa = $request->input('id_preventa');
        $boletasreser->precio_venta = $request->input('precio_venta');
        $boletasreser->precio_servicio = $request->input('precio_servicio');
        $boletasreser->impuesto = $request->input('impuesto');
        $boletasreser->status = $request->input('status');
        $boletasreser->email_usuario = $request->input('email_usuario');
        $boletasreser->id_punto_venta = $request->input('id_punto_venta');
        $boletasreser->identificacion = $request->input('identificacion');
        $boletasreser->razon_nombre = $request->input('razon_nombre');
        $boletasreser->telefono = $request->input('telefono');
        $boletasreser->direccion = $request->input('direccion');
        $boletasreser->email = $request->input('email');
        $boletasreser->email_referido = $request->input('email_referido');
        $boletasreser->update();

        
        return $this->sendResponse($boletasreser->toArray(), 'Boletas reservada actualizada con éxito');
    }

    /**
     * Elimina todas las boletasreservadas
     *
     * [Se filtra por el ID de la boleta Evento]
     *
     * @param  \App\Models\BoletaReserva  $id_boleta
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_boleta)
    {
        try {
            $boletasreser = BoletaReserva::find($id_boleta)->get();
            if (is_null($boletasreser)) {
                return $this->sendError('Boleta reservada no encontrada');
            }
            BoletaReserva::find($id_boleta)->delete();
            return $this->sendResponse($boletasreser->toArray(), 'Boleta reservada eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La boleta reservada no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
