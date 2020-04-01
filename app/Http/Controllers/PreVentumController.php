<?php

namespace App\Http\Controllers;

use App\Models\Preventum;
use App\Models\Evento;
use App\Models\Tribuna;
use App\Models\Localidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use Validator;
/**
 * @group Administración de Preventa
 *
 * APIs para la gestion de la tabla preventa
 */
class PreVentumController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);
    }


    /**
     * Lista de la tabla preventa paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $preventa = Preventum::with("evento")->with("precios_monedas")->paginate(15);
        return $this->sendResponse($preventa->toArray(), 'Preventa devueltos con éxito');
    }


    /**
     * Lista de las preventas.
     *
     * @return \Illuminate\Http\Response
     */
    public function preventum_all()
    {
        $preventa = Preventum::with("evento")->with("precios_monedas")->get();
        return $this->sendResponse($preventa->toArray(), 'Preventa devueltos con éxito');
    }


    /**
     * Buscar Preventa por nombre.
     *@bodyParam nombre string Nombre del punto de venta.
     *@response{
     *    "nombre" : "Preventa New",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarPreventa(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $preventa = Preventum::where('nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($preventa->toArray(), 'Todas las preventas filtradas');
       }else{
            
            $preventa = Preventum::get();
            return $this->sendResponse($preventa->toArray(), 'Todas las preventas devueltas'); 
       }

        
    }

    /**
     * Agrega un nuevo elemento a la tabla preventa
     *
     *@bodyParam nombre string required Nombre de la preventa.
     *@bodyParam id_evento int Id del evento.
     *@bodyParam id_evento_origen int Evento en que se creo la preventa.
     *@bodyParam id_tribuna int Id de la tribuna.
     *@bodyParam id_localidad int Id de la localidad.
     *@bodyParam fecha_inicio date Fecha de inicio.
     *@bodyParam fecha_fin date Fecha de finalización.
     *@bodyParam hora_inicio time Hora de inicio.
     *@bodyParam hora_fin time Hora de finalización. 
     *@bodyParam porcentaje_descuento_precio float Porcentaje de descuento aplicado al precio de la preventa.
     *@bodyParam porcentaje_descuento_precio2 float Porcentaje de descuento aplicado al precio de la preventa 2.
     *@bodyParam porcentaje_descuento_servicio float Porcentaje de descuento aplicado al servicio de la preventa.
     *@bodyParam porcentaje_descuento_servicio2 float Porcentaje de descuento aplicado al servicio de la preventa 2.
     *@bodyParam descuento_fijo_precio int Descuento fijo aplicado al precio de la preventa. 
     *@bodyParam tipo_descuento_precio int Tipo de descuento aplicado al precio de la preventa. 
     *@bodyParam descuento_fijo_servicio int Descuento fijo aplicado al servicio de la preventa. 
     *@bodyParam codigo_moneda string required Código de la moneda.
     *@bodyParam codigo_moneda2 string Código de la moneda 2.
     *@bodyParam activo int required Estado de la preventa. Defaults 0
     * @response {
     *  "nombre": "PreVenta New",
     *  "id_evento": 1,
     *  "id_evento_origen": 1,
     *  "id_tribuna": null,
     *  "id_localidad": null,
     *  "fecha_inicio": null,
     *  "fecha_fin": null,
     *  "hora_inicio": null,
     *  "hora_fin": null,
     *  "porcentaje_descuento_precio" : 10.00,
     *  "descuento_fijo_precio" : 20.00,
     *  "descuento_fijo_precio2" : 0,
     *  "porcentaje_descuento_servicio" : 10.00,
     *  "descuento_fijo_servicio2" : 20.00,
     *  "descuento_fijo_servicio" : 10,
     *  "activo": 0           
     *  "codigo_moneda": "USD",
     *  "codigo_moneda2": "COP"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [                        
            "nombre" => 'required|max:200', 
            "id_evento" =>  'nullable|integer',
            "id_evento_origen" =>  'nullable|integer',
            "id_tribuna" =>  'nullable|integer',
            "id_localidad" =>  'nullable|integer', 
            "fecha_inicio" => 'nullable|date|date_format:Y-m-d',
            "fecha_fin" => 'nullable|date|date_format:Y-m-d',
            "hora_inicio" => 'nullable|date_format:H:i',
            "hora_fin" => 'nullable|date_format:H:i',
            "porcentaje_descuento_servicio" => 'nullable',
            "descuento_fijo_servicio2" => 'nullable',
            "descuento_fijo_servicio" => 'nullable|integer', 
            "porcentaje_descuento_precio" => 'nullable',
            "descuento_fijo_precio2" => 'nullable',
            "descuento_fijo_precio" => 'nullable|integer', 
            "activo" => 'required|integer',
            "codigo_moneda" => 'required|string',
            "codigo_moneda2" => 'nullable|string'          
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(is_null($request->input('id_evento')))
            if(is_null($request->input('id_tribuna')))
                if(is_null($request->input('id_localidad')))
                    return $this->sendError('Debe especificar un evento o una tribuna o una localidad.');


        if($request->input('id_evento') != null && $request->input('id_evento') != ""){

            $evento = Evento::find($request->input('id_evento'));
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }
            Input::merge(['id_tribuna' => null]);
            Input::merge(['id_localidad' => null]);
        }

        if($request->input('id_tribuna') != null && $request->input('id_tribuna') != ""){

            $tribuna = Tribuna::find($request->input('id_tribuna'));
            if (is_null($tribuna)) {
                return $this->sendError('La tribuna indicada no existe');
            }
            Input::merge(['id_evento' => null]);
            Input::merge(['id_localidad' => null]);
        }

        if($request->input('id_localidad') != null && $request->input('id_localidad') != ""){

            $localidad = Localidad::find($request->input('id_localidad'));
            if (is_null($localidad)) {
                return $this->sendError('La localidad indicada no existe');
            }
            Input::merge(['id_evento' => null]);
            Input::merge(['id_tribuna' => null]);
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


        if($request->input('porcentaje_descuento_precio') != null && $request->input('porcentaje_descuento_precio') != ""){            
            Input::merge(['descuento_fijo_precio' => null]);            
        }

        if($request->input('porcentaje_descuento_servicio') != null && $request->input('porcentaje_descuento_servicio') != ""){            
            Input::merge(['descuento_fijo_servicio' => null]);            
        }

        if($request->input('descuento_fijo_servicio') != null && $request->input('descuento_fijo_servicio') != ""){            
            Input::merge(['porcentaje_descuento_servicio' => null]);            
        }

        if($request->input('descuento_fijo_precio') != null && $request->input('descuento_fijo_precio') != ""){            
            Input::merge(['porcentaje_descuento_precio' => null]);            
        }        
        

        if(is_null($request->input('activo'))){
            Input::merge(['activo' => 0]);
        }

        if(!is_null($request->input('id_evento_origen'))){
            $evento_origen = Evento::find($request->input('id_evento_origen'));
            if (is_null($evento_origen)) {
                return $this->sendError('El evento origen indicado no existe');
            }
        }

        $preventa = Preventum::create($request->all());  


        $preciosmonedas = new PreciosMonedas();
        $preciosmonedas->id_preventa = $preventa->id;
        $preciosmonedas->descuento_fijo_precio = $request->input('descuento_fijo_precio');
        $preciosmonedas->descuento_fijo_servicio = $request->input('descuento_fijo_servicio');
        $preciosmonedas->codigo_moneda = $request->input('codigo_moneda');
        $preciosmonedas->save();

        if(!is_null($request->input('codigo_moneda2'))){ 

            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->id_preventa = $preventa->id;
            $preciosmonedas->descuento_fijo_precio = $request->input('descuento_fijo_precio2');
            $preciosmonedas->descuento_fijo_servicio = $request->input('descuento_fijo_servicio2');
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save();

        }


        return $this->sendResponse($preventa->toArray(), 'Preventa creada con éxito');
    }


    /**
     * Listado de preventas por evento
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\Models\Evento  $id
     * @return \Illuminate\Http\Response
     */
    public function listado_preventasEvento($id)
    {
        
        $evento = Evento::find($id);
        if(!$evento){
            return $this->sendError('No se encuentra el evento especificado');
        }

        $preventa = Preventum::where('id_evento_origen','=',$id)->orderBy('fecha_inicio')->get();
        if (count($preventa) == 0) {
            return $this->sendError('No se encuentran preventas por evento especificado');
        }
        return $this->sendResponse($preventa->toArray(), 'Preventas devueltas con éxito');
    }

    /**
     * Lista una preventa en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Preventum  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $preventa = Preventum::with("evento")
                            ->with("precios_monedas")
                            ->where('id','=',$id)->get();
        if (count($preventa) == 0) {
            return $this->sendError('La preventa no se encuentra');
        }
        return $this->sendResponse($preventa->toArray(), 'Preventa devuelto con éxito');
    }


    /**
     * Actualiza un elemento a la tabla preventa
     *
     *@bodyParam nombre string required Nombre de la preventa.
     *@bodyParam id_evento int Id del evento.
     *@bodyParam id_evento_origen int Evento en que se creo la preventa.
     *@bodyParam id_tribuna int Id de la tribuna.
     *@bodyParam id_localidad int Id de la localidad.
     *@bodyParam fecha_inicio date Fecha de inicio.
     *@bodyParam fecha_fin date Fecha de finalización.
     *@bodyParam hora_inicio time Hora de inicio.
     *@bodyParam hora_fin time Hora de finalización. 
     *@bodyParam porcentaje_descuento_precio float Porcentaje de descuento aplicado al precio de la preventa.
     *@bodyParam porcentaje_descuento_precio2 float Porcentaje de descuento 2 aplicado al precio de la preventa.
     *@bodyParam descuento_fijo_precio int Descuento fijo aplicado al precio de la preventa. 
     *@bodyParam porcentaje_descuento_servicio float Porcentaje de descuento aplicado al servicio de la preventa.
     *@bodyParam porcentaje_descuento_servicio2 float Porcentaje de descuento 2 aplicado al servicio de la preventa.
     *@bodyParam descuento_fijo_servicio int Descuento fijo aplicado al servicio de la preventa. 
     *@bodyParam activo int required Estado de la preventa. Defaults 0
     *@bodyParam codigo_moneda string required Código de la moneda.
     *@bodyParam codigo_moneda2 string Código de la moneda 2.
     * @response {
     *  "nombre": "Pre-Venta Edit",
     *  "id_evento": null,
     *  "id_evento_origen": 1,
     *  "id_tribuna": 1,
     *  "id_localidad": null,
     *  "fecha_inicio": null,
     *  "fecha_fin": null,
     *  "hora_inicio": null,
     *  "hora_fin": null,
     *  "porcentaje_descuento_precio" : 10.00,
     *  "descuento_fijo_precio2" : 20.00,
     *  "descuento_fijo_precio" : 0,
     *  "porcentaje_descuento_servicio" : 10.00,
     *  "descuento_fijo_servicio2" : 20.00,
     *  "descuento_fijo_servicio" : 0,
     *  "activo": 1,
     *  "codigo_moneda": "USD",
     *  "codigo_moneda2": "COP"         
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Preventum  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [                        
            "nombre" => 'required|max:200', 
            "id_evento" =>  'nullable|integer',
            "id_evento_origen" =>  'nullable|integer',
            "id_tribuna" =>  'nullable|integer',
            "id_localidad" =>  'nullable|integer', 
            "fecha_inicio" => 'nullable|date|date_format:Y-m-d',
            "fecha_fin" => 'nullable|date|date_format:Y-m-d',
            "hora_inicio" => 'nullable|date_format:H:i',
            "hora_fin" => 'nullable|date_format:H:i',
            "porcentaje_descuento_servicio" => 'nullable',
            "descuento_fijo_servicio2" => 'nullable',
            "descuento_fijo_servicio" => 'nullable|integer', 
            "porcentaje_descuento_precio" => 'nullable',
            "descuento_fijo_precio2" => 'nullable',
            "descuento_fijo_precio" => 'nullable|integer', 
            "activo" => 'required|integer',
            "codigo_moneda" => 'required|string',
            "codigo_moneda2" => 'nullable|string'          
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        if(is_null($input['id_evento']))
            if(is_null($input['id_tribuna']))
                if(is_null($input['id_localidad']))
                    return $this->sendError('Debe especificar un evento o una tribuna o una localidad.');

        $preventa_search = Preventum::find($id);
        if (is_null($preventa_search)) {
            return $this->sendError('Descuento no encontrado');
        }


        if($input['id_evento'] != null && $input['id_evento'] != ""){
            $evento = Evento::find($input['id_evento']);
            if (is_null($evento)) {
                return $this->sendError('El evento indicado no existe');
            }
            $preventa_search->id_evento = $input['id_evento'];
            $preventa_search->id_tribuna = null;
            $preventa_search->id_localidad = null;

        }

        if($input['id_tribuna'] != null && $input['id_tribuna'] != ""){
            $tribuna = Tribuna::find($request->input('id_tribuna'));
            if (is_null($tribuna)) {
                return $this->sendError('La tribuna indicada no existe');
            }
            $preventa_search->id_evento = null;
            $preventa_search->id_tribuna = $input['id_tribuna'];
            $preventa_search->id_localidad = null;

        }

        if($input['id_localidad'] != null && $input['id_localidad'] != ""){
            
            $localidad = Localidad::find($request->input('id_localidad'));
            if (is_null($localidad)) {
                return $this->sendError('La localidad indicada no existe');
            }
            $preventa_search->id_evento = null;
            $preventa_search->id_tribuna = null;
            $preventa_search->id_localidad = $input['id_localidad'];

        }


        if(!is_null($input['codigo_moneda2'])){
            $moneda = Moneda::find($input['codigo_moneda2']);
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicada no existe');
            }
        }


        if(!is_null($input['codigo_moneda'])){
            $moneda = Moneda::find($input['codigo_moneda']);
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicada no existe');
            }
        }
        

        if(is_null($input['activo'])){
            $preventa_search->activo  = 0;
        }else{
            $preventa_search->activo  = $input['activo'];
        } 

        if(is_null($input['porcentaje_descuento_precio'])){
            $preventa_search->porcentaje_descuento_precio  = null;            
        }else{
            $preventa_search->porcentaje_descuento_precio  = $input['porcentaje_descuento_precio'];
        } 

        if(is_null($input['porcentaje_descuento_servicio'])){
            $preventa_search->porcentaje_descuento_servicio  = null;            
        }else{
            $preventa_search->porcentaje_descuento_servicio  = $input['porcentaje_descuento_servicio'];
            
        }

        if(!is_null($input['id_evento_origen'])){
            $evento_origen = Evento::find($input['id_evento_origen']);
            if (is_null($evento_origen)) {
                return $this->sendError('El evento origen indicado no existe');
            }
            $preventa_search->id_evento_origen = $input['id_evento_origen']; 
        } 

        $preventa_search->nombre = $input['nombre']; 
        $preventa_search->fecha_inicio = $input['fecha_inicio'];
        $preventa_search->fecha_fin = $input['fecha_fin'];
        $preventa_search->hora_inicio = $input['hora_inicio'];
        $preventa_search->hora_fin = $input['hora_fin'];
        $preventa_search->save();


        $pmonedas_search = PreciosMonedas::where('id_preventa', $id)->get();
    
        if(count($pmonedas_search) > 1){

            $i = "";
            foreach ($pmonedas_search as $valuekey) {
                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'descuento_fijo_precio' => $input['descuento_fijo_precio'.$i],
                                'descuento_fijo_servicio' => $input['descuento_fijo_servicio'.$i],
                                'codigo_moneda' => $input['codigo_moneda'.$i]
                            ]);
                $i = "2";

            } 
                                    
        }else{

            foreach ($pmonedas_search as $valuekey) {                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'descuento_fijo_precio' => $input['descuento_fijo_precio'],
                                'descuento_fijo_servicio' => $input['descuento_fijo_servicio'],
                                'codigo_moneda' => $input['codigo_moneda']
                            ]); 
            }

            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->descuento_fijo_precio = $input['descuento_fijo_precio2'];
            $preciosmonedas->descuento_fijo_servicio = $input['descuento_fijo_servicio2'];
            $preciosmonedas->id_preventa = $id;
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save(); 

        }
        

        


        return $this->sendResponse($preventa_search->toArray(), 'Preventa actualizada con éxito');
    }

    /**
     * Elimina un elemento de la tabla preventa
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Preventum  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $preventa = Preventum::find($id);
            if (is_null($preventa)) {
                return $this->sendError('Preventa no encontrado');
            }
            $preventa->delete();
            return $this->sendResponse($preventa->toArray(), 'Preventa eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La preventa no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}

