<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GrupsVendedore;
use Validator;
/**
 * @group Administración de Grupos Vendedores
 *
 * APIs para la gestion de la tabla grups_vendedores
 */
class GrupsVendedoreController extends BaseController
{
    /**
     * Listado de los grupos de vendedores paginados.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $grups_vendedores = GrupsVendedore::paginate(15);
        return $this->sendResponse($grups_vendedores->toArray(), 'Grupos de vendedores devueltos con éxito');
    }


    /**
     * Listado de todos los grupos de vendedores.
     *
     * @return \Illuminate\Http\Response
     */
    public function groups_vendedores_all()
    {
        
        $grups_vendedores = GrupsVendedore::get();
        return $this->sendResponse($grups_vendedores->toArray(), 'Grupos de vendedores devueltos con éxito');
    }


     /**
     * Buscar Grupo de Vendedores por nombre.
     *@bodyParam nombre string Nombre del grupo de vendedor.
     *@response{
     *    "nombre" : "Grupo Vendedor",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarGrupoVendedores(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $grups_vendedores = \DB::table('grups_vendedores')
                ->where('grups_vendedores.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('grups_vendedores.*')
                ->get();
            return $this->sendResponse($grups_vendedores->toArray(), 'Todos los Grupos de Vendedores filtrados');
       }else{
            
            $grups_vendedores = \DB::table('grups_vendedores')                
                ->select('grups_vendedores.*')
                ->get();
            return $this->sendResponse($grups_vendedores->toArray(), 'Todos los Grupo de Vendedores devueltos'); 
       }

        
    }


    /**
     * Agrega un nuevo elemento a la tabla grups_vendedores 
     *@bodyParam nombre string required Nombre del grupo de vendedores.
     *@bodyParam caracteristica string required Característica del grupo de vendedores. 
     * @response {
     *  "nombre": "Grupo 1",
     *  "caracteristica": "Grupo vendedor 1",      
     * }   
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
       $validator = Validator::make($request->all(), [
            'nombre' => 'required',
            'caracteristica' => 'required',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
         $grup_vendedor = GrupsVendedore::create($request->all());        
         return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores creado con éxito');
    }

    /**
     * Lista un grupo de vendedor en especifico
     * [Se filtra por el ID]
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $grup_vendedor = GrupsVendedore::find($id);
        if (is_null($grup_vendedor)) {
            return $this->sendError('Grupo de vendedores no encontrado');
        }
        return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores devuelto con éxito');
    }

  
    /**
     * Actualiza un elemeto de la tabla grups_vendedores 
     *@bodyParam nombre string required Nombre del grupo de vendedores.
     *@bodyParam caracteristica string required Característica del grupo de vendedores.
     * [Se filtra por el ID]
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $input = $request->all();
        $validator = Validator::make($input, [
            'nombre' => 'required',
            'caracteristica' => 'required',             
        ]);
        
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $grup_vendedor = GrupsVendedore::find($id);
        if (is_null($grup_vendedor)) {
            return $this->sendError('Grupo de vendedores no encontrado');
        }

        $grup_vendedor->nombre = $input['nombre'];
        $grup_vendedor->caracteristica = $input['caracteristica'];
        $grup_vendedor->save();

        return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla grups_vendedores
     *
     * [Se filtra por el ID]
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try { 

            $grup_vendedor = GrupsVendedore::find($id);
            if (is_null($grup_vendedor)) {
                return $this->sendError('Grupo de vendedores no encontrado');
            }
            $grup_vendedor->delete();
            return $this->sendResponse($grup_vendedor->toArray(), 'Grupo de vendedores eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El Grupo de vendedores no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
