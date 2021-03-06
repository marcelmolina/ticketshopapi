<?php

namespace App\Http\Controllers;

use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use App\Models\Preventum;
use App\Models\Evento;
use App\Models\Moneda;
use App\Models\Puesto;
use App\Models\Localidad;
use App\Models\Tribuna;
use App\Models\Fila;
use App\Models\Palco;
use App\Models\PreciosMonedas;
use App\Models\PuestosPalcoEvento;
use Illuminate\Http\Request;
use App\Http\Services\BoletaService;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\LocalidadEventoController;
use Validator;
use Carbon\Carbon;


/**
 * @group Administración de Boleta Evento
 *
 * APIs para la gestion de la tabla boleta_evento
 */
class BoletaEventoController extends BaseController
{
    

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy', 'storexlocalidad']]);
        $this->serviceBoleta = new BoletaService();        
    }

   
    /**
     * Lista de la tabla boleta_evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $boleta_evento = BoletaEvento::with("evento")
                    ->with("puesto")                   
                    ->with("precios_monedas")
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
     *@bodyParam precio_venta2 float Precio de venta (2) de la boleta del evento.
     *@bodyParam precio_servicio2 float Precio del servicio (2) de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string Codigo de la moneda.     
     *@bodyParam codigo_moneda2 string Codigo de la moneda (2).     
     *
     *@response{
     *       "id_evento" : 2,
     *       "id_puesto" : 2,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : 0,
     *       "precio_venta2" : 0,
     *       "precio_servicio2" : 0,
     *       "status" : 0,
     *       "codigo_moneda" : "USD"
     *       "codigo_moneda2" : "COP"               
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
            'precio_venta2' => 'nullable',
            'precio_servicio2' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'nullable',
            'codigo_moneda2' => 'nullable'
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

        $validate = $this->serviceBoleta->checkBoletaEvent(1 , $request->input('id_evento'), $request->input('id_puesto'));
        if($validate){
            return $this->sendError('El puesto ya se encuentra registrado a un palco del evento.');
        }

        if(!is_null($request->input('codigo_moneda'))){
            $moneda = Moneda::find($request->input('codigo_moneda'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicada no existe');
            }
        }else{
            Input::merge(['codigo_moneda' => null]);
        }

        if(!is_null($request->input('codigo_moneda2'))){
            $moneda = Moneda::find($request->input('codigo_moneda2'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicada no existe');
            }
        }

        if(is_null($request->input('impuesto'))){
            Input::merge(['impuesto' => 0]);
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }

        $token = md5(microtime());        
        
        $boleta_evento = BoletaEvento::create($request->all());   

        $boleta = BoletaEvento::find($boleta_evento->id);
        $boleta->token_qr = $token;
        $boleta->save();

        $preciosmonedas = new PreciosMonedas();
        $preciosmonedas->id_boleta_evento = $boleta_evento->id;
        $preciosmonedas->precio_venta = $request->input('precio_venta');
        $preciosmonedas->precio_servicio = $request->input('precio_servicio');
        $preciosmonedas->codigo_moneda = $request->input('codigo_moneda');
        $preciosmonedas->save();

        if(!is_null($request->input('codigo_moneda2'))){
            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->id_boleta_evento = $boleta_evento->id;
            $preciosmonedas->precio_venta = $request->input('precio_venta2');
            $preciosmonedas->precio_servicio = $request->input('precio_servicio2');
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save();
        }

        return $this->sendResponse($boleta->toArray(), 'Boleta del evento creada con éxito');
    }

    
    /**
     * Store por localidad (BoletaEvento)     
     *
     *@bodyParam id_localidad int required ID de la localidad.
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam precio_venta float required Precio de venta de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam precio_venta2 float Precio de venta (2) de la boleta del evento.
     *@bodyParam precio_servicio2 float Precio del servicio (2) de la boleta.
     *@bodyParam codigo_moneda string Codigo de la moneda. 
     *@bodyParam codigo_moneda2 string Codigo de la moneda (2).  
     *
     *@response{
     *       "id_localidad" : 2,
     *       "id_evento" : 2,
     *       "impuesto" : 0,
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "precio_venta2" : 0,
     *       "precio_servicio2" : 0,
     *       "codigo_moneda" : "USD",             
     *       "codigo_moneda2" : "COP"               
     * }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storexlocalidad(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id_localidad' => 'required|integer',
            'id_evento'=> 'required|integer',
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'precio_venta2' => 'nullable',
            'precio_servicio2' => 'nullable',
            'codigo_moneda' => 'nullable',
            'codigo_moneda2' => 'nullable'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('Localidad no encontrada');
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        if(!is_null($request->input('codigo_moneda'))){
            $moneda = Moneda::find($request->input('codigo_moneda'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicada no existe');
            }  
        }

        if(!is_null($request->input('codigo_moneda2'))){
            $moneda = Moneda::find($request->input('codigo_moneda2'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicada no existe');
            }  
        }

        
        $puestos = Puesto::wherehas('localidad',function($query) use($request){
                            $query->where('id', $request->input('id_localidad'));
                        })
                    ->whereDoesntHave('palcos')
                    ->with(['localidad' => function($query) use($request){
                            $query->where('id', $request->input('id_localidad'));
                        }])
                    ->get();

        
        (new LocalidadEventoController)->store($request);

        foreach ($puestos as $puesto) {
            $this->store(
                new Request([
                    'id_evento' => $request->input('id_evento'),
                    'id_puesto' => $puesto->id,
                    'precio_venta' => $request->input('precio_venta'),
                    'precio_servicio' => $request->input('precio_servicio'),
                    'impuesto' => $request->input('impuesto'),
                    'precio_venta2' => $request->input('precio_venta2'),
                    'precio_servicio2' => $request->input('precio_servicio2'),
                    'status' => $request->input('status'),
                    'codigo_moneda' => $request->input('codigo_moneda'),
                    'codigo_moneda2' => $request->input('codigo_moneda2')
                ])
            );
        }

        $palcos = Palco::where('id_localidad', $request->input('id_localidad'))
                ->get();
        

        foreach ($palcos as $palco) {
            $palcoevento = PalcoEvento::create([
                    'id_evento'=> $request->input('id_evento'),
                    'id_palco' => $palco->id,
                    'precio_venta' => $request->input('precio_venta'),
                    'precio_servicio' => $request->input('precio_servicio'),
                    'impuesto' => $request->input('impuesto'),
                    'precio_venta2' => $request->input('precio_venta2'),
                    'precio_servicio2' => $request->input('precio_servicio2'),
                    'status' => $request->input('status'),
                    'codigo_moneda' => $request->input('codigo_moneda')
                ]);
            foreach ($palco->puestos as $puesto) {
                PuestosPalcoEvento::create([
                    'id_palco_evento' => $palcoevento->id,
                    'id_palco' => $palcoevento->id_palco,
                    'id_puesto' => $puesto->id
                ]);
            }
        }

        return response()->json('Boleta evento por localidad creada con éxito', 200);
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
                    ->with("precios_monedas")
                    ->find($id);                    
        if (!$boleta_evento) {
            return $this->sendError('La boleta de evento no se encuentra');
        }
        return $this->sendResponse($boleta_evento->toArray(), 'La boleta del evento devuelta con éxito');
    }
  


    /**
     * Actualiza un elemento a la tabla boleta_evento.
     *
     * [Se filtra por el ID]
     *@bodyParam precio_venta float required Precio de eventa de la boleta del evento.
     *@bodyParam precio_servicio float required Precio del servicio.
     *@bodyParam impuesto float Impuesto de la boleta.
     *@bodyParam precio_venta2 float Precio de venta (2) de la boleta del evento.
     *@bodyParam precio_servicio2 float Precio del servicio (2) de la boleta.
     *@bodyParam status int Status de la boleta del evento.  
     *@bodyParam codigo_moneda string Codigo de la moneda.
     *@bodyParam codigo_moneda2 string Codigo de la moneda (2).     
     *
     *@response{
     *       "precio_venta" : 0,
     *       "precio_servicio" : 0,
     *       "impuesto" : null,
     *       "precio_venta2" : 0,
     *       "precio_servicio2" : 0,
     *       "status" : null,
     *       "codigo_moneda" : "USD"               
     *       "codigo_moneda2" : "COP"               
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
            'precio_venta' => 'required',
            'precio_servicio' => 'required',
            'impuesto' => 'nullable',
            'precio_venta2' => 'nullable',
            'precio_servicio2' => 'nullable',
            'status' => 'nullable|integer',
            'codigo_moneda' => 'nullable',
            'codigo_moneda2' => 'nullable' 
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        } 

        if(!is_null($input['codigo_moneda2'])){
            $moneda2 = Moneda::find($input['codigo_moneda2']);
            if (is_null($moneda2)) {
                return $this->sendError('La moneda 2 indicada no existe');
            } 
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

        $boleta_evento_search->save();

        $pmonedas_search = PreciosMonedas::where('id_boleta_evento', $id)->get();

        if(count($pmonedas_search) > 1){
            $i = "";
            foreach ($pmonedas_search as $valuekey) {
                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'precio_venta' => $input['precio_venta'.$i],
                                'precio_servicio' => $input['precio_servicio'.$i],
                                'codigo_moneda' => $input['codigo_moneda'.$i]
                            ]);
                $i = "2";

            }            
            
        }else{

            foreach ($pmonedas_search as $valuekey) {                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'precio_venta' => $input['precio_venta'],
                                'precio_servicio' => $input['precio_servicio'],
                                'codigo_moneda' => $input['codigo_moneda']
                            ]); 
            }

            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->precio_venta = $input['precio_venta2'];
            $preciosmonedas->precio_servicio = $input['precio_servicio2'];
            $preciosmonedas->id_boleta_evento = $id;
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save(); 

        }

        return $this->sendResponse($boleta_evento_search->toArray(), 'Boleta del evento actualizada con éxito');

    }



    /**
     * Actualiza el estado del boleta_evento
     *
     * [Se filtra por el ID del BoletaEvento]
     *
     *@bodyParam status int required Estado.
     *
     * @param  \App\Models\BoletaEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function update_status(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'status' => 'required|integer'             
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $boleta_evento_search = BoletaEvento::find($id);
        if (is_null($boleta_evento_search)) {
            return $this->sendError('Boleta de evento no encontrado');
        }

        $boleta_evento_search->status  = $input['status'];

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



    /**
     * Listado de puestos por evento
     *
     * [Se filtra por el ID del Evento]
     *
     * @param  \App\Models\BoletaEvento  $boletaEvento
     * @return \Illuminate\Http\Response
     */
    public function listado_puestos_evento($id)
    {

        $evento = Evento::find($id);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $puestos_evento = BoletaEvento::with('puesto')->with('boleta_reserva')->with('boletas_preimpresa')->with('boletas_prevent')->where('id_evento', $id)->get();


        foreach ($puestos_evento as $puestos) {
            
            $puestos['puesto']['fila'] = Fila::find($puestos['puesto']['id_fila']);
            $puestos['puesto']['localidad'] = Localidad::find($puestos['puesto']['id_localidad']);
        }


        $palcos_evento = PalcoEvento::with('palco')->with('palco_reserva')->with('palco_preimpreso')->with('palco_prevent')->with('puestos_palco_eventos')->where('id_evento', $id)->get();

        foreach ($palcos_evento as $palcos) {

           $palcos['palco']['localidad_palco'] = Localidad::find($palcos['palco']['id_localidad']);
           
           foreach ($palcos['puestos_palco_eventos'] as $puestos_palco) {
               
               $puestos_palco['puesto'] = Puesto::with('fila')->with('localidad')->find($puestos_palco['id_puesto']);

           }
           
        }

        $puestos_com = compact('puestos_evento', 'palcos_evento');

        return $this->sendResponse($puestos_com, 'Puestos y palcos del evento');

    }


    /**
     * Listado de filas, puestos con las boletas y palcos por localidad y evento
     *
     * [Se filtra por el ID del Localidad y el ID del evento]
     *
     * @param  \App\Models\BoletaEvento  $boletaEvento
     * @return \Illuminate\Http\Response
     */
    public function listado_boletas_localidad($id_localidad, $id_evento,$codigo_moneda)
    {

        $localidad = Localidad::find($id_localidad);
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicado no existe');
        }

        $evento = Evento::find($id_evento);
        if (is_null($evento) && $id_evento!=0) {
            return $this->sendError('El evento indicado no existe');
        }

        $boletas_localidad = Localidad::with(['filas','filas.puestos','filas.puestos.palcos',
            'filas.puestos.palcos.palco_eventos' => function ($query) use($id_evento,$codigo_moneda)
                                {
                                    $query->where('id_evento',$id_evento); 
                                    $query->with(array('precios_monedas' => function($query) use($codigo_moneda)
                                    {
                                        $query->where('codigo_moneda', $codigo_moneda);
                                         
                                    }));
                                },
            'filas.puestos.boleta_eventos' => function ($query) use($id_evento,$codigo_moneda)
                                {
                                    $query->where('id_evento',$id_evento);
                                    $query->with(array('precios_monedas' => function($query) use($codigo_moneda)
                                    {
                                        $query->where('codigo_moneda', $codigo_moneda);
                                         
                                    }));
                                },
            'palcos','palcos.palco_eventos' => function ($query) use($id_evento,$codigo_moneda)
                                {
                                    $query->where('id_evento',$id_evento);
                                    $query->with(array('precios_monedas' => function($query) use($codigo_moneda)
                                    {
                                        $query->where('codigo_moneda', $codigo_moneda);
                                         
                                    }));
                                }])
            ->where('id', $id_localidad)
            ->get();

        return $this->sendResponse($boletas_localidad->toArray(), 'Boletas y palcos por localidad y evento devueltas con éxito');

    }


     /**
     * Reserva de boletas o palcos por localidad y evento
     * Usado para el carro de compras
     *
     * [Se filtra por el ID del Localidad y el ID del evento]
     *
     *@bodyParam id_evento int Id del evento.
     *@bodyParam id_localidad int Id de la localidad.
     *
     * @return \Illuminate\Http\Response
     */
    public function boletas_palcos_reservadas(Request $request)
    {
        
        $validator = Validator::make($request->all(), [            
            'id_evento' => 'nullable|integer',
            'id_localidad' => 'nullable|integer',           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $localidad = Localidad::find($request->input('id_localidad'));
        if (is_null($localidad)) {
            return $this->sendError('La localidad indicado no existe');
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }


        if ($localidad->palco!=1) {
            $boletaOpalco = BoletaEvento::where('id_evento', $request->input('id_evento'))
                      ->wherehas('puesto',function($query) use($request){
                            $query->where('id_localidad', $request->input('id_localidad'));
                        })
                      ->where('status', 1)
                      ->first();

            $boletaOpalco->status = 2;
            $boletaOpalco->save();
        } else {
            $boletaOpalco = PalcoEvento::where('id_evento', $request->input('id_evento'))
                      ->wherehas('palco.puestos',function($query) use($request){
                            $query->where('id_localidad', $request->input('id_localidad'));
                        })
                      ->where('status', 1)
                      ->first();

            $boletaOpalco->status = 2;
            $boletaOpalco->save();
        }

        return $this->sendResponse($boletaOpalco->toArray(), 'Boleta o Palco reservado con éxito');
            
    }



    /**
     * Obtiene precio de boleteria     
     *
     * @return \Illuminate\Http\Response
     */
    public function getPrecio(Request $request)
    {

        $validator = Validator::make($request->all(), [            
            'id_evento' => 'nullable|integer',
            'id_tribuna' => 'nullable|integer',
            'id_localidad' => 'nullable|integer'           
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(!is_null($request->input('id_localidad'))){
            $localidad = Localidad::find($request->input('id_localidad'));
            if (is_null($localidad)) {
                return $this->sendError('La localidad indicado no existe');
            }
        }

        if(!is_null($request->input('id_tribuna'))){
            $tribuna = Tribuna::find($request->input('id_tribuna'));
            if (is_null($tribuna)){
                return $this->sendError('La tribuna indicada no existe');
            }
        }

        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $preventa = PreVentum::with('precios_monedas')->where(function ($query) use ($request) {
            $query->where('id_evento','=',$request->input('id_evento'))
		  ->orWhere('id_tribuna','=',$request->input('id_tribuna'))
                  ->orWhere('id_localidad','=',$request->input('id_localidad'));
        })
        ->whereDate('fecha_inicio','>=',Carbon::now())->whereDate('fecha_fin','<=',Carbon::now())->whereDate('hora_inicio','>=',Carbon::now())->whereDate('hora_fin','<=',Carbon::now())->orderby('fecha_inicio')->first();

        
        $total = $request->precio_venta+$request->precio_servicio;
        $totalAr = array();
        
        if (!is_null($preventa)) {
            if ($preventa->id_localidad==$request->id_localidad || $preventa->id_tribuna==$request->id_tribuna || $preventa->id_evento==$request->id_evento) {
                
                if(count($preventa->precios_monedas) > 0){
                    foreach ($preventa->precios_monedas as $value) {
                        if($value['descuento_fijo_precio']){
                            $total = $total-$value['descuento_fijo_precio'];

                            array_push($totalAr, array('total' => $total, 'moneda' => $value['codigo_moneda']));
                        }
                    }
                }else{
                    
                    if($preventa->porcentaje_descuento_precio){

                        $total = $total-(($request->precio_venta*$preventa->porcentaje_descuento_precio/100));
                           
                    }
                       
                    
                }


                if(count($preventa->precios_monedas) > 0){
                    foreach ($preventa->precios_monedas as $value) {
                        if($value['descuento_fijo_servicio']){
                            $total = $total-$value['descuento_fijo_servicio'];

                            array_push($totalAr, array('total' => $total, 'moneda' => $value['codigo_moneda']));
                        }
                    }
                }else{
                    
                    if($preventa->porcentaje_descuento_servicio){

                        $total = $total-(($request->precio_venta*$preventa->porcentaje_descuento_servicio/100));
                           
                    }
                       
                    
                }

            }
        }
        if(sizeof($totalAr) > 0){
            return $this->sendResponse($totalAr, '');
        }

        return $total+(($total*$request->impuesto)/100);
    }

}
