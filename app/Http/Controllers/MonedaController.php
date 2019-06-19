<?php

namespace App\Http\Controllers;

use App\Models\Moneda;
use Illuminate\Http\Request;
use Validator;

/**
 * @group Administración de Moneda 
 *
 * APIs para la gestion de moneda
 */
class MonedaController extends BaseController
{
    /**
     * Lista de la tabla moneda.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $moneda = Moneda::paginate(15);

        return $this->sendResponse($moneda->toArray(), 'Monedas devueltas con éxito');
    }

    /**
     * Buscar Moneda por descripción.
     *@bodyParam descripcion string Descripción de la moneda.
     *@response{
     *    "descripcion" : "Peso Colombiano",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarMoneda(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["descripcion"]) && $input["descripcion"] != null){
            
            $input = $request->all();
            $monedas = Moneda::where('descripcion','like', '%'.strtolower($input["descripcion"]).'%')
                ->get();
            return $this->sendResponse($monedas->toArray(), 'Todas las moneda filtradas');
       }else{
            
            $monedas = Moneda::get();
            return $this->sendResponse($monedas->toArray(), 'Todas las monedas devueltas'); 
       }

        
    }

    /**
     * Agrega un nuevo elemento a la tabla moneda
     *@bodyParam codigo_moneda string required Código de la moneda (Clave primaria).
     *@bodyParam descripcion string required Descripcion de la moneda.
     *@bodyParam simbolo string required Símbolo de la moneda.
     *
     *@response{
     *    "codigo_moneda" : "COL",
     *    "descripcion" : "Peso Colombiano" 
     *    "simbolo" : "$"
     * }
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_moneda' => 'required|max:10',
            'descripcion' => 'required|max:200',
            'simbolo' => 'required|max:5'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $moneda= Moneda::create($request->all());        
        return $this->sendResponse($moneda->toArray(), 'Moneda creada con éxito');
    }

    /**
     * Lista una moneda en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Moneda  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $moneda = Moneda::find($id);
        if (is_null($moneda)) {
            return $this->sendError('Moneda no encontrada');
        }
        return $this->sendResponse($moneda->toArray(), 'Moneda devuelta con éxito');
    }

  
    /**
     * Actualiza un elemeto de la tabla moneda
     *@bodyParam descripcion string required Descripcion de la moneda.
     *@bodyParam simbolo string required Símbolo de la moneda.
     *
     *@response{
     *    "descripcion" : "Pesos Colombiano" 
     *    "simbolo" : "$"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Moneda  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'descripcion' => 'required|max:200',
            'simbolo' => 'required|max:5'             
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $moneda = Moneda::find($id);        
        if (is_null($moneda)) {
            return $this->sendError('Moneda no encontrada');
        }
        $moneda->descripcion = $input['descripcion'];
        $moneda->simbolo = $input['simbolo'];
        $moneda->save();

        return $this->sendResponse($moneda->toArray(), 'Moneda actualizada con éxito');
    }

    /**
     * Elimina un elemento de la tabla moneda
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Moneda  $moneda
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {       

        try {
            $moneda = Moneda::find($id);
            if (is_null($moneda)) {
                return $this->sendError('Moneda no encontrada');
            }
            $moneda->delete();
            return $this->sendResponse($moneda->toArray(), 'Moneda eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La moneda no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
