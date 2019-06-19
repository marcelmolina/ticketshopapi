<?php

namespace App\Http\Controllers;

use App\Models\Tasa;
use App\Models\Moneda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;
use Carbon\Carbon;
/**
 * @group Administración de Tasa 
 *
 * APIs para la gestion de la tabla tasa
 */
class TasaController extends BaseController
{
    /**
     * Lista de la tabla tasa.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $tasa = Tasa::with('moneda_alta')->with('moneda_baja')->paginate(15);
        return $this->sendResponse($tasa->toArray(), 'Tasas devueltas con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla tasa.
     *@bodyParam codigo_moneda_alta string required Codigo de la moneda (Alta).
     *@bodyParam codigo_moneda_baja string required Codigo de la moneda (Baja).
     *@bodyParam valor string required Valor de conversión.
     *@bodyParam activo string Estado de la tasa.
     *@response{
     *    "codigo_moneda_alta" : "COL",
     *    "codigo_moneda_baja" : "USD",
     *    "valor" : 3230,
     *    "activo" : 1,
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_moneda_alta' => 'required',
            'codigo_moneda_baja' => 'required',
            'valor' => 'required',
            'activo' => 'nullable'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $moneda_alta = Moneda::find($request->input('codigo_moneda_alta'));
        if (is_null($moneda_alta)) {
            return $this->sendError('El codigo de la moneda (ALTA) indicada no existe');
        }

        $moneda_baja = Moneda::find($request->input('codigo_moneda_baja'));
        if (is_null($moneda_baja)) {
            return $this->sendError('El codigo de la moneda (BAJA) indicada no existe');
        }
        
        if(is_null($request->input('activo'))){
            Input::merge(['activo' => 1]);
        }

        $fecha_actual = Carbon::now();        
        Input::merge(['fecha_hora' => $fecha_actual]);

        $tasa = Tasa::create($request->all());        
        return $this->sendResponse($tasa->toArray(), 'Tasa creada con éxito');
    }

    /**
     * Lista de una tasa en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Tasa  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tasa = Tasa::with('moneda_alta')
                            ->with('moneda_baja')
                            ->where('id','=',$id)
                            ->get();
        if (count($tasa) == 0) {
            return $this->sendError('La tasa no se encuentra');
        }
        return $this->sendResponse($tasa->toArray(), 'Tasa devuelta con éxito');
    }

    /**
     *  Actualiza un elemento a la tabla tasa.
     *
     *@bodyParam codigo_moneda_alta string required Codigo de la moneda (Alta).
     *@bodyParam codigo_moneda_baja string required Codigo de la moneda (Baja).
     *@bodyParam valor string required Valor de conversión.
     *@bodyParam activo string Estado de la tasa.
     *@response{
     *    "codigo_moneda_alta" : "COL",
     *    "codigo_moneda_baja" : "USD",
     *    "valor" : 3230,
     *    "activo" : 1,
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tasa  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'codigo_moneda_alta' => 'required',
            'codigo_moneda_baja' => 'required',
            'valor' => 'required',
            'activo' => 'nullable'
        ]);

        $moneda_alta = Moneda::find($input['codigo_moneda_alta']);
        if (is_null($moneda_alta)) {
            return $this->sendError('El codigo de la moneda (ALTA) indicada no existe');
        }

        $moneda_baja = Moneda::find($input['codigo_moneda_baja']);
        if (is_null($moneda_baja)) {
            return $this->sendError('El codigo de la moneda (BAJA) indicada no existe');
        }

        $tasa_search = Tasa::find($id);
        if (is_null($tasa_search)) {
            return $this->sendError('Tasa no encontrada');
        }

        if(is_null($input['activo'])){
            $tasa_search->activo  = 1;
        }else{
            $tasa_search->activo  = $input['activo'];
        }

        $fecha_actual = Carbon::now();        
        $tasa_search->fecha_hora = $fecha_actual;

        $tasa_search->codigo_moneda_alta = $input['codigo_moneda_alta'];
        $tasa_search->codigo_moneda_baja = $input['codigo_moneda_baja'];
        $tasa_search->valor = $input['valor']; 

        $tasa_search->save();
        return $this->sendResponse($tasa_search->toArray(), 'Tasa actualizada con éxito');

    }

    /**
     * Elimina un elemento de la tabla tasa
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Tasa  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 
            $tasa = Tasa::find($id);
            if (is_null($tasa)) {
                return $this->sendError('Tasa no encontrada');
            }
            $tasa->delete();
            return $this->sendResponse($tasa->toArray(), 'Tasa eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La tasa no se puede eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }


    /**
     * Metodo para convertir moneda
     * [Metodo POST]
     *@bodyParam moneda_origen string required Codigo de la moneda (Alta).
     *@bodyParam moneda_destino string required Codigo de la moneda (Baja).
     *@bodyParam cantidad float required Cantidad a convertir.
     *@response{
     *    "moneda_origen" : "COL",
     *    "moneda_destino" : "USD",
     *    "cantidad" : 3230
     * } 
     *
     */
    public function convertir(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'moneda_origen' => 'required',
            'moneda_destino' => 'required',
            'cantidad' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $moneda_alta = $request->input('moneda_origen');
        $moneda_baja = $request->input('moneda_destino');

        $cantidad = $request->input('cantidad');

        $tasa_mult = Tasa::where('codigo_moneda_alta','=', $moneda_alta)
                ->where('codigo_moneda_baja','=', $moneda_baja)
                ->orderBy('fecha_hora','desc')
                ->take(1)->get();
        
        if (count($tasa_mult) == 0) {
            $tasa_div = Tasa::where('codigo_moneda_alta','=', $moneda_baja)
                ->where('codigo_moneda_baja','=', $moneda_alta)
                ->orderBy('fecha_hora', 'desc')
                ->take(1)->get();
            
            if (count($tasa_div) == 0) {
                return $this->sendError('Codigos de moneda no se encuentran registrados. No es posible hacer la conversión.');   
            }else{
               
               $valor_conv =  (float) $tasa_div[0]["valor"];               
               $cant_convertida = number_format((float)($cantidad/$valor_conv), 2, '.', '');
                
               $result = ["moneda_alta" => $moneda_alta, "moneda_baja" => $moneda_baja, "cantidad" => $cantidad, "tasa" => $valor_conv, "cantidad_convertida" => $cant_convertida];

               return $this->sendResponse($result, 'Cantidad convertidad exitosamente');
            }
            
        }else{
            $valor_conv =  (float) $tasa_mult[0]["valor"];
            $cant_convertida = (float)($cantidad * $valor_conv);
            
            $result = ["moneda_alta" => $moneda_alta, "moneda_baja" => $moneda_baja, "cantidad" => $cantidad, "tasa" => $valor_conv, "cantidad_convertida" => $cant_convertida];

            return $this->sendResponse($result, 'Cantidad convertidad exitosamente');
        }


    }
}
