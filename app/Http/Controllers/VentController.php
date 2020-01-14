<?php

namespace App\Http\Controllers;

use App\Models\Vent;
use App\Models\DetalleVent;
use App\Models\BoletaEvento;
use App\Models\Preventum;
use App\Models\PalcoEvento;
use App\Models\PuntoVentum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Validator;
use stdClass;

class VentController extends BaseController
{
   

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['show', 'obtener_refventa', 'generarsha']]);        
    }
    

    public function generarsha(Request $request){

        $validator = Validator::make($request->all(), [          
            'refventa' => 'required|string',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $info_pago = new stdClass();
       
        $info_pago->usuarioId = 1963; // TicketShop
        $info_pago->claveSecretausuarioID = '2cff6cd4b00c7ace4b2615bb99db4511'; // TicketShop

        $info_pago->Moneda=$request->Moneda;
        $info_pago->refVenta= $request->input('refventa');
        $info_pago->URLRespuesta='http://18.209.56.187/paymentconfirm';
        $info_pago->URLConfirma = 'http://api.ticketshop.com.co/api/payment_confirm';

        $info_pago->firma_codificada = openssl_digest( $info_pago->usuarioId . $info_pago->refVenta . $info_pago->Moneda . $info_pago->claveSecretausuarioID, 'sha512' );

        return $this->sendResponse(get_object_vars($info_pago), 'Cliente devuelto con éxito');
    }


    public function obtener_refventa(Request $request)
    {
        $validator = Validator::make($request->all(), [          
            'boletas' => 'nullable|array',
            'boletas.*' => 'integer',
            'palcos' => 'nullable|array',
            'palcos.*' => 'integer',
            'id_punto_venta' => 'nullable|integer',
            'tipo_venta' => 'nullable|string',
            'identificacion' => 'nullable|string',
            'nombre' => 'nullable|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
            'email' => 'nullable|email',
            'tipo_identidicacion' => 'nullable'

        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(!is_null($request->input('id_punto_venta'))){
            $punto_venta = PuntoVentum::find($request->input('id_punto_venta'));
            if (is_null($punto_venta)) {
                return $this->sendError('Punto de venta no encontrado');
            }
        }

        $array_boletas = $request->input('boletas');
        $array_palcos = $request->input('palcos');

        if(is_null($array_boletas) && is_null($array_palcos)){
            return $this->sendError('Debe proporcionar boletas o palcos');
        }

       

        $email_user_log = auth()->user()->email;
        $venta = new Vent();
        $venta->email_usuario = $email_user_log;
        $venta->id_punto_venta = $request->input('id_punto_venta');
        $venta->tipo_venta = $request->input('tipo_venta');
        $venta->identificacion = $request->input('identificacion');
        $venta->nombre = $request->input('nombre');
        $venta->direccion = $request->input('direccion');
        $venta->telefono = $request->input('telefono');
        $venta->email = $request->input('email');
        $venta->tipo_identidicacion = $request->input('tipo_identidicacion');
        
        $today = Carbon::now();
        $time = explode(" ",$today->toDateTimeString());
        $today = $time[0];
        $time = $time[1];
        $venta->fecha = $today;       

        $venta->token_refventa = md5(microtime().$today); 

        $venta->save();

        $id_venta = $venta->id;

        
        if(!is_null($array_boletas)){
            
            //Calculo de las boletas            

            foreach ($array_boletas as $value_boleta) {

                $precioventa = 0;
                $precioservicio = 0;
                $impuesto = 0;
                $moneda = 0;
                $fijo_descuento = 0;
                $porcent_descuento = 0;
                $porcent_descuento_ser = 0;
                $fijo_descuento_ser = 0;
                $band = false;
                $boleta_v = BoletaEvento::find($value_boleta);               

                $precioventa = $boleta_v->precio_venta;
                $precioservicio = $boleta_v->precio_servicio;
                $impuesto = $boleta_v->impuesto;
                $moneda = $boleta_v->codigo_moneda;
               
                //Descuento para todo el evento
                $preventas = Preventum::where('fecha_inicio','<=',$today)
                                ->where('fecha_fin','>=',$today)
                                ->where('hora_inicio','<=',$time)
                                ->where('hora_fin','>=',$time)
                                ->where('activo',1)
                                ->where('id_evento', $boleta_v->id_evento)
                                ->whereNotNull('id_evento')
                                ->whereNull('id_tribuna')
                                ->whereNull('id_localidad')
                                ->orderBy('fecha_fin', 'DESC')
                                ->orderBy('hora_fin', 'DESC')
                                ->first();               
                if($preventas){                    
                    $porcent_descuento = $preventas->porcentaje_descuento_precio;
                    $fijo_descuento = $preventas->descuento_fijo_precio;

                    $porcent_descuento_ser = $preventas->porcentaje_descuento_servicio;
                    $fijo_descuento_ser = $preventas->descuento_fijo_servicio;

                    $band = true;
                }

                //Descuento para toda la tribuna
                if(!$band){
                   $preventas_tr = Preventum::where('fecha_inicio','<=',$today)
                                    ->where('fecha_fin','>=',$today)
                                    ->where('hora_inicio','<=',$time)
                                    ->where('hora_fin','>=',$time)
                                    ->where('activo',1)
                                    ->whereNotNull('id_tribuna')
                                    ->whereNull('id_evento')
                                    ->whereNull('id_localidad')
                                    ->orderBy('fecha_fin', 'DESC')
                                    ->orderBy('hora_fin', 'DESC')
                                    ->first();

                   
                    
                   if($preventas_tr){ 

                        $boletas_tr = BoletaEvento::find($value_boleta)
                          ->with(['puesto', 'puesto.localidad', 'puesto.localidad.tribuna' =>function($query) use($preventas_tr){
                               
                                $query->where('id', $preventas_tr->id_tribuna);
                            }])                      
                          ->where('id_evento', $boleta_v->id_evento)
                          ->first();
                         
                        if($boletas_tr){
                            $porcent_descuento = $preventas_tr->porcentaje_descuento_precio;
                            $fijo_descuento = $preventas_tr->descuento_fijo_precio;

                            $porcent_descuento_ser = $preventas_tr->porcentaje_descuento_servicio;
                            $fijo_descuento_ser = $preventas_tr->descuento_fijo_servicio;

                            $band = true;
                        }
                   }
                }
                
                //Descuento para toda la localidad
                if(!$band){
                    $preventas_ld = Preventum::where('fecha_inicio','<=',$today)
                                    ->where('fecha_fin','>=',$today)
                                    ->where('hora_inicio','<=',$time)
                                    ->where('hora_fin','>=',$time)
                                    ->where('activo',1)
                                    ->whereNotNull('id_localidad')
                                    ->whereNull('id_evento')
                                    ->whereNull('id_tribuna')
                                    ->orderBy('fecha_fin', 'DESC')
                                    ->orderBy('hora_fin', 'DESC')
                                    ->first();

                    if($preventas_ld){ 

                        $boletas_ld = BoletaEvento::find($value_boleta)
                          ->wherehas('puesto', function($query) use($preventas_ld){
                                $query->wherehas('localidad', function($query) use($preventas_ld){
                                   $query->where('id', $preventas_ld->id_localidad);
                                });                                
                            })                          
                          ->where('id_evento', $boleta_v->id_evento)                      
                          ->first();

                        if($boletas_ld){
                            $porcent_descuento = $preventas_ld->porcentaje_descuento_precio;
                            $fijo_descuento = $preventas_ld->descuento_fijo_precio;

                            $porcent_descuento_ser = $preventas_ld->porcentaje_descuento_servicio;
                            $fijo_descuento_ser = $preventas_ld->descuento_fijo_servicio;


                            $band = true;
                        }
                    }
                }
               
                BoletaEvento::find($value_boleta)->update(['status' => 6]);

                $detalle_venta = new DetalleVent();
                $detalle_venta->id_venta = $id_venta;
                $detalle_venta->id_boleta_evento = $value_boleta;
                $detalle_venta->precio_venta = $precioventa;
                
                $detalle_venta->impuesto = $impuesto;
                $detalle_venta->status = 0;
                $detalle_venta->codigo_moneda = $boleta_v->codigo_moneda;

                if($band){

                    if($porcent_descuento == 0 || is_null($porcent_descuento)){
                       $detalle_venta->monto_descuento =  $fijo_descuento;
                    }
                    else{
                       $detalle_venta->monto_descuento = (($precioventa * $porcent_descuento)/100);
                    }
                
                    if($porcent_descuento_ser == 0 || is_null($porcent_descuento_ser)){
                       $precioservicio =  $fijo_descuento_ser;
                    }else{
                       $precioservicio = (($precioventa * $porcent_descuento_ser)/100);
                    }

                }else{
                    $detalle_venta->monto_descuento = 0;
                }               
                $detalle_venta->precio_servicio = $precioservicio;
                $detalle_venta->save();

            }

        }
            
        if(!is_null($array_palcos)){
            
            //Calculo de las palcos            

            foreach ($array_palcos as $value_palco) {

               $precioventa = 0;
               $precioservicio = 0;
               $impuesto = 0;
               $moneda = 0;
               $fijo_descuento = 0;
               $porcent_descuento = 0;
               $porcent_descuento_ser = 0;
               $fijo_descuento_ser = 0;
               $band = false;
               $palco_v = PalcoEvento::find($value_palco);               

               $precioventa = $palco_v->precio_venta;
               $precioservicio = $palco_v->precio_servicio;
               $impuesto = $palco_v->impuesto;
               $moneda = $palco_v->codigo_moneda;  
             

                //Descuento para todo el evento
                $preventas = Preventum::where('fecha_inicio','<=',$today)
                                ->where('fecha_fin','>=',$today)
                                ->where('hora_inicio','<=',$time)
                                ->where('hora_fin','>=',$time)
                                ->where('activo',1)
                                ->where('id_evento', $palco_v->id_evento)
                                ->whereNotNull('id_evento')
                                ->whereNull('id_tribuna')
                                ->whereNull('id_localidad')
                                ->orderBy('fecha_fin', 'DESC')
                                ->orderBy('hora_fin', 'DESC')
                                ->first();               
                if($preventas){                    
                    $porcent_descuento = $preventas->porcentaje_descuento_precio;
                    $fijo_descuento = $preventas->descuento_fijo_precio;

                    $porcent_descuento_ser = $preventas->porcentaje_descuento_servicio;
                    $fijo_descuento_ser = $preventas->descuento_fijo_servicio;

                    $band = true;
                }

                

                //Descuento para toda la tribuna
                if(!$band){
                   $preventas_tr = Preventum::where('fecha_inicio','<=',$today)
                                    ->where('fecha_fin','>=',$today)
                                    ->where('hora_inicio','<=',$time)
                                    ->where('hora_fin','>=',$time)
                                    ->where('activo',1)
                                    ->whereNotNull('id_tribuna')
                                    ->whereNull('id_evento')
                                    ->whereNull('id_localidad')
                                    ->orderBy('fecha_fin', 'DESC')
                                    ->orderBy('hora_fin', 'DESC')
                                    ->first();

                   
                    
                   if($preventas_tr){ 

                        $palcos_tr = PalcoEvento::find($value_palco)
                          ->wherehas('palco', function($query) use($preventas_tr){
                                $query->wherehas('localidad', function($query) use($preventas_tr){
                                    $query->wherehas('tribuna', function($query) use($preventas_tr){
                                        $query->where('id', $preventas_tr->id_tribuna);
                                    });                                   
                                });
                            })                      
                          ->where('id_evento', $palco_v->id_evento)
                          ->first();
                         
                        if($palcos_tr){
                            $porcent_descuento = $preventas_tr->porcentaje_descuento_precio;
                            $fijo_descuento = $preventas_tr->descuento_fijo_precio;

                            $porcent_descuento_ser = $preventas_tr->porcentaje_descuento_servicio;
                            $fijo_descuento_ser = $preventas_tr->descuento_fijo_servicio;

                            $band = true;
                        }
                   }
                }
                

                //Descuento para toda la localidad
                if(!$band){
                    $preventas_ld = Preventum::where('fecha_inicio','<=',$today)
                                    ->where('fecha_fin','>=',$today)
                                    ->where('hora_inicio','<=',$time)
                                    ->where('hora_fin','>=',$time)
                                    ->where('activo',1)
                                    ->whereNotNull('id_localidad')
                                    ->whereNull('id_evento')
                                    ->whereNull('id_tribuna')
                                    ->orderBy('fecha_fin', 'DESC')
                                    ->orderBy('hora_fin', 'DESC')
                                    ->first();

                    if($preventas_ld){ 

                        $palcos_ld = PalcoEvento::find($value_palco)
                          ->wherehas('palco', function($query) use($preventas_ld){
                                $query->wherehas('localidad', function($query) use($preventas_ld){
                                   $query->where('id', $preventas_ld->id_localidad);
                                });                                
                            })                          
                          ->where('id_evento', $palco_v->id_evento)                      
                          ->first();

                        if($palcos_ld){
                            $porcent_descuento = $preventas_ld->porcentaje_descuento_precio;
                            $fijo_descuento = $preventas_ld->descuento_fijo_precio;

                            $porcent_descuento_ser = $preventas_ld->porcentaje_descuento_servicio;
                            $fijo_descuento_ser = $preventas_ld->descuento_fijo_servicio;

                            $band = true;
                        }
                    }
                }


                PalcoEvento::find($value_palco)->update(['status' => 6]);

                $detalle_venta = new DetalleVent();
                $detalle_venta->id_venta = $id_venta;
                $detalle_venta->id_palco_evento = $value_palco;
                $detalle_venta->precio_venta = $precioventa;
                
                $detalle_venta->impuesto = $impuesto;
                $detalle_venta->status = 0;
                $detalle_venta->codigo_moneda = $palco_v->codigo_moneda;

                if($band){

                    if($porcent_descuento == 0 || is_null($porcent_descuento)){
                       $detalle_venta->monto_descuento =  $fijo_descuento;
                    }
                    else{
                       $detalle_venta->monto_descuento = (($precioventa * $porcent_descuento)/100); 
                    }

                    if($porcent_descuento_ser == 0 || is_null($porcent_descuento_ser)){
                       $precioservicio =  $fijo_descuento_ser;
                    }else{
                       $precioservicio = (($precioventa * $porcent_descuento_ser)/100);
                    }

                }else{
                    $detalle_venta->monto_descuento = 0;
                }  

                $detalle_venta->precio_servicio = $precioservicio;
                $detalle_venta->save();               
               
            }

        }        

        $venta_total = Vent::with('detalle_vents')->find($id_venta);        
        $precio_venta_total = 0;
        $precio_venta_total_boletas = 0;
        $precio_venta_total_palcos = 0;
        
        foreach ($venta_total->detalle_vents as $key) {

            if(!is_null($key->impuesto)){
                $precio_impuesto = ($key->precio_venta * $key->impuesto) / 100;
            }else{
                $precio_impuesto = 0;
            }
            

            $precio_venta_total = $precio_venta_total + ($key->precio_venta + $key->precio_servicio + $precio_impuesto - $key->monto_descuento);

            if($key->id_palco_evento == null){
                $precio_venta_total_boletas = $precio_venta_total_boletas + ($key->precio_venta + $key->precio_servicio + $precio_impuesto - $key->monto_descuento);
            }else{
                if($key->id_boleta_evento == null){
                    $precio_venta_total_palcos = $precio_venta_total_palcos + ($key->precio_venta + $key->precio_servicio + $precio_impuesto - $key->monto_descuento);
                }
            }
        
        }        

        $refVenta = [
            'precio_total_boletas' => $precio_venta_total_boletas,
            'precio_total_palcos' => $precio_venta_total_palcos,
            'precio_total' => round($precio_venta_total,2),
            'refVenta' => $venta_total->token_refventa
        ];        

        return $this->sendResponse($refVenta, 'Success');

    }



    public function validatePalcos($array_palcos)
    {
        foreach ($array_palcos as $value_palco) {
            $palco_v = PalcoEvento::where('status', 1)->find($value_palco);
            if(!$palco_v){
                return false;
            } 
        }
        return true;
        
    }

    public function validateBoletas($array_boletas)
    {
        foreach ($array_boletas as $value_boleta) {
            $boleta_v = BoletaEvento::where('status', 1)->find($value_boleta);
            if(!$boleta_v){
                return false;
            }
        }
        return true;
        
    }    


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vent  $vent
     * @return \Illuminate\Http\Response
     */
    public function show($id_venta)
    {
        $venta = Vent::with('punto_ventum')->with('detalle_pago')->with('detalle_vents')->where('active', 1)->find($id_venta);
        if (is_null($venta)) {
            return $this->sendError('Venta no encontrado');
        }
        return $this->sendResponse($venta->toArray(), 'Venta devuelta con éxito');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return true;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return true;
    }    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vent  $vent
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vent $vent)
    {
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vent  $vent
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vent $vent)
    {
        return true;
    }
}

