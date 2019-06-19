<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\Auditorio;
use App\Models\Cliente;
use App\Models\Temporada;
use App\Models\TipoEvento;
use Validator;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Evento
 *
 * APIs para la gestion del evento
 */
class EventoController extends BaseController
{
    /**
     * Lista de la tabla evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evento = Evento::with('auditorio')
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada')                    
                    ->paginate(15);
        return $this->sendResponse($evento->toArray(), 'Eventos devueltos con éxito');
    }


    /**
     * Buscar Evento por nombre.
     *@bodyParam nombre string Nombre del evento.
     *@response{
     *    "nombre" : "Evento 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarEvento(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $evento = Evento::with('auditorio')
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada')
                ->where('evento.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($evento->toArray(), 'Todos los Eventos filtrados');
       }else{
            
            $evento = Evento::with('auditorio')
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada')
                    ->get();
            return $this->sendResponse($evento->toArray(), 'Todos los Eventos devueltos'); 
       }
        
    }


    /**
     * Agrega un nuevo elemento a la tabla evento
     *
     *@bodyParam fecha_evento date required Fecha del evento. Example: 2019-01-01
     *@bodyParam nombre string required Nombre del evento.
     *@bodyParam hora_inicio time Hora de inicio del evento. Example: null
     *@bodyParam hora_apertura time Hora de apertura del evento. Example: null
     *@bodyParam hora_finalizacion time Hora de finalizacion del evento. Example: null
     *@bodyParam codigo_pulep string Codigo del evento. Example: null
     *@bodyParam id_tipo_evento int  Id del tipo de evento. Defaults to 0
     *@bodyParam domicilios int Domicilios del evento. Defaults to 0
     *@bodyParam venta_linea int Venta en linea del evento. Defaults to 1
     *@bodyParam id_auditorio int required Id del auditorio del evento.
     *@bodyParam id_cliente int required Id del cliente del evento.
     *@bodyParam id_temporada int Id de la temporada del evento.
     *@bodyParam status int Status del evento.
     *@bodyParam fecha_inicio_venta_internet date Fecha de inicio de la venta por internet. Example: 2019-01-01
     *@bodyParam fecha_inicio_venta_puntos int required Cantidad de puntos de la ventas desde la fecha de inicio.
     *@bodyParam monto_minimo double Monto mínimo del evento.
     *@response{
     *       "fecha_evento" : "2019-01-01",
     *       "nombre" : "Evento WW",
     *       "hora_inicio": null,
     *       "hora_apertura": null,
     *       "hora_finalizacion" : null,
     *       "codigo_pulep": null,
     *       "id_tipo_evento": 0,
     *       "domicilios": 0,
     *       "venta_linea" : 1,
     *       "id_auditorio": 2,
     *       "id_cliente": 3,
     *       "id_temporada" : 1,
     *       "status": 0,
     *       "fecha_inicio_venta_internet": null,
     *       "fecha_inicio_venta_puntos": 12,
     *       "monto_minimo": 10.10,
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_evento' => 'required|date',
            'nombre' => 'required',
            'id_auditorio' => 'required',
            'id_cliente' => 'required',
            'fecha_inicio_venta_puntos' => 'required|integer'      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $auditorio = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio indicado no encontrado');
        }

        $cliente = Cliente::find($request->input('id_cliente'));
        if (is_null($cliente)) {
            return $this->sendError('Cliente indicado no encontrado');
        }

        if(!is_null($request->input('hora_inicio'))){
            $validator = Validator::make($request->all(), [
                'hora_inicio' => 'timezone',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }

        if(!is_null($request->input('hora_apertura'))){
            $validator = Validator::make($request->all(), [
                'hora_apertura' => 'timezone',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }

        if(!is_null($request->input('hora_finalizacion'))){
            $validator = Validator::make($request->all(), [
                'hora_finalizacion' => 'timezone',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }

        if(!is_null($request->input('fecha_inicio_venta_internet'))){
            $validator = Validator::make($request->all(), [
                'fecha_inicio_venta_internet' => 'date',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
        }

        if(!is_null($request->input('id_tipo_evento'))){            
            $tipoEvento = TipoEvento::find($request->input('id_tipo_evento'));
            if (is_null($tipoEvento)) {
                return $this->sendError('Tipo de evento indicado no encontrado');
            }
        }

        if(!is_null($request->input('id_temporada'))){            
            $temporada = Temporada::find($request->input('id_temporada'));
            if (is_null($temporada)) {
                return $this->sendError('Temporada indicado no encontrada');
            }
        }

        if(is_null($request->input('status'))){
            Input::merge(['status' => 0]);
        }

        if(is_null($request->input('monto_minimo'))){
            Input::merge(['monto_minimo' => 0.00]);
        }

        if(is_null($request->input('domicilios'))){
            Input::merge(['domicilios' => 0]);
        }

        if(is_null($request->input('venta_linea'))){
            Input::merge(['venta_linea' => 1]);
        }

        $evento = Evento::create($request->all());        
        return $this->sendResponse($evento->toArray(), 'Evento creado con éxito');
    }

     /**
     * Lista un evento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evento = Evento::find($id);
        if (is_null($evento)) {
            return $this->sendError('Evento no encontrado');
        }
        return $this->sendResponse($evento->toArray(), 'Evento devuelto con éxito');
    }

    /**
     * Actualiza un elemeto de la tabla evento 
     *
     * [Se filtra por el ID]
     *@bodyParam fecha_evento date required Fecha del evento. Example: 2019-01-01
     *@bodyParam nombre string required Nombre del evento.
     *@bodyParam hora_inicio time Hora de inicio del evento. Example: null
     *@bodyParam hora_apertura time Hora de apertura del evento. Example: null
     *@bodyParam hora_finalizacion time Hora de finalizacion del evento. Example: null
     *@bodyParam codigo_pulep string Codigo del evento. Example: null
     *@bodyParam id_tipo_evento int  Id del tipo de evento. Defaults to 0
     *@bodyParam domicilios int Domicilios del evento. Defaults to 0
     *@bodyParam venta_linea int Venta en linea del evento. Defaults to 1
     *@bodyParam id_auditorio int required Id del auditorio del evento.
     *@bodyParam id_cliente int required Id del cliente del evento.
     *@bodyParam id_temporada int Id de la temporada del evento.
     *@bodyParam status int Status del evento.
     *@bodyParam fecha_inicio_venta_internet date Fecha de inicio de la venta por internet. Example: 2019-01-01
     *@bodyParam fecha_inicio_venta_puntos int required Cantidad de puntos de la ventas desde la fecha de inicio.
     *@bodyParam monto_minimo double Monto mínimo del evento.
     *@response{
     *       "fecha_evento" : "2019-01-03",
     *       "nombre" : "Evento WW",
     *       "hora_inicio": null,
     *       "hora_apertura": null,
     *       "hora_finalizacion" : null,
     *       "codigo_pulep": null,
     *       "id_tipo_evento": 0,
     *       "domicilios": 1,
     *       "venta_linea" : 1,
     *       "id_auditorio": 1,
     *       "id_cliente": 3,
     *       "id_temporada" : null,
     *       "status": 1,
     *       "fecha_inicio_venta_internet": "2019-01-01",
     *       "fecha_inicio_venta_puntos": 12,
     *       "monto_minimo": 150.10
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'fecha_evento' => 'required|date',
            'nombre' => 'required',
            'id_auditorio' => 'required',
            'id_cliente' => 'required',
            'fecha_inicio_venta_puntos' => 'required|integer'      
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $evento_search = Evento::find($id);
        if (is_null($evento_search)) {
            return $this->sendError('Evento no encontrado');
        } 

        $auditorio = Auditorio::find($input['id_auditorio']);
        if (is_null($auditorio)) {
            return $this->sendError('Auditorio indicado no encontrado');
        }

        $cliente = Cliente::find($input['id_cliente']);
        if (is_null($cliente)) {
            return $this->sendError('Cliente indicado no encontrado');
        }

        if(!is_null($input['hora_inicio'])){
            $validator = Validator::make($input, [
                'hora_inicio' => 'timezone',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $evento_search->hora_inicio = $input['hora_inicio'];
        }
        if(!is_null($input['hora_apertura'])){
            $validator = Validator::make($input, [
                'hora_apertura' => 'timezone',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $evento_search->hora_apertura = $input['hora_apertura'];
        }

        if(!is_null($input['hora_finalizacion'])){
            $validator = Validator::make($input, [
                'hora_finalizacion' => 'timezone',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $evento_search->hora_finalizacion = $input['hora_finalizacion'];
        }

        if(!is_null($input['fecha_inicio_venta_internet'])){
            $validator = Validator::make($input, [
                'fecha_inicio_venta_internet' => 'date',      
            ]);
            if($validator->fails()){
                return $this->sendError('Error de validación.', $validator->errors());       
            }
            $evento_search->fecha_inicio_venta_internet = $input['fecha_inicio_venta_internet'];
        }

        if(!is_null($input['id_tipo_evento'])){            
            $tipoEvento = TipoEvento::find($input['id_tipo_evento']);
            if (is_null($tipoEvento)) {
                return $this->sendError('Tipo de evento indicado no encontrado');
            }
            $evento_search->id_tipo_evento = $input['id_tipo_evento'];
        }else{
            $evento_search->id_tipo_evento = null;
        }

        if(!is_null($input['id_temporada'])){            
            $temporada = Temporada::find($input['id_temporada']);
            if (is_null($temporada)) {
                return $this->sendError('Temporada indicado no encontrada');
            }
            $evento_search->id_temporada = $input['id_temporada'];
        }

        if(is_null($input['status'])){
            $evento_search->status  = 0;
        }

        if(is_null($input['monto_minimo'])){
           $evento_search->monto_minimo = 0.00;
        }

        if(is_null($input['domicilios'])){
             $evento_search->domicilios = 0;
        }

        if(is_null($input['venta_linea'])){
            $evento_search->venta_linea = 1;
        }

        $evento_search->fecha_evento = $input['fecha_evento'];
        $evento_search->nombre = $input['nombre'];
        $evento_search->id_auditorio = $input['id_auditorio'];
        $evento_search->id_cliente = $input['id_cliente'];
        $evento_search->fecha_inicio_venta_puntos = $input['fecha_inicio_venta_puntos'];
        $evento_search->status = $input['status'];
        $evento_search->save();
        return $this->sendResponse($evento_search->toArray(), 'Evento actualizado con éxito');

    }

     /**
     * Elimina un elemento de la tabla evento
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $evento = Evento::find($id);
            if (is_null($evento)) {
                return $this->sendError('Evento no encontrado');
            }
            $evento->delete();
            return $this->sendResponse($evento->toArray(), 'Evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El evento no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }


    /**
     * Listado de los eventos por tipo
     *
     * [Se filtra por el ID del tipo de evento]
     *
     */
    public function listeventipo($id){

        $tipoEvento = TipoEvento::find($id);
        if (is_null($tipoEvento)) {
            return $this->sendError('Tipo de evento no encontrado');
        }

        $events = \DB::table('evento')
                ->join('tipo_evento', 'evento.id_tipo_evento', '=', 'tipo_evento.id')
                ->join('auditorio', 'evento.id_auditorio', '=', 'auditorio.id')
                ->where('evento.id_tipo_evento','=', $id)
                ->select('evento.id','evento.fecha_evento', 'evento.nombre','evento.hora_inicio','evento.hora_apertura', 'evento.hora_finalizacion', 'evento.fecha_inicio_venta_internet','evento.monto_minimo', 'tipo_evento.id AS id_tipo_evento','tipo_evento.nombre AS tipo_evento', 'auditorio.id AS id_auditorio','auditorio.nombre AS auditorio')
                ->get();

        $events_img = \DB::table('evento')
                      ->join('imagen_evento', 'evento.id', '=', 'imagen_evento.id_evento')
                      ->join('imagen', 'imagen_evento.id_imagen', '=', 'imagen.id')
                      ->where('evento.id_tipo_evento','=', $id)
                      ->select('imagen.nombre', 'imagen.url')
                      ->get();


        $events->first()->imagenes_evento = $events_img->toArray();
        
        
        return $this->sendResponse($events, 'Listado de evento por tipo devuelto con éxito');
    }



    /**
     * Buscar evento con filtros
     *
     * [Filtros por rango de precios, artista, tipo de evento. rango de fechas, (opcionales los parámetros de búsqueda)]
     *
     *@response{
     *  "precio_inicio": 100,
     *  "precio_fin": null,
     *  "artistas": [
     *                      {
     *                          "id_artist" : 1  
     *                      },
     *                      {
     *                          "id_artist" : 2  
     *                      }
     *               ],
     *  "tipos_evento" : [
     *                       {
     *                          "id_tipo_evento" : 1  
     *                       },
     *                       {
     *                          "id_tipo_evento" : 2  
     *                       }
     *              ],
     *  "fecha_inicio" : null,
     *  "fecha_fin" : null
     * }
     */
    public function buscar_evento(Request $request){

        $input = $request->all();
        if(count($input) > 0){
            $eventos = Evento::with('artists')
                        ->with('tipoevento')                        
                        ->with('imagens')
                        ->with('palcos')
                        ->where('status', 1)
                        ->paginate(15);

            if(!is_null($input['precio_inicio'])){
                
                $validator = Validator::make($input, [
                    'precio_inicio' => 'integer',                  
                ]);
                $variable = $input['precio_inicio'];
                $eventos = Evento::with(array('palcos' => function($query) use ($variable)
                        {  
                            $query->where('precio_venta','>=', $variable);
                        }))
                        ->with('artists')
                        ->with('tipoevento')                        
                        ->with('imagens')
                        ->where('status', 1)
                        ->get();

                if(!is_null($input['precio_fin'])){
                    $validator = Validator::make($input, [
                        'precio_fin' => 'integer',                  
                    ]);
                    $variable2 = $input['precio_fin'];
                    $eventos = Evento::with(array('palcos' => function($query) use ($variable, $variable2)
                        {  
                            $query->where('precio_venta','>=', $variable);
                            $query->where('precio_venta','<=', $variable2);
                        }))
                        ->with('artists')
                        ->with('tipoevento')                        
                        ->with('imagens')
                        ->where('status', 1)
                        ->get();
                }
            }

            if(!is_null($input['artistas'])){
                $artistas = array();
                foreach ($input['artistas'] as $key) {
                    array_push($artistas, $key["id_artist"]); 
                }
                $eventos = Evento::with(array('artists' => function($query) use ($artistas)
                        {  
                            $query->whereIn('id', $artistas);                           
                        }))
                        ->with('palcos')
                        ->with('tipoevento')                        
                        ->with('imagens')
                        ->where('status', 1)
                        ->get();
                
            }

            if(!is_null($input['tipos_evento'])){
                $tipos_evento = array();
                foreach ($input['tipos_evento'] as $key) {
                    array_push($tipos_evento, $key["id_tipo_evento"]); 
                }
                $eventos->whereIn('id_tipo_evento',$tipos_evento);
            }

            if(!is_null($input['fecha_inicio'])){
                $validator = Validator::make($input, [
                        'fecha_inicio' => 'date',                  
                    ]);
                $eventos->where('fecha_evento',$input['fecha_inicio']);
                if(!is_null($input['fecha_fin'])){
                    $validator = Validator::make($input, [
                        'fecha_fin' => 'date',                  
                    ]);
                    $eventos->whereBetween('fecha_evento',$input['fecha_inicio'], $input['fecha_fin']);
                }
            }

            
            
            $lista_eventos = compact('eventos');
            return $this->sendResponse($lista_eventos, 'Listado de eventos devuelto con éxito');

        }else{
            
            $eventos = Evento::with('artists')
                        ->with('tipoevento')
                        ->with('palcos')
                        ->with('imagens')
                        ->where('evento.status',1)                         
                        ->paginate(10);
            $lista_eventos = compact('eventos'); 

            return $this->sendResponse($lista_eventos, 'Listado de eventos devuelto con éxito');

            
        } 

        
        
        
    }

    /**
     * Detalle del evento
     *
     * [Se filtra por el ID del evento]
     *
     */
    public function detalle_evento($id){

        $evento = Evento::find($id);
        if (is_null($evento)) {
            return $this->sendError('Evento no encontrado');
        }
        $id_auditorio = $evento->id_auditorio;

        try {

        $events = \DB::table('evento')
                ->join('tipo_evento', 'evento.id_tipo_evento', '=', 'tipo_evento.id')                
                ->join('temporada', 'evento.id_temporada', '=', 'temporada.id')
                ->join('auditorio', 'evento.id_auditorio', '=', 'auditorio.id')                
                ->join('clientes', 'evento.id_cliente', '=', 'clientes.id')
                ->where('evento.id','=', $id)
                ->select('evento.fecha_evento', 'evento.nombre','evento.hora_inicio','evento.hora_apertura', 'evento.hora_finalizacion', 'evento.codigo_pulep','evento.domicilios','evento.venta_linea','evento.status','evento.fecha_inicio_venta_internet','evento.fecha_inicio_venta_puntos','tipo_evento.nombre AS tipo_evento', 'evento.monto_minimo','temporada.nombre AS nombre_temporada','temporada.status AS status_temporada', 'auditorio.id AS id_auditorio','auditorio.nombre AS auditorio', 'auditorio.ciudad AS ciudad_auditorio', 'auditorio.departamento AS departamento_auditorio', 'auditorio.pais AS pais_auditorio', 'auditorio.direccion', 'auditorio.latitud', 'auditorio.longitud', 'auditorio.aforo', 'clientes.Identificacion AS identificacion_cliente', 'clientes.tipo_identificacion', 'clientes.nombrerazon', 'clientes.direccion AS direccion_cliente', 'clientes.ciudad AS ciudad_cliente', 'clientes.departamento AS departamento_cliente', 'clientes.email', 'clientes.telefono', 'clientes.tipo_cliente')
                ->get();

        $preventas = \DB::table('evento')                
                ->join('preventa', 'preventa.id_evento', '=', 'evento.id')                
                ->where('evento.id','=', $id)
                ->select('preventa.nombre AS nombre_preventa','preventa.fecha_inicio AS fecha_inicio_preventa', 'preventa.fecha_fin AS fecha_fin_preventa', 'preventa.activo AS status_preventa')
                ->get(); 
        $events->first()->preventa_tickect = $preventas->toArray();

        $events_img = \DB::table('imagen_evento')
                      ->join('imagen', 'imagen_evento.id_imagen', '=', 'imagen.id')
                      ->where('imagen_evento.id_evento','=', $id)
                      ->select('imagen.nombre', 'imagen.url')
                      ->get();

        
        $events->first()->imagenes_evento = $events_img->toArray();

        $auditorio_img = \DB::table('imagenes_auditorio')
                      ->join('imagen', 'imagenes_auditorio.id_imagen', '=', 'imagen.id')
                      ->where('imagenes_auditorio.id_auditorio','=', $id_auditorio)
                      ->select('imagen.nombre', 'imagen.url')
                      ->get();

        $events->first()->imagenes_auditorio = $auditorio_img->toArray();

        $palcos_evento = \DB::table('palco_evento')                     
                      ->where('palco_evento.id_evento','=', $id)
                      ->select('palco_evento.id','palco_evento.id_palco')
                      ->groupBy('palco_evento.id','palco_evento.id_palco')
                      ->get();

        $palcos_full = array();

        for ($i=0; $i < count($palcos_evento); $i++) { 
            
            $info_palco = \DB::table('palco')  
                      ->join('localidad', 'palco.id_localidad', '=', 'localidad.id')                     
                      ->where('palco.id','=', $palcos_evento[$i]->id_palco)
                      ->select('palco.id AS palco_id', 'localidad.nombre AS localidad_palco')
                      ->get();

            
            $info_puestos = \DB::table('puestos_palco_evento')                         
                    ->join('palco_evento', 'puestos_palco_evento.id_palco_evento', '=', 'palco_evento.id')
                    ->join('puesto', 'puestos_palco_evento.id_puesto', '=', 'puesto.id')
                    ->join('localidad', 'localidad.id', '=', 'puesto.id_localidad')  
                    ->join('fila', 'puesto.id_fila', '=', 'fila.id')                     
                    ->where('palco_evento.id_evento','=', $id)
                    ->where('puestos_palco_evento.id_palco_evento','=', $palcos_evento[$i]->id)
                    ->where('puestos_palco_evento.id_palco','=', $palcos_evento[$i]->id_palco)
                      ->select('puesto.id AS id_puesto','puesto.numero AS numero_puesto', 'localidad.nombre AS localidad_puesto','fila.nombre AS nombre_fila', 'fila.numero AS numero_fila', 'palco_evento.id_palco','palco_evento.precio_venta', 'palco_evento.precio_servicio','palco_evento.impuesto', 'palco_evento.status')
                      
                      ->get();
            
            $info_palco->first()->puestos = $info_puestos;

            array_push($palcos_full, $info_palco);

        }
        $events->first()->palcos = $palcos_full;
        
        
        return $this->sendResponse($events, 'Deatalle del evento devuelto con éxito');
            
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrio un error', 'exception' => $e->errorInfo], 400);
        }
        
    }   
}
