<?php

namespace App\Http\Controllers;

use App\Models\PalcoReserva;
use App\Models\PalcoEvento;
use App\Models\Preventum;
use App\Models\Moneda;
use App\Models\Usuario;
use App\Models\PuntoVentum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Administración de los Palcos Reservados
 *
 * APIs para la gestion de las palcos reservados (Creditos)
 */
class PalcoReservaController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

   /**
     * Lista de los palcos reservados paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $palcosreser = PalcoReserva::with('palco_evento', 'moneda', 'preventum', 'usuario', 'punto_ventum')->paginate(15);
        return $this->sendResponse($palcosreser->toArray(), 'Palcos reservados devueltos con éxito');
    }


    /**
     * Lista de todos los palcos reservados.
     *
     * @return \Illuminate\Http\Response
     */
    public function palcosreservados_all()
    {
        $palcosreser = PalcoReserva::with('palco_evento', 'moneda', 'preventum', 'usuario', 'punto_ventum')->get();
        return $this->sendResponse($palcosreser->toArray(), 'Palcos reservados devueltos con éxito');
    }

  
    /**
     * Agrega un nuevo elemento a la tabla Palcos Reservados.
     *@bodyParam id_palco_evento int required ID de la palcos evento.
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam precio_venta float Precio venta del palco reservado.
     *@bodyParam precio_servicio float Precio servicio del palco reservado.     
     *@bodyParam impuesto float Impuesto del palco reservado.
     *@bodyParam status int Estatdo del palco reservado.
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
     *    "id_palco_evento" : 1,
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
            'id_palco_evento' => 'required|integer',            
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

        $palcosevento = PalcoEvento::find($request->input('id_palco_evento'));
        if (is_null($palcosevento)) {
            return $this->sendError('El palco_evento indicado no existe');
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

        $palcosreser = new PalcoReserva();
        $palcosreser->id_palco_evento = $request->input('id_palco_evento');
        $palcosreser->codigo_moneda = $request->input('codigo_moneda');
        $palcosreser->id_preventa = $request->input('id_preventa');
        $palcosreser->precio_venta = $request->input('precio_venta');
        $palcosreser->precio_servicio = $request->input('precio_servicio');
        $palcosreser->impuesto = $request->input('impuesto');
        $palcosreser->status = $request->input('status');
        $palcosreser->email_usuario = $request->input('email_usuario');
        $palcosreser->id_punto_venta = $request->input('id_punto_venta');
        $palcosreser->identificacion = $request->input('identificacion');
        $palcosreser->razon_nombre = $request->input('razon_nombre');
        $palcosreser->telefono = $request->input('telefono');
        $palcosreser->direccion = $request->input('direccion');
        $palcosreser->email = $request->input('email');
        $palcosreser->email_referido = $request->input('email_referido');
        $palcosreser->save();

        return $this->sendResponse($palcosreser->toArray(), 'Palco reservado con éxito');
    }

    /**
     * Lista un palco reservado en especifico 
     *
     * [Se filtra por el ID de la PalcoEvento]
     *
     * @param  \App\Models\PalcoReserva  $id_palco_evento
     * @return \Illuminate\Http\Response
     */
    public function show($id_palco_evento)
    {
        $palcosevento = PalcoEvento::find($id_palco_evento);
        if (is_null($palcosevento)) {
            return $this->sendError('El palco_evento indicado no existe');
        }

        $palcosreser = PalcoReserva::with('palco_evento', 'moneda', 'preventum', 'usuario', 'punto_ventum', 'abono_reservas')->where('id_palco_evento', $id_palco_evento)->get();
        return $this->sendResponse($palcosreser->toArray(), 'Palco reservado devuelto con éxito');
    }

  

    /**
     * Actualiza un elemento a la tabla PalcoReserva.
     * [Se filtra por el ID del PalcoEvento]
     *
     *@bodyParam id_palco_evento int required ID de la palcos evento.
     *@bodyParam id_preventa int Id de la preventa.
     *@bodyParam precio_venta float Precio venta del palco reservado.
     *@bodyParam precio_servicio float Precio servicio del palco reservado.     
     *@bodyParam impuesto float Impuesto del palco reservado.
     *@bodyParam status int Estatdo del palco reservado.
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
     *    "id_preventa" : 1,     
     *    "precio_venta" : 2000,
     *    "precio_servicio" : null,
     *    "impuesto" : null,
     *    "status" : 2,
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
     * @param  \App\Models\PalcoReserva  $id_palco_evento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_palco_evento)
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

        $palcosreser = PalcoReserva::find($id_palco_evento);
        if(is_null($palcosreser)){
            return $this->sendError('El palco reservado indicada no existe');
        }

        $palcosevento = PalcoEvento::find($id_palco_evento);
        if (is_null($palcosevento)) {
            return $this->sendError('El palco_evento indicado no existe');
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


        $palcosreser->codigo_moneda = $request->input('codigo_moneda');
        $palcosreser->id_preventa = $request->input('id_preventa');
        $palcosreser->precio_venta = $request->input('precio_venta');
        $palcosreser->precio_servicio = $request->input('precio_servicio');
        $palcosreser->impuesto = $request->input('impuesto');
        $palcosreser->status = $request->input('status');
        $palcosreser->email_usuario = $request->input('email_usuario');
        $palcosreser->id_punto_venta = $request->input('id_punto_venta');
        $palcosreser->identificacion = $request->input('identificacion');
        $palcosreser->razon_nombre = $request->input('razon_nombre');
        $palcosreser->telefono = $request->input('telefono');
        $palcosreser->direccion = $request->input('direccion');
        $palcosreser->email = $request->input('email');
        $palcosreser->email_referido = $request->input('email_referido');
        $palcosreser->update();

        
        return $this->sendResponse($palcosreser->toArray(), 'Palco reservado actualizado con éxito');

    }
    
    /**
     * Elimina todos los palcos reservados
     *
     * [Se filtra por el ID del PalcoEvento]
     *
     * @param  \App\Models\PalcoReserva  $id_palco_evento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_palco_evento)
    {
        try {
            $palcosreser = PalcoReserva::find($id_palco_evento)->get();
            if (is_null($palcosreser)) {
                return $this->sendError('Palco reservado no encontrada');
            }
            PalcoReserva::find($id_palco_evento)->delete();
            return $this->sendResponse($palcosreser->toArray(), 'Palco reservado eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El palco reservado no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
