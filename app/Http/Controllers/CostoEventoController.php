<?php

namespace App\Http\Controllers;

use App\Models\CostoEvento;
use App\Models\TipoCosto;
use App\Models\Evento;
use App\Models\Moneda;
use App\Models\PreciosMonedas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
/**
 * @group Administración de CostoEvento
 *
 * APIs para la gestion de la costo_evento
 */
class CostoEventoController extends BaseController
{
    

    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy', 'destroyxevento']]);
    }

    /**
     * Lista de la tabla costo_evento.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $costo_evento = CostoEvento::with("evento")
                            ->with("tipo_costo")
                            ->with("precios_monedas")
                            ->paginate(15);
        return $this->sendResponse($costo_evento->toArray(), 'Costos de los eventos devueltos con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla costo_evento.
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_tipo_costo int required ID del tipo de costo.
     *@bodyParam descripcion string required Descripción del costo del evento.
     *@bodyParam valor float Valor del costo del evento.
     *@bodyParam codigo_moneda string required Codigo de la moneda.
     *@bodyParam valor2 float Valor 2 del costo del evento.
     *@bodyParam codigo_moneda2 string Codigo de la moneda 2.
     *@response{
     *    "id_evento" : 3,
     *    "id_tipo_costo" : 2,
     *    "descripcion": "Costo Promocional",
     *    "valor": 100,
     *    "codigo_moneda": "USD",
     *    "valor2": 1000,
     *    "codigo_moneda2": "COP"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_evento' => 'required|integer',
            'id_tipo_costo' => 'required|integer',
            'descripcion' => 'required',
            'valor' => 'nullable',
            'codigo_moneda' => 'required|string',
            'valor2' => 'nullable',
            'codigo_moneda2' => 'nullable|string'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $evento = Evento::find($request->input('id_evento'));
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $tipo_costo = TipoCosto::find($request->input('id_tipo_costo'));
        if (is_null($tipo_costo)) {
            return $this->sendError('El tipo de costo indicado no existe');
        }

        $moneda = Moneda::find($request->input('codigo_moneda'));
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        if(!is_null($request->input('codigo_moneda2'))){
            $moneda = Moneda::find($request->input('codigo_moneda2'));
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicada no existe');
            }
        }

        if(is_null($request->input('valor'))){
            Input::merge(['valor' => 0.00]);
        }

        $costo_evento = CostoEvento::create($request->all());  

        $preciosmonedas = new PreciosMonedas();
        $preciosmonedas->id_costo_evento = $costo_evento->id;
        $preciosmonedas->valor = $request->input('valor');
        $preciosmonedas->codigo_moneda = $request->input('codigo_moneda');
        $preciosmonedas->save();

        if(!is_null($request->input('codigo_moneda2'))){ 

            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->id_costo_evento = $costo_evento->id;
            $preciosmonedas->valor = $request->input('valor2');
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save();

        }


        return $this->sendResponse($costo_evento->toArray(), 'Costo del evento creado con éxito');
    }

    /**
     * Lista un costo de evento en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\CostoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $costo_evento = CostoEvento::with("evento")
                            ->with("tipo_costo")
                            ->with("precios_monedas")
                            ->where('id','=',$id)
                            ->get();
        if (count($costo_evento) == 0) {
            return $this->sendError('El costo de evento no se encuentra');
        }
        return $this->sendResponse($costo_evento->toArray(), 'Costo del evento devuelto con éxito');
    }

    /**
     * Costos de un evento en específico 
     *
     * [Se filtra por el ID del Evento]
     *
     * @param  \App\Models\CostoEvento  $id_evento
     * @return \Illuminate\Http\Response
     */
    public function costos_evento($id_evento)
    {
        $costo_evento = CostoEvento::with("evento")
                            ->with("tipo_costo")
                            ->with("precios_monedas")
                            ->where('id_evento','=',$id_evento)
                            ->get();
        if (count($costo_evento) == 0) {
            return $this->sendError('El costo de evento no se encuentra');
        }
        return $this->sendResponse($costo_evento->toArray(), 'Costos del evento devuelto con éxito');
    }

    /**
     * Actualiza un elemento a la tabla costo_evento.
     *
     *@bodyParam id_evento int required ID del evento.
     *@bodyParam id_tipo_costo int required ID del tipo de costo.
     *@bodyParam descripcion string required Descripción del costo del evento.
     *@bodyParam valor float Valor del costo del evento.
     *@bodyParam codigo_moneda string required Codigo de la moneda.
     *@bodyParam valor2 float Valor 2 del costo del evento.
     *@bodyParam codigo_moneda2 string Codigo de la moneda 2.
     *@response{
     *    "id_evento" : 4,
     *    "id_tipo_costo" : 2,
     *    "descripcion": "Costo Familiar",
     *    "valor": 100,
     *    "codigo_moneda": "USD",
     *    "valor2": 1000,
     *    "codigo_moneda2": "COP"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CostoEvento  $costoEvento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id_evento' => 'required|integer',
            'id_tipo_costo' => 'required|integer',
            'descripcion' => 'required',
            'valor' => 'nullable',
            'codigo_moneda' => 'required',
            'valor2' => 'nullable',
            'codigo_moneda2' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        $evento = Evento::find($input['id_evento']);
        if (is_null($evento)) {
            return $this->sendError('El evento indicado no existe');
        }

        $tipo_costo = TipoCosto::find($input['id_tipo_costo']);
        if (is_null($tipo_costo)) {
            return $this->sendError('El tipo de costo indicado no existe');
        }

        $moneda = Moneda::find($input['codigo_moneda']);
        if (is_null($moneda)) {
            return $this->sendError('La moneda indicado no existe');
        }

        if(!is_null($input['codigo_moneda2'])){
            $moneda = Moneda::find($input['codigo_moneda2']);
            if (is_null($moneda)) {
                return $this->sendError('La moneda 2 indicado no existe');
            }
        }

        $costoevento_search = CostoEvento::find($id);
        if (is_null($costoevento_search)) {
            return $this->sendError('Costo del evento no encontrado');
        }
        $cd_moneda_last = $costoevento_search->codigo_moneda;

        $costoevento_search->id_evento = $input['id_evento'];
        $costoevento_search->id_tipo_costo = $input['id_tipo_costo'];
        $costoevento_search->descripcion = $input['descripcion'];        

        $costoevento_search->save();


        $pmonedas_search = PreciosMonedas::where('id_costo_evento', $id)->get();

        if(count($pmonedas_search) > 1){
            $i = "";
            foreach ($pmonedas_search as $valuekey) {
                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'valor' => $input['valor'.$i],
                                'codigo_moneda' => $input['codigo_moneda'.$i]
                            ]);
                $i = "2";

            }            
            
        }else{

            foreach ($pmonedas_search as $valuekey) {                
                PreciosMonedas::find($valuekey['id'])
                    ->update([
                                'valor' => $input['valor'],
                                'codigo_moneda' => $input['codigo_moneda']
                            ]); 
            }

            $preciosmonedas = new PreciosMonedas();
            $preciosmonedas->valor = $input['valor2'];
            $preciosmonedas->id_costo_evento = $id;
            $preciosmonedas->codigo_moneda = $request->input('codigo_moneda2');
            $preciosmonedas->save(); 

        }


        return $this->sendResponse($costoevento_search->toArray(), 'Costo del evento actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla costo_evento
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\CostoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $costo_evento = CostoEvento::find($id);
            if (is_null($costo_evento)) {
                return $this->sendError('Costo del evento no encontrado');
            }
            $costo_evento->delete();
            return $this->sendResponse($costo_evento->toArray(), 'Costo del evento eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El costo del evento no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }

    /**
     * Elimina los costos del evento
     *
     * [Se filtra por el ID del evento]
     *
     * @param  \App\Models\CostoEvento  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyxevento($id)
    {
        try { 
            $costo_evento = CostoEvento::where('id_evento', $id)->get();
            if (count($costo_evento) == 0) {
                return $this->sendError('No se encuentran costos por evento');
            }
            CostoEvento::where('id_evento', $id)->delete();
            return $this->sendResponse($costo_evento->toArray(), 'Costos por evento eliminados con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Los costos por evento no se puede eliminar', 'exception' => $e->errorInfo], 400);
        }
    }
}
