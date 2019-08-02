<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Pais;

use Validator;

/**
 * @group Administración de Pais
 *
 * APIs para la gestion de la tabla pais
 */
class PaisController extends BaseController
{
    /**
     * Lista de la tabla pais paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pais = Pais::paginate(15);
        return $this->sendResponse($pais->toArray(), 'Paises devueltos con éxito');
    }



    /**
     * Lista de los paises.
     *
     * @return \Illuminate\Http\Response
     */
    public function pais_all()
    {
        $pais = Pais::get();
        return $this->sendResponse($pais->toArray(), 'Paises devueltos con éxito');
    }



    /**
     * Buscar paises por descripción.
     *@bodyParam nombre string Descripcion del pais.
     *@response{
     *    "nombre" : "Pais",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarPais(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $paises = Pais::where('pais.descripcion','like', '%'.strtolower($input["nombre"]).'%')
                ->get();
            return $this->sendResponse($paises->toArray(), 'Todos los Paises filtrados');
       }else{
            
            $paises = Pais::get();
            return $this->sendResponse($paises->toArray(), 'Todos los Paises devueltos'); 
       }

        
    }


    /**
     * Agrega un nuevo elemento a la tabla pais
     *@bodyParam descripcion string required Descripcion del pais.
     * @response {
     *  "descripcion": "Pais New"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $pais = Pais::create($request->all());        
        return $this->sendResponse($pais->toArray(), 'Pais creado con éxito');
    }


    /**
     * Lista de un pais en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        $pais = Pais::find($id);
        if (is_null($pais)) {
            return $this->sendError('Pais no encontrado');
        }
        return $this->sendResponse($localidad->toArray(), 'Pais devuelto con éxito');
    }


    /**
     * Actualiza un elemento a la tabla pais
     *@bodyParam descripcion string required Descripcion del pais.
     *@response {
     *  "descripcion": "Pais New 1"
     * }
     * [Se filtra por el ID]
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

        $pais_search = Pais::find($id);        
        if (is_null($pais_search)) {
            return $this->sendError('Pais no encontrado');
        }

        $pais_search->descripcion = $input['descripcion'];
        $pais_search->save();

        return $this->sendResponse($pais_search->toArray(), 'Pais actualizado con éxito');

    }


    /**
     * Elimina un elemento de la tabla pais
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {        

        try {
            $pais = Pais::find($id);
            if (is_null($pais)) {
                return $this->sendError('Pais no encontrado');
            }
            $pais->delete();
            return $this->sendResponse($pais->toArray(), 'Pais eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El pais no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
        
    }
}
