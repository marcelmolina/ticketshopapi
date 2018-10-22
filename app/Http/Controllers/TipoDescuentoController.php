<?php

namespace App\Http\Controllers;

use App\Models\TipoDescuento;
use Illuminate\Http\Request;
use Validator;

class TipoDescuentoController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
         $tipoDescuento = TipoDescuento::paginate(15);

        return $this->sendResponse($tipoDescuento->toArray(), 'Tipos de descuentos devueltos con éxito');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   

    /**
     * Store a newly created resource in storage.
     *
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
          $tipoDescuento=TipoDescuento::create($request->all());        
         return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento creado con éxito');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
         $tipoDescuento = TipoDescuento::find($id);


        if (is_null($tipoDescuento)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }


        return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento devuelto con éxito');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, TipoDescuento $tipoDescuento)
    {
        
          $input = $request->all();


        $validator = Validator::make($input, [
            'nombre' => 'required',           
                   
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        

     $tipoDescuento = TipoDescuento::find($id);

 if (is_null($tipoDescuento)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }

        $tipoDescuento->nombre = $input['nombre'];              
         $tipoDescuento->save();
         
        return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento actualizado con éxito');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoDescuento  $tipoDescuento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tipoDescuento= TipoDescuento::find($id);
        if (is_null($tipoDescuento)) {
            return $this->sendError('Tipo de descuento no encontrado');
        }
        $tipoDescuento->delete();


        return $this->sendResponse($tipoDescuento->toArray(), 'Tipo de descuento eliminado con éxito');
    }
}
