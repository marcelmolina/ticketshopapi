<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use App\Models\BoletaEvento;
use App\Models\Auditorio;
use App\Models\AuditorioMapeado;
use App\Models\Cliente;
use App\Models\PreciosMonedas;
use App\Models\Temporada;
use App\Models\TipoEvento;
use App\Models\Moneda;
use App\Models\Preventum;
use App\Models\CostoEvento;
use App\Models\LocalidadEvento;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
/**
 * @group Administración de Evento
 *
 * APIs para la gestion del evento
 */
class EventoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy', 'eventos_usuario']]);        
    }

    /**
     * Lista de la tabla evento paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $evento = Evento::with('auditorio')
                    ->with("auditorio_mapeado")
                    ->with('tipoevento')
                    ->with('cliente')                    
                    ->with('temporada')
                    ->with('imagens')
                    ->with("precios_monedas")                 
                    ->paginate(15);
        return $this->sendResponse($evento->toArray(), 'Eventos devueltos con éxito');
    }


    /**
     * Lista de todos los evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function evento_all()
    {
        $evento = Evento::with('auditorio')
                    ->with("auditorio_mapeado")
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada') 
                    ->with("precios_monedas")                   
                    ->get();
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
                    ->with("auditorio_mapeado")
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada')                    
                    ->with("precios_monedas")
                ->where('evento.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($evento->toArray(), 'Todos los Eventos filtrados');
       }else{
            
            $evento = Evento::with('auditorio')
                    ->with("auditorio_mapeado")
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada')                    
                    ->with("precios_monedas")
                    ->get();
            return $this->sendResponse($evento->toArray(), 'Todos los Eventos devueltos'); 
       }
        
    }


    /**
     * Lista de eventos por artistas y tipo.
     * Precios minimos y maximos.
     *
     * @return \Illuminate\Http\Response
     */
    public function artist_evento_precios()
    {
        $eventos = Evento::with('artists')
                    ->with('tipoevento') 
                    ->where('status', 1)                   
                    ->get();

        $monedas = Moneda::get();

        $precio_maximo = 0;        
        $moneda_maximo = "";
        
        foreach ($eventos as $evento) {
           
            $boletas = PreciosMonedas::whereHas('boleta_evento', function($query) use($evento)
                        {
                             $query->where('boleta_evento.id_evento', $evento['id']);                            
                        })               
                        ->orWhereHas('palco_evento', function($query) use($evento)
                        {
                             $query->where('palco_evento.id_evento', $evento['id']);                            
                        })                        
                        ->whereNotNull('id_boleta_evento')
                        ->OrwhereNotNull('id_palco_evento')
                        ->with('moneda')
                        ->orderBy('precio_venta' ,'desc')
                        ->get();
            
            $precios_array = array();
            foreach ($monedas as $moneda) {
                
                
                $boleta_filter = array_filter($boletas->toArray(), function($bol) use($moneda){
                    return  $bol['codigo_moneda'] == $moneda->codigo_moneda;
                });
                                
                if(count($boleta_filter) > 0){
                   
                   array_push($precios_array, array('precio_venta_max' => $boleta_filter[0]['precio_venta'], 'codigo_moneda_max' => $boleta_filter[0]['codigo_moneda']));

                    if(count($boleta_filter) > 1){
                        array_push($precios_array, array('precio_venta_min' => $boleta_filter[count($boleta_filter) - 1 ]['precio_venta'], 'codigo_moneda_min' => $boleta_filter[count($boleta_filter) - 1]['codigo_moneda']));
                    }else{
                        array_push($precios_array, array('precio_venta_min' => 0, 'codigo_moneda_min' => ''));
                    }
                }
            }
            
            
            
            $evento['precios_max_min'] = $precios_array;            
            
        }

        return $this->sendResponse($eventos->toArray(), 'Eventos devueltos con éxito');
    }


    /**
     * Eventos por usuario logeado. 
     * [Se buscan los eventos del usuario logeado actualmente]
     * @return \Illuminate\Http\Response
     */
    public function eventos_usuario()
    {
       
       $email = auth()->user()->email;
       
       if(isset($email) && $email != null){            
            
            $evento = Evento::with('auditorio')
                    ->with("auditorio_mapeado")
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('imagens')
                    ->with('temporada')
                    ->with('precios_monedas')
                ->where('evento.email_usuario', $email)
                ->get();
            return $this->sendResponse($evento->toArray(), 'Eventos por usuario');
       }
       return $this->sendError('No se encuentra usuario logeado');
        
    }


    /**
     * Eventos clasificados por estado. 
     * [Todos los eventos clasificados por estado]
     *@bodyParam estado int Estado del evento.
     *@response{
     *    "estado" : "1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function eventos_estado($estado)
    {
                       
        $evento = Evento::with('auditorio')
                ->with("auditorio_mapeado")
                ->with('tipoevento')
                ->with('cliente')
                ->with('temporada')
                ->with('imagens')
                ->with("precios_monedas")
                ->where('evento.status', $estado)
                ->paginate(15);
        if(count($evento) > 0){
            return $this->sendResponse($evento->toArray(), 'Eventos por usuario');
        }
        return $this->sendError('No hay eventos con ese estado');
        
    }


    /**
     * Agrega un nuevo elemento a la tabla evento
     *
     *@bodyParam fecha_evento date required Fecha del evento. Example: 2019-01-01
     *@bodyParam fecha_finalizacion_evento date Fecha de finalización del evento. Example: 2019-01-02
     *@bodyParam nombre string required Nombre del evento.
     *@bodyParam descripcion string Descripcion del evento.
     *@bodyParam hora_inicio time Hora de inicio del evento. Example: null
     *@bodyParam hora_apertura time Hora de apertura del evento. Example: null
     *@bodyParam hora_finalizacion time Hora de finalizacion del evento. Example: null
     *@bodyParam codigo_pulep string Codigo del evento. Example: null
     *@bodyParam id_tipo_evento int  Id del tipo de evento. Defaults to 0
     *@bodyParam domicilios int Domicilios del evento. Defaults to 0
     *@bodyParam venta_linea int Venta en linea del evento. Defaults to 1
     *@bodyParam id_auditorio int required Id del auditorio del evento.
     *@bodyParam id_auditorio_mapeado int Id del auditorio mapeado del evento.
     *@bodyParam id_cliente int required Id del cliente del evento.
     *@bodyParam id_temporada int Id de la temporada del evento.
     *@bodyParam status int Status del evento.
     *@bodyParam fecha_inicio_venta_internet date Fecha de inicio de la venta por internet. Example: 2019-01-01
     *@bodyParam fecha_inicio_venta_puntos date Fecha donde va a empezar la venta de la boletería desde los puntos de venta. Example: 2019-01-01
     *@bodyParam monto_minimo double Monto mínimo del evento.
     *@bodyParam monto_minimo2 double Monto mínimo (2) del evento.
     *@bodyParam hora_inicio_venta_internet time Hora inicio de la venta por internet 
     *@bodyParam hora_inicio_venta_puntos time Hora inicio de la venta de los puntos de venta 
     *@bodyParam codigo_moneda string Codigo de la moneda.    
     *@bodyParam codigo_moneda2 string Codigo de la moneda (2).    
     *@response{
     *       "fecha_evento" : "2019-01-01",
     *       "fecha_finalizacion_evento" : "2019-01-02",
     *       "nombre" : "Evento WW",
     *       "descripcion" : null,
     *       "hora_inicio": null,
     *       "hora_apertura": null,
     *       "hora_finalizacion" : null,
     *       "codigo_pulep": null,
     *       "id_tipo_evento": 0,
     *       "domicilios": 0,
     *       "venta_linea" : 1,
     *       "id_auditorio": 2,
     *       "id_auditorio_mapeado": 1,
     *       "id_cliente": 3,
     *       "id_temporada" : 1,
     *       "status": 0,
     *       "fecha_inicio_venta_internet": null,
     *       "fecha_inicio_venta_puntos": null,
     *       "monto_minimo": 10.10,
     *       "monto_minimo2": 100,
     *       "cant_max_boletas":10,
     *       "hora_inicio_venta_internet":null,
     *       "hora_inicio_venta_puntos":null,
     *       "codigo_moneda" : "USD"   
     *       "codigo_moneda2" : "COP"   
     *     }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_evento' => 'required|date',
            'fecha_finalizacion_evento' => 'nullable|date|date_format:Y-m-d',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'id_auditorio' => 'required',
            'id_auditorio_mapeado' => 'nullable|integer',
            'id_cliente' => 'required',
            'fecha_inicio_venta_puntos' => 'nullable|date|date_format:Y-m-d',
            'hora_inicio_venta_puntos' => 'nullable|date_format:H:i',
            'hora_inicio' => 'nullable|date_format:H:i', 
            'hora_apertura' => 'nullable|date_format:H:i', 
            'hora_finalizacion' => 'nullable|date_format:H:i', 
            'fecha_inicio_venta_internet' => 'nullable|date|date_format:Y-m-d',
            'hora_inicio_venta_internet' => 'nullable|date_format:H:i',
            'cant_max_boletas' => 'nullable|integer', 
            'monto_minimo' => 'nullable|numeric',  
            'monto_minimo2' => 'nullable|numeric',  
            'codigo_moneda' => 'nullable|string',
            'codigo_moneda2' => 'nullable|string'
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

        if(!is_null($request->input('id_auditorio_mapeado'))){
            $auditorio_map = AuditorioMapeado::find($request->input('id_auditorio_mapeado'));
            if (is_null($auditorio_map)) {
                return $this->sendError('Mapeo de auditorio indicado no encontrado');
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

        if(!is_null($request->input('codigo_moneda'))){
            $moneda = Moneda::find($request->input('codigo_moneda'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda indicado no existe');
            }
        }else{
            return $this->sendError('Debe indicar moneda');
        }

        if(!is_null($request->input('monto_minimo2')) && !is_null($request->input('codigo_moneda2'))){
            $moneda = Moneda::find($request->input('codigo_moneda2'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicada no existe');
            }
        }

        $evento = Evento::create($request->all());   
        $evento->email_usuario = auth()->user()->email;
        $evento->save();

        $preciosmonedas = new PreciosMonedas();
        $preciosmonedas->id_evento = $evento->id;
        $preciosmonedas->monto_minimo = $request->input('monto_minimo');
        $preciosmonedas->codigo_moneda = $request->input('codigo_moneda');
        $preciosmonedas->save();

        if(!is_null($request->input('monto_minimo2')) && !is_null($request->input('codigo_moneda2'))){            
            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->id_evento = $evento->id;
            $preciosmonedas->monto_minimo = $request->input('monto_minimo2');
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save();
        }


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
        $evento = Evento::with("auditorio")
            ->with("auditorio_mapeado")
            ->with("tipoevento")
            ->with("cliente")
            ->with("temporada")
            ->with("imagens")
            ->with("precios_monedas.moneda", "preventa.precios_monedas.moneda")            
            ->with("costos.precios_monedas.moneda")
            ->with("localidades_evento.codigo_moneda")
            ->with("localidades_evento.codigo_moneda2")
            ->with("condiciones")            
            ->with("artists")
            ->with("auditorio_mapeado.tribunas.localidads.localidad_evento.codigo_moneda")
            ->with("auditorio_mapeado.tribunas.localidads.localidad_evento.codigo_moneda2")
            ->with("puntoventa_eventos.punto_ventum")
            ->with("preventas")            
            ->find($id);
        
        if (is_null($evento)) {
            return $this->sendError('Evento no encontrado');
        }
        
        $array_monedas = array();
        // $monedas_evento = PreciosMonedas::with('moneda')->where('id_evento', $id)->select('codigo_moneda')->get();
        // foreach ($monedas_evento->toArray() as $value) {
        //     if(!in_array($value, $array_monedas))
        //         array_push($array_monedas, $value);
        // }
        
        // $preventa = Preventum::with("precios_monedas")
        //                     ->with("precios_monedas.moneda")
        //                     ->where('id_evento', $id)
        //                     ->get();        
        // foreach ($preventa->toArray()  as $value) {
        //     foreach ($value["precios_monedas"] as $moneda) { 
        //         $campo = array('codigo_moneda' => $moneda["codigo_moneda"], 'moneda' => $moneda["moneda"]);
        //         if(!in_array($campo, $array_monedas))
        //             array_push($array_monedas, $campo);
        //     }
        // }


        // $costos = CostoEvento::with("precios_monedas")
        //                     ->with("precios_monedas.moneda")
        //                     ->where('id_evento', $id) 
        //                     ->get();
        // foreach ($costos->toArray()  as $value) {
        //     foreach ($value["precios_monedas"] as $moneda) { 
        //         $campo = array('codigo_moneda' => $moneda["codigo_moneda"], 'moneda' => $moneda["moneda"]);
        //         if(!in_array($campo, $array_monedas))
        //             array_push($array_monedas, $campo);
        //     }
        // }
        
        

        $localidades = LocalidadEvento::with("localidad")
                        ->with("codigo_moneda")
                        ->with("codigo_moneda2")
                        ->where('id_evento', $id)                        
                        ->get();

        
        foreach ($localidades->toArray()  as $value) {
            
            if($value["codigo_moneda"] != null){
                $campo = array('codigo_moneda' => $value["codigo_moneda"]['codigo_moneda'], 'moneda' => $value["codigo_moneda"]);
                if(!in_array($campo, $array_monedas)){
                    array_push($array_monedas, $campo);                    
                }
            }

            if($value["codigo_moneda2"] != null){
                $campo = array('codigo_moneda' => $value["codigo_moneda2"]['codigo_moneda'], 'moneda' => $value["codigo_moneda2"]);
                if(!in_array($campo, $array_monedas)){
                    array_push($array_monedas, $campo);
                }

            }
            
        }
        
       $event = $evento->toArray();       
       
       $event['monedas_evento'] = $array_monedas;

        return $this->sendResponse($event, 'Evento devuelto con éxito');
    }

    /**
     * Actualiza un elemeto de la tabla evento 
     *
     * [Se filtra por el ID]
     *@bodyParam fecha_evento date required Fecha del evento. Example: 2019-01-01
     *@bodyParam fecha_finalizacion_evento date Fecha de finalización del evento. Example: 2019-01-02
     *@bodyParam nombre string required Nombre del evento.
     *@bodyParam descripcion string Descripcion del evento.
     *@bodyParam hora_inicio time Hora de inicio del evento. Example: null
     *@bodyParam hora_apertura time Hora de apertura del evento. Example: null
     *@bodyParam hora_finalizacion time Hora de finalizacion del evento. Example: null
     *@bodyParam codigo_pulep string Codigo del evento. Example: null
     *@bodyParam id_tipo_evento int  Id del tipo de evento. Defaults to 0
     *@bodyParam domicilios int Domicilios del evento. Defaults to 0
     *@bodyParam venta_linea int Venta en linea del evento. Defaults to 1
     *@bodyParam id_auditorio int required Id del auditorio del evento.
     *@bodyParam id_auditorio_mapeado int Id del auditorio mapeado del evento.
     *@bodyParam id_cliente int required Id del cliente del evento.
     *@bodyParam id_temporada int Id de la temporada del evento.
     *@bodyParam status int Status del evento.
     *@bodyParam fecha_inicio_venta_internet date Fecha de inicio de la venta por internet. Example: 2019-01-01
     *@bodyParam fecha_inicio_venta_puntos date Fecha donde va a empezar la venta de la boletería desde los puntos de venta. Example: 2019-01-01
     *@bodyParam monto_minimo double Monto mínimo del evento.
     *@bodyParam monto_minimo2 double Monto mínimo (2) del evento.
     *@bodyParam hora_inicio_venta_internet time Hora inicio de la venta por internet 
     *@bodyParam hora_inicio_venta_puntos time Hora inicio de la venta de los puntos de venta 
     *@bodyParam codigo_moneda string Codigo de la moneda.    
     *@bodyParam codigo_moneda2 string Codigo de la moneda (2).  
     *@response{
     *       "fecha_evento" : "2019-01-03",
     *       "fecha_finalizacion_evento" : "2019-01-04",
     *       "nombre" : "Evento WW",
     *       "descripcion" : "Evento WWTC",
     *       "hora_inicio": null,
     *       "hora_apertura": null,
     *       "hora_finalizacion" : null,
     *       "codigo_pulep": null,
     *       "id_tipo_evento": 0,
     *       "domicilios": 1,
     *       "venta_linea" : 1,
     *       "id_auditorio": 1,
     *       "id_auditorio_mapeado": 2,
     *       "id_cliente": 3,
     *       "id_temporada" : null,
     *       "status": 1,
     *       "fecha_inicio_venta_internet": "2019-01-01",
     *       "fecha_inicio_venta_puntos": "2019-01-04",
     *       "monto_minimo": 150.10,
     *       "monto_minimo": 1900,
     *       "cant_max_boletas":10,
     *       "hora_inicio_venta_internet":null,
     *       "hora_inicio_venta_puntos":null,
     *       "codigo_moneda" : "USD"
     *       "codigo_moneda" : "COP"  
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
            'fecha_finalizacion_evento' => 'nullable|date|date_format:Y-m-d',
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
            'id_auditorio' => 'required',
            'id_auditorio_mapeado' => 'nullable|integer',
            'id_cliente' => 'required',
            'fecha_inicio_venta_puntos' => 'nullable|date|date_format:Y-m-d',
            'hora_inicio_venta_puntos' => 'nullable|date_format:H:i',
            'hora_inicio' => 'nullable|date_format:H:i', 
            'hora_apertura' => 'nullable|date_format:H:i', 
            'hora_finalizacion' => 'nullable|date_format:H:i', 
            'fecha_inicio_venta_internet' => 'nullable|date|date_format:Y-m-d',
            'hora_inicio_venta_internet' => 'nullable|date_format:H:i',
            'cant_max_boletas' => 'nullable|integer',
            'monto_minimo' => 'nullable|numeric',  
            'monto_minimo2' => 'nullable|numeric',  
            'codigo_moneda' => 'nullable|string',
            'codigo_moneda2' => 'nullable|string'       
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        } 

        if(!is_null($request->input('codigo_moneda2'))){
            $moneda = Moneda::find($request->input('codigo_moneda2'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicada no existe');
            }
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
            $evento_search->hora_inicio = $input['hora_inicio'];
        }

        if(!is_null($input['hora_apertura'])){            
            $evento_search->hora_apertura = $input['hora_apertura'];
        }

        if(!is_null($input['hora_finalizacion'])){            
            $evento_search->hora_finalizacion = $input['hora_finalizacion'];
        }

        if(!is_null($input['fecha_inicio_venta_internet'])){            
            $evento_search->fecha_inicio_venta_internet = $input['fecha_inicio_venta_internet'];
        }

        if(!is_null($input['fecha_inicio_venta_puntos'])){            
            $evento_search->fecha_inicio_venta_puntos = $input['fecha_inicio_venta_puntos'];
        }

        if(!is_null($input['fecha_finalizacion_evento'])){
            $evento_search->fecha_finalizacion_evento = $input['fecha_finalizacion_evento'];
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

        if(!is_null($request->input('id_auditorio_mapeado'))){
            $auditorio_map = AuditorioMapeado::find($request->input('id_auditorio_mapeado'));
            if (is_null($auditorio_map)) {
                return $this->sendError('Mapeo de auditorio indicado no encontrado');
            }
            $evento_search->id_auditorio_mapeado = $input['id_auditorio_mapeado'];
        }else{
            $evento_search->id_auditorio_mapeado = null;
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
        }else{
            $evento_search->status = $input['status'];
        }

        if(is_null($input['monto_minimo'])){
           $evento_search->monto_minimo = 0.00;           
        }else{
            $evento_search->monto_minimo = $input['monto_minimo'];           
        }
        

        if(is_null($input['domicilios'])){
             $evento_search->domicilios = 0;
        }else{
            $evento_search->domicilios = $input['domicilios'];
        }

        if(is_null($input['venta_linea'])){
            $evento_search->venta_linea = 1;
        }else{
            $evento_search->venta_linea = $input['venta_linea'];
        }
       

        $evento_search->descripcion = $input['descripcion'];
        $evento_search->fecha_evento = $input['fecha_evento'];
        $evento_search->nombre = $input['nombre'];
        $evento_search->id_auditorio = $input['id_auditorio'];
        $evento_search->id_cliente = $input['id_cliente'];       

        if(is_null($input['cant_max_boletas'])){
            $evento_search->cant_max_boletas = null;
        }else{
            $evento_search->cant_max_boletas = $input['cant_max_boletas'];
        }

        $evento_search->save();


        $pmonedas_search = PreciosMonedas::where('id_evento', $id)->get();

        if(count($pmonedas_search) > 1){
            $i = "";
            foreach ($pmonedas_search as $valuekey) {
                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'monto_minimo' => $input['monto_minimo'.$i],
                                'codigo_moneda' => $input['codigo_moneda'.$i]
                            ]);
                $i = "2";

            }            
            
        }else{
            foreach ($pmonedas_search as $valuekey) {                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'monto_minimo' => $input['monto_minimo'],
                                'codigo_moneda' => $input['codigo_moneda']
                            ]); 
            }

            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->monto_minimo = $input['monto_minimo2'];
            $preciosmonedas->id_evento = $id;
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save(); 
        }


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

        $evento = Evento::with('auditorio')
                    ->with("auditorio_mapeado")
                    ->with('tipoevento')
                    ->with('cliente')
                    ->with('temporada')
                    ->with('imagens')
                    ->with("precios_monedas")
                ->where('evento.id_tipo_evento', $id)
                ->get();
        
        return $this->sendResponse($evento->toArray(), 'Listado de evento por tipo devuelto con éxito');
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

            $eventos = Evento::with('imagens');
                       

            if(!is_null($input['precio_inicio'])){
                
                $validator = Validator::make($input, [
                        'precio_inicio' => 'nullable|integer',                  
                ]);
                if($validator->fails()){
                    return $this->sendError('Error de validación.', $validator->errors());       
                }
                $variable = $input['precio_inicio'];

                if(!is_null($input['precio_fin'])){
                    $validator = Validator::make($input, [
                        'precio_fin' => 'nullable|integer',                  
                    ]);
                    if($validator->fails()){
                        return $this->sendError('Error de validación.', $validator->errors());       
                    }
                    $variable2 = $input['precio_fin'];
                    $eventos->with(array('palcos' => function($query) use ($variable, $variable2)
                        {  
                            $query->where('precio_venta','>=', $variable);
                            $query->where('precio_venta','<=', $variable2);
                        }));
                }else{

                    $eventos->with(array('palcos' => function($query) use ($variable)
                        {  
                            $query->where('precio_venta','>=', $variable);
                        }));
                }
                
            }

            if(!is_null($input['artistas'])){
                $artistas = $input['artistas'];
                $eventos->with(array('artists' => function($query) use ($artistas)
                        {  
                            $item = 0;
                            foreach ($artistas as $key) {
                                if($item == 0){
                                    $query->where('id_artista', $key);
                                }else{
                                    $query->orWhere('id_artista', $key);
                                }
                                $item = $item + 1;
                            }
                            $query->whereNotNull('id_artista');

                        }));
            }else{
                $eventos->with('artists');
            }

            if(!is_null($input['tipos_evento'])){
                $tipos_evento = array();
                foreach ($input['tipos_evento'] as $key) {
                    array_push($tipos_evento, $key); 
                }
                $eventos->with('tipoevento')->whereIn('id_tipo_evento',$tipos_evento);
            }else{
                $eventos->with('tipoevento');
            }


            if(!is_null($input['fecha_inicio'])){
                $validator = Validator::make($input, [
                        'fecha_inicio' => 'date',                  
                    ]);                
                if(!is_null($input['fecha_fin'])){
                    $validator = Validator::make($input, [
                        'fecha_fin' => 'date',                  
                    ]);
                    $eventos->whereBetween('fecha_evento',[$input['fecha_inicio'], $input['fecha_fin']]);
                }else{
                    $eventos->whereDate('fecha_evento',$input['fecha_inicio']);
                }
            }

            $eventos->with('codigo_moneda');

            
            $lista_eventos = $eventos->where('evento.status', 1)
                            ->get();

            $filtrado = json_decode($lista_eventos);                
            $filtrado = array_filter($filtrado, function($val) { return $val->artists != null; });
            $lista_eventos = $filtrado;
            return $this->sendResponse($lista_eventos, 'Listado de eventos devuelto con éxito');

        }else{
            
            $eventos = Evento::with('artists')
                        ->with('tipoevento')
                        ->with('auditorio_mapeado')
                        ->with('palcos')
                        ->with('imagens')
                        ->with('precios_monedas')
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
        try {

            $events = Evento::with('artists')
                        ->with('tipoevento')
                        ->with('auditorio_mapeado')
                        ->with('preventa')
                        ->with('imagens')
                        ->with('boleta_eventos')
                        ->with('palcos')
                        ->with('precios_monedas')
                        ->get();
            
            return $this->sendResponse($events, 'Deatalle del evento devuelto con éxito');
            
        } catch (Exception $e) {
            return response()->json(['error' => 'Ocurrio un error', 'exception' => $e->errorInfo], 400);
        }
        
    } 
}
