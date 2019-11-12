<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Genero 
 *
 * APIs para la gestion de genero
 */
class GeneroController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla genero paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $genero = Genero::paginate(15);
        return $this->sendResponse($genero->toArray(), 'Géneros devueltos con éxito');
    }


    /**
     * Lista de la tabla de todos los generos.
     *
     * @return \Illuminate\Http\Response
     */
    public function generos_all()
    {
        
        $genero = Genero::get();
        return $this->sendResponse($genero->toArray(), 'Géneros devueltos con éxito');
    }


    /**
     * Buscar Generos por descripción.
     *@bodyParam nombre string Nombre del genero.
     *@response{
     *    "nombre" : "Rock",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarGenero(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $generos = \DB::table('genero')
                ->where('genero.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('genero.id','genero.nombre')
                ->get();
            return $this->sendResponse($generos->toArray(), 'Todos los Géneros filtrados');
       }else{
            
            $generos = \DB::table('genero')                
                ->select('genero.id','genero.nombre')
                ->get();
            return $this->sendResponse($generos->toArray(), 'Todos los géneros devueltos'); 
       }

        
    }

   

    /**
     * Agrega un nuevo elemento a la tabla genero
     *@bodyParam nombre string required Nombre del genero.
     *@response{
     *    "nombre" : "Electronica",
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
       $validator = Validator::make($request->all(), [
            'nombre' => 'required'            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
          $genero=Genero::create($request->all());        
         return $this->sendResponse($genero->toArray(), 'Género creado con éxito');
    }

    /**
     * Lista un genero en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Genero  $genero
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $genero = Genero::find($id);


        if (is_null($genero)) {
            return $this->sendError('Género no encontrado');
        }


        return $this->sendResponse($genero->toArray(), 'Género devuelto con éxito');
    }

    

    /**
     * Actualiza un elemeto de la tabla Genero 
     *
     * [Se filtra por el ID]
     *@bodyParam nombre string required Nombre del genero.
     *@response{
     *    "nombre" : "Electronica Sound",
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Genero  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',            
        ]);


        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }

        $genero = Genero::find($id);        
        if (is_null($genero)) {
            return $this->sendError('Genero no encontrada');
        }

        $genero->nombre = $input['nombre'];
         $genero->save();

        return $this->sendResponse($genero->toArray(), 'Género actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla Genero
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Genero  $genero
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        try {
            $genero = Genero::find($id);
            if (is_null($genero)) {
                return $this->sendError('Género no encontrado');
            }
            $genero->delete();

            return $this->sendResponse($genero->toArray(), 'Genero eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El genero no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
