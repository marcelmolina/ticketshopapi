<?php

namespace App\Http\Controllers;

use App\Models\PreciosMonedas;
use App\Models\Preventum;
use App\Models\Evento;
use App\Models\BoletaEvento;
use App\Models\PalcoEvento;
use App\Models\LocalidadEvento;
use App\Models\CostoEvento;
use App\Models\Moneda;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de PreciosMonedas
 *
 * APIs para la gestion de los precios con diferentes monedas
 */
class PreciosMonedasController extends BaseController
{
    

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy', 'destroy_evento']]);
    }
    
    /**
     * Lista de la tabla PreciosMonedas.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $precios = PreciosMonedas::with("evento", "palco_evento", "boleta_evento", "preventum", "costo_evento", "moneda")->paginate(15);
        return $this->sendResponse($precios->toArray(), 'Precios-Monedas devueltas con éxito');
    }

   
    /**
     *  Agrega un nuevo elemento a la tabla Precios-Monedas.
     *
     *@bodyParam id_evento int ID del evento.
     *@bodyParam id_palco_evento int ID de la tabla palcoEvento.
     *@bodyParam id_boleta_evento int ID de la tabla boletaEvento.
     *@bodyParam id_preventa int ID de la tabla preventa.
     *@bodyParam id_costo_evento int ID de la tabla costo_evento.
     *@bodyParam monto_minimo float Monto Minimo.
     *@bodyParam precio_venta float Precio Venta.  
     *@bodyParam precio_servicio float Precio del servicio.
     *@bodyParam descuento_fijo_precio float Descuento fijo en el precio.
     *@bodyParam descuento_fijo_servicio float Descuento fijo en el servicio.
     *@bodyParam valor float Valor.
     *@bodyParam status bolean Estado del registro.
     *@bodyParam codigo_moneda string required Codigo de la moneda. 
     *
     *@response{
     *       "id_evento" : 2,
     *       "monto_minimo" : 200,
     *       "codigo_moneda" : "USD"               
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [                        
            "id_evento" => 'nullable|integer', 
            "id_palco_evento" =>  'nullable|integer',
            "id_boleta_evento" =>  'nullable|integer',
            "id_preventa" =>  'nullable|integer', 
            "id_costo_evento" => 'nullable|integer',
            "codigo_moneda" => 'required|string',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('Moneda no encontrada');
        }

        $preciosM = new PreciosMonedas();

        if(is_null($request->input('id_evento')))
            if(is_null($request->input('id_palco_evento')))
                if(is_null($request->input('id_boleta_evento')))
                    if(is_null($request->input('id_preventa')))
                        if(is_null($request->input('id_costo_evento'))){
                            return $this->sendError('Debe especificar por lo menos un ID para asignar precios'); 
        }

        if(!is_null($request->input('id_evento'))){

            $evento = Evento::find($request->input('id_evento'));
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            
            $preciosM->id_evento = $request->input('id_evento');
            $preciosM->monto_minimo = $request->input('monto_minimo');
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->save();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');
        }
        else if(!is_null($request->input('id_palco_evento'))){

            $palco_evento = PalcoEvento::find($request->input('id_palco_evento'));
            if (is_null($palco_evento)) {
                return $this->sendError('Palco de evento no encontrado');
            }
            
            $preciosM->id_palco_evento = $request->input('id_palco_evento');
            $preciosM->precio_venta = $request->input('precio_venta');
            $preciosM->precio_servicio = $request->input('precio_servicio');
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->save();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');

        }
        else if(!is_null($request->input('id_boleta_evento'))){ 

            $boleta_evento = BoletaEvento::find($request->input('id_boleta_evento'));
            if (is_null($boleta_evento)) {
                return $this->sendError('Boleta de evento no encontrado');
            }
            $preciosM->id_boleta_evento = $request->input('id_boleta_evento');
            $preciosM->precio_venta = $request->input('precio_venta');
            $preciosM->precio_servicio = $request->input('precio_servicio');            
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->save();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');

        }
      
        else if(!is_null($request->input('id_preventa'))){ 

            $preventa = Preventum::find($request->input('id_preventa'));
            if (is_null($preventa)) {
                return $this->sendError('La preventa no se encuentra');
            }

            $preciosM->id_preventa = $request->input('id_preventa');
            $preciosM->descuento_fijo_precio = $request->input('descuento_fijo_precio');
            $preciosM->descuento_fijo_servicio = $request->input('descuento_fijo_servicio');
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->save();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');
        }
        else{
            if(!is_null($request->input('id_costo_evento'))){

                $costo_evento = CostoEvento::find($request->input('id_costo_evento'));
                if (is_null($costo_evento)) {
                    return $this->sendError('El costo de evento no se encuentra');
                }

                $preciosM->id_costo_evento = $request->input('id_costo_evento');
                $preciosM->valor = $request->input('valor');
                $preciosM->codigo_moneda = $request->input('codigo_moneda');
                $preciosM->save();

                return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');

            }
        }

        return $this->sendError('Verifique su solicitud');  
    }

     /**
     * Precios del evento de la tabla precios_monedas
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\Models\Evento  $id_evento
     * @return \Illuminate\Http\Response
     */
    public function precios_evento($id_evento)
    {
        $evento = PreciosMonedas::with("moneda")
                        ->where('id_evento', $id_evento)
                        ->get();

        
        if(is_null($evento) || count($evento) == 0 ){
            return $this->sendError('Precios monedas por evento no encontrados');
        }

        $preventa = Preventum::with("precios_monedas")
                            ->with("precios_monedas.moneda")
                            ->where('id_evento', $id_evento)
                            ->get();
        
        $costos = CostoEvento::with("precios_monedas")
                            ->with("precios_monedas.moneda")
                            ->where('id_evento', $id_evento)
                            ->get();

        $localidades = LocalidadEvento::with("localidad")
                        ->with("codigo_moneda")
                        ->with("codigo_moneda2")
                        ->where('id_evento', $id_evento)
                        ->get();

        $resp = compact('evento', 'preventa', 'costos', 'localidades');

        return $this->sendResponse($resp, 'Precios monedas por evento devueltos con éxito');
    }



    /**
     * Precios del evento por una moneda específica
     *
     * [Se filtra por el ID del evento y el codigo de la moneda]
     *
     * @param  \App\Models\Evento  $id_evento
     * @return \Illuminate\Http\Response
     */
    public function precios_evento_moneda($id_evento, $codigo_moneda)
    {
        $evento = PreciosMonedas::with("moneda")
                        ->where('id_evento', $id_evento)
                        ->where('codigo_moneda', $codigo_moneda)
                        ->get();
        

        $preventa = Preventum::with(["precios_monedas" => function($query) use($codigo_moneda){
                                $query->where('precios_monedas.codigo_moneda', '=', $codigo_moneda);
                            }])
                            ->where('id_evento', $id_evento)
                            ->get();
        
        $costos = CostoEvento::with(["precios_monedas" => function($query) use($codigo_moneda){
                                $query->where('.precios_monedas.codigo_moneda', '=', $codigo_moneda);
                            }])
                            ->where('id_evento', $id_evento)
                            ->get();

        $localidades = LocalidadEvento::with("localidad")
                        ->with("codigo_moneda")                        
                        ->where('codigo_moneda', $codigo_moneda)                        
                        ->where('id_evento', $id_evento)
                        ->get();
        
        if(count($localidades) == 0){

        $localidades = LocalidadEvento::with("localidad")                        
                        ->with("codigo_moneda2")                        
                        ->where('codigo_moneda2', $codigo_moneda)
                        ->where('id_evento', $id_evento)
                        ->get();
        }

        $resp = compact('evento', 'preventa', 'costos', 'localidades');

        return $this->sendResponse($resp, 'Precios monedas por evento devueltos con éxito');
    }


    /*
     * 
     *
     * @param  \App\Models\PreciosMonedas  $preciosMonedas
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return;
    }

   
    /**
     *  Actualiza un elemento a la tabla Precios-Monedas.
     * [Se filtra por el ID]
     *
     *@bodyParam id_evento int ID del evento.
     *@bodyParam id_palco_evento int ID de la tabla palcoEvento.
     *@bodyParam id_boleta_evento int ID de la tabla boletaEvento.
     *@bodyParam id_preventa int ID de la tabla preventa.
     *@bodyParam id_costo_evento int ID de la tabla costo_evento.
     *@bodyParam monto_minimo float Monto Minimo.
     *@bodyParam precio_venta float Precio Venta.  
     *@bodyParam precio_servicio float Precio del servicio.
     *@bodyParam descuento_fijo_precio float Descuento fijo en el precio.
     *@bodyParam descuento_fijo_servicio float Descuento fijo en el servicio.
     *@bodyParam valor float Valor.
     *@bodyParam status bolean Estado del registro.
     *@bodyParam codigo_moneda string required Codigo de la moneda. 
     *
     *@response{
     *       "id_evento" : 1,
     *       "monto_minimo" : 200,
     *       "codigo_moneda" : "USD"               
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PreciosMonedas  $preciosMonedas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [                        
            "id_evento" => 'nullable|integer', 
            "id_palco_evento" =>  'nullable|integer',
            "id_boleta_evento" =>  'nullable|integer',
            "id_preventa" =>  'nullable|integer', 
            "id_costo_evento" => 'nullable|integer',
            "codigo_moneda" => 'required|string',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('Moneda no encontrada');
        }

        $preciosM = PreciosMonedas::find($id);
        if(is_null($preciosM)){
            return $this->sendError('Precios monedas no encontrados');
        }

        if(is_null($request->input('id_evento')))
            if(is_null($request->input('id_palco_evento')))
                if(is_null($request->input('id_boleta_evento')))
                    if(is_null($request->input('id_preventa')))
                        if(is_null($request->input('id_costo_evento'))){
                            return $this->sendError('Debe especificar por lo menos un ID para asignar precios'); 
        }

        if(!is_null($request->input('id_evento'))){

            $evento = Evento::find($request->input('id_evento'));
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            
            $preciosM->id_evento = $request->input('id_evento');
            $preciosM->monto_minimo = $request->input('monto_minimo');
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->update();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');
        }
        else if(!is_null($request->input('id_palco_evento'))){

            $palco_evento = PalcoEvento::find($request->input('id_palco_evento'));
            if (is_null($palco_evento)) {
                return $this->sendError('Palco de evento no encontrado');
            }
            
            $preciosM->id_palco_evento = $request->input('id_palco_evento');
            $preciosM->precio_venta = $request->input('precio_venta');
            $preciosM->precio_servicio = $request->input('precio_servicio');            
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->update();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');

        }
        else if(!is_null($request->input('id_boleta_evento'))){ 

            $boleta_evento = BoletaEvento::find($request->input('id_boleta_evento'));
            if (is_null($boleta_evento)) {
                return $this->sendError('Boleta de evento no encontrado');
            }
            $preciosM->id_boleta_evento = $request->input('id_boleta_evento');
            $preciosM->precio_venta = $request->input('precio_venta');
            $preciosM->precio_servicio = $request->input('precio_servicio');            
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->update();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');

        }

        else if(!is_null($request->input('id_preventa'))){ 

            $preventa = Preventum::find($request->input('id_preventa'));
            if (is_null($preventa)) {
                return $this->sendError('La preventa no se encuentra');
            }

            $preciosM->id_preventa = $request->input('id_preventa');
            $preciosM->descuento_fijo_precio = $request->input('descuento_fijo_precio');
            $preciosM->descuento_fijo_servicio = $request->input('descuento_fijo_servicio');           
            $preciosM->codigo_moneda = $request->input('codigo_moneda');
            $preciosM->update();

            return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');
        }
        else{
            if(!is_null($request->input('id_costo_evento'))){

                $costo_evento = CostoEvento::find($request->input('id_costo_evento'));
                if (is_null($costo_evento)) {
                    return $this->sendError('El costo de evento no se encuentra');
                }

                $preciosM->id_costo_evento = $request->input('id_costo_evento');
                $preciosM->valor = $request->input('valor');
                $preciosM->codigo_moneda = $request->input('codigo_moneda');
                $preciosM->save();

                return $this->sendResponse($preciosM->toArray(), 'Precio por moneda creado con éxito');

            }
        }

        return $this->sendError('Verifique su solicitud'); 
    }

     /**
     * Elimina un elemento de la tabla precios_monedas
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\PreciosMonedas  $preciosMonedas
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_preciosMonedas)
    {
        try { 

            $precios_monedas = PreciosMonedas::find($id_preciosMonedas);
            if (is_null($precios_monedas)) {
                return $this->sendError('Precio por moneda no encontrado');
            }
            $precios_monedas->delete();
            return $this->sendResponse($precios_monedas->toArray(), 'Precio por moneda eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Precio por moneda no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
        
    }


    /**
     * Elimina todos los registros de tabla precios_monedas
     *     
     * [Se filtra por ID del evento y Codigo de la moneda]
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy_evento($id_evento, $codigo_moneda)
    {
        try { 


            $evento = Evento::find($id_evento);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }

            $moneda = Moneda::find($codigo_moneda);
            if (is_null($moneda)) {
                return $this->sendError('Moneda no encontrada');
            }

            //Precios del Evento
            $precios_monedas_evento = PreciosMonedas::where('id_evento', $id_evento)->where('codigo_moneda', $codigo_moneda)->delete();

            //Precios de la Preventa
            $preventa = Preventum::whereHas("precios_monedas", function ($query) use($codigo_moneda) {
                            $query->where('codigo_moneda', $codigo_moneda);
                        })
                        ->where('id_evento', $id_evento)
                        ->get();
            foreach ($preventa as $key) {
                 $precios_monedas = PreciosMonedas::find($key->precios_monedas->id)->where('codigo_moneda', $codigo_moneda)->delete();           
            }

            //Precios del CostoEvento
            $costoevento = CostoEvento::whereHas("precios_monedas", function ($query) use($codigo_moneda)         {              
                            $query->where('codigo_moneda', $codigo_moneda);
                        })
                        ->where('id_evento', $id_evento)
                        ->get();
            foreach ($costoevento as $key) {
                 $precios_monedas = PreciosMonedas::where($key->precios_monedas->id)->where('codigo_moneda', $codigo_moneda)->delete();           
            }

            //Precios de la LocalidadEvento
            $localidad = LocalidadEvento::where('codigo_moneda', $codigo_moneda)->where('id_evento', $id_evento)->first();
            
            if(is_null($localidad)){
                
                $localidad = LocalidadEvento::where('codigo_moneda2', $codigo_moneda)->where('id_evento', $id_evento)->first();
                
                if(!is_null($localidad)){
                    $localidad->delete();
                }
            
            }else{
                $localidad->delete();
            }

            //Precios de la BoletaEvento
            $boleta_evento = BoletaEvento::whereHas("precios_monedas", function ($query) use($codigo_moneda) {
                            $query->where('codigo_moneda', $codigo_moneda);
                        })
                        ->where('id_evento', $id_evento)
                        ->get();
            foreach ($boleta_evento as $key) {
                 $precios_monedas = PreciosMonedas::find($key->precios_monedas->id)->where('codigo_moneda', $codigo_moneda)->delete();           
            }

            //Precios del Palco Evento
            $palco_evento = PalcoEvento::whereHas("precios_monedas", function ($query) use($codigo_moneda) {
                            $query->where('codigo_moneda', $codigo_moneda);
                        })
                        ->where('id_evento', $id_evento)
                        ->get();
            foreach ($palco_evento as $key) {
                 $precios_monedas = PreciosMonedas::find($key->precios_monedas->id)->where('codigo_moneda', $codigo_moneda)->delete();           
            }
            
            return $this->sendResponse('', 'Todos los precios por moneda por evento y codigo moneda eliminados con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'No se pueden eliminar, ocurrió un problema.', 'exception' => $e->errorInfo], 400);
        }
        
    }
}
