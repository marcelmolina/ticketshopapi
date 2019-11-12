<?php

namespace App\Http\Controllers;

use App\Models\TipoCosto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;

/**
 * @group Administración de Tipo Costo
 *
 * APIs para la gestion de tipo_costo
 */
class TipoCostoController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'edit', 'update', 'destroy']]);
    }


    /**
     * Lista de la tabla tipo costo paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipo_costo = TipoCosto::paginate(15);
        return $this->sendResponse($tipo_costo->toArray(), 'Tipos de costo devueltos con éxito');
    }


    /**
     * Lista de los tipos de costo.
     *
     * @return \Illuminate\Http\Response
     */
    public function tipo_costo_all()
    {
        $tipo_costo = TipoCosto::get();
        return $this->sendResponse($tipo_costo->toArray(), 'Tipos de costo devueltos con éxito');
    }
    

     /**
     * Buscar Tipo de costo por descripción.
     *@bodyParam nombre string Nombre del tipo de costo.
     *@response{
     *    "nombre" : "Condicion 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarTipoCosto(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $tipo_costos = TipoCosto::where('descripcion','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($tipo_costos->toArray(), 'Todos los tipos de costos filtrados');
       }else{
            
            $tipo_costos = TipoCosto::get();
            return $this->sendResponse($tipo_costos->toArray(), 'Todos los tipos de costos devueltos'); 
       }

        
    }

  

    /**
     * Agrega un nuevo elemento a la tabla tipo_costo
     *@bodyParam descripcion string required Tipo Costo del evento.     
     *@response{
     *       "descripcion" : "Costo Familiar"             
     *     }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion'=> 'required'                       
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $tipo_costo = TipoCosto::create($request->all());        
        return $this->sendResponse($tipo_costo->toArray(), 'Tipo de costo creado con éxito');
    }

    /**
     * Lista un tipo de costo en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoCosto  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipo_costo = TipoCosto::find($id);
        if (is_null($tipo_costo)) {
            return $this->sendError('Tipo de costo no encontrado');
        }
        return $this->sendResponse($tipo_costo->toArray(), 'Tipo de costo devuelto con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla tipo_costo 
     *
     * [Se filtra por el ID]
     *@bodyParam descripcion string required Tipo Costo del evento.     
     *@response{
     *       "descripcion" : "Costo Juvenil"             
     *     }
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoCosto  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'descripcion'=> 'required'                       
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $tipo_costo = TipoCosto::find($id);
        if (is_null($tipo_costo)) {
            return $this->sendError('Tipo de costo no encontrado');
        }

        $tipo_costo->descripcion = $input['descripcion'];        
        $tipo_costo->save();

        return $this->sendResponse($tipo_costo->toArray(), 'Tipo de costo actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla tipo_costo
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoCosto  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $tipo_costo =TipoCosto::find($id);
            if (is_null($tipo_costo)) {
                return $this->sendError('Tipo de costo no encontrada');
            }
            $tipo_costo->delete();
            return $this->sendResponse($tipo_costo->toArray(), 'Tipo de costo eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Tipo de costo no se puedo eliminar, el registro esta siendo usado en la tabla costo_evento', 'exception' => $e->errorInfo], 400);
        }
    }
}
