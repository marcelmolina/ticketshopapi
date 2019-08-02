<?php

namespace App\Http\Controllers;

use App\Models\TipoCupon;
use Illuminate\Http\Request;
use Validator;
/**
 * @group Administración de Tipo de Cupon
 *
 * APIs para la gestion de la tabla tipo_cupon
 */
class TipoCuponController extends BaseController
{
    /**
     * Lista de la tabla tipo cupon paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipoCupon = TipoCupon::paginate(15);
        return $this->sendResponse($tipoCupon->toArray(), 'Tipos de cupones devueltos con éxito');
    }


    /**
     * Lista de los tipos de cupon.
     *
     * @return \Illuminate\Http\Response
     */
    public function tipo_cupon_all()
    {
        $tipoCupon = TipoCupon::get();
        return $this->sendResponse($tipoCupon->toArray(), 'Tipos de cupones devueltos con éxito');
    }


    /**
     * Buscar Tipo de cupon por descripción.
     *@bodyParam nombre string Nombre del Tipo de descuento.
     *@response{
     *    "nombre" : "Tipo cupon 1",
     * }
     * @return \Illuminate\Http\Response
     */
    public function buscarTipoCupon(Request $request)
    {
       
       $input = $request->all();
       
       if(isset($input["nombre"]) && $input["nombre"] != null){
            
            $input = $request->all();
            $tipoCupon = \DB::table('tipo_cupon')
                ->where('tipo_cupon.nombre','like', '%'.strtolower($input["nombre"]).'%')
                ->select('tipo_cupon.id','tipo_cupon.nombre')
                ->get();
            return $this->sendResponse($tipoCupon->toArray(), 'Todos los tipos de cupon filtrados');
       }else{
            
            $tipoCupon = \DB::table('tipo_cupon')                
                ->select('tipo_cupon.id','tipo_cupon.nombre')
                ->get();
            return $this->sendResponse($tipoCupon->toArray(), 'Todos los tipos de cupon devueltos'); 
       }
        
    }

   
    /**
     * Agrega un nuevo elemento a la tabla tipo_cupon
     *@bodyParam nombre string required Nombre del tipo de cupón.
     * @response {      
     *  "nombre": "Tipo 1"            
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
          $tipoCupon=TipoCupon::create($request->all());        
         return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón  creado con éxito');
    }

     /**
     * Lista de un tipo de cupon en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoCupon = TipoCupon::find($id);


        if (is_null($tipoCupon)) {
            return $this->sendError('Tipo de Cupón no encontrado');
        }


        return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón  devuelto con éxito');
    }

    
    /**
     * Actualiza un elemeto de la tabla tipo_cupon 
     *
     * [Se filtra por el ID]
     *@bodyParam nombre string required Nombre del tipo de cupón.
     * @response {
     *  "nombre": "Tipo Cupon 1"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoCupon  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',           
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

     $tipoCupon = TipoCupon::find($id);

    if (is_null($tipoCupon)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }

        $tipoCupon->nombre = $input['nombre'];              
         $tipoCupon->save();
         
        
        return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón actualizado con éxito');
    }

    /**
     * Elimina un elemento de la tabla tipo_cupon
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      
        try { 

            $tipoCupon = TipoCupon::find($id);
            if (is_null($tipoCupon)) {
                return $this->sendError('Tipo de cupón no encontrado');
            }
            $tipoCupon->delete();
            return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'Tipo de Cupón  no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
    }
}
