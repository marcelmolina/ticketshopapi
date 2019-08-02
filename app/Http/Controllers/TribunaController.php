<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\Tribuna;
use App\Models\Auditorio;
use Illuminate\Support\Facades\Input;
/**
 * @group Administración de Tribuna
 *
 * APIs para la gestion de la tabla tribuna
 */
class TribunaController extends BaseController
{
     /**
     * Lista de la tabla tribuna paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tribuna = Tribuna::paginate(15);
        return $this->sendResponse($tribuna->toArray(), 'Tribunas devueltas con éxito');
    }


    /**
     * Lista de las tribunas.
     *
     * @return \Illuminate\Http\Response
     */
    public function tribuna_all()
    {
        $tribuna = Tribuna::get();
        return $this->sendResponse($tribuna->toArray(), 'Tribunas devueltas con éxito');
    }

    /**
     * Listado detallado de las tribunas.
     *
     * @return \Illuminate\Http\Response
     */
    public function listado_detalle_tribunas()
    {
        
        $tribunas = Tribuna::with('auditorio')->paginate(15);       
        return $this->sendResponse($tribunas, 'Tribunas devueltas con éxito');
    }


    /**
     * Buscar tribuna por descripción.
     *@bodyParam nombre string Nombre de la tribuna.
     *@response{
     *    "nombre" : "Tribuna",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarTribuna(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $generos = Tribuna::with('auditorio')
                        ->where('tribuna.nombre','like', '%'.strtolower($input["nombre"]).'%')
                        ->get();
            return $this->sendResponse($generos->toArray(), 'Todas las tribunas filtradas');
       }else{
            
            $generos = Tribuna::with('auditorio')->get();
            return $this->sendResponse($generos->toArray(), 'Todas las tribunas devueltas'); 
       }

        
    }
    

    /**
     * Agrega un nuevo elemento a la tabla tribuna
     *@bodyParam nombre string required Nombre de la tribuna.
     *@bodyParam id_auditorio int required Id del auditorio.
     * @response {      
     *  "nombre": "Tribuna Gold", 
     *  "id_auditorio": 1    
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'nombre' => 'required',            
            'id_auditorio' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $auditorio = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio)) {
            return $this->sendError('El Auditorio indicado no existe');
        }

        $tribuna = Tribuna::create($request->all());        
        return $this->sendResponse($tribuna->toArray(), 'Tribuna creada con éxito');
    }

    /**
     * Lista una tribuna en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $tribuna = Tribuna::find($id);

        if (is_null($tribuna)) {
            return $this->sendError('Tribuna no encontrada');
        }

        return $this->sendResponse($tribuna->toArray(), 'Tribuna devuelta con éxito');
    }
  

    /**
     * Actualiza un elemeto de la tabla tribuna 
     *
     * [Se filtra por el ID]
     *@bodyParam nombre string required Nombre de la tribuna.
     *@bodyParam id_auditorio int required Id del auditorio.
     * @response {
     *  "nombre": "Tribuna Gold New", 
     *  "id_auditorio": 2    
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',            
            'id_auditorio' => 'required',           
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        $auditorio_search = Auditorio::find($request->input('id_auditorio'));
        if (is_null($auditorio_search)) {
            return $this->sendError('El Auditorio indicado no existe');
        }

        $tribuna_search = Tribuna::find($id);        
        if (is_null($tribuna_search)) {
            return $this->sendError('Tribuna no encontrada');
        }

        $tribuna_search->nombre = $input['nombre'];
        $tribuna_search->id_auditorio = $input['id_auditorio'];          
        $tribuna_search->save();

        return $this->sendResponse($tribuna_search->toArray(), 'Tribuna actualizada con éxito');
    }

    /**
     * Elimina un elemento de la tabla tribuna
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $tribuna = Tribuna::find($id); 
            if (is_null($tribuna)) {
                return $this->sendError('Tribuna no encontrada');
            }
            $tribuna->delete();
            return $this->sendResponse($tribuna->toArray(), 'Tribuna eliminada con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'La tribuna no se puedo eliminar, es usada en otra tabla', 'exception' => $e->errorInfo], 400);
        }
        
    }
}
