<?php

namespace App\Http\Controllers;

use App\Models\TipoCupon;
use Illuminate\Http\Request;
use Validator;

class TipoCuponController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $tipoCupon = TipoCupon::paginate(15);

        return $this->sendResponse($tipoCupon->toArray(), 'Tipos de cupones devueltos con éxito');
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
          $tipoCupon=TipoCupon::create($request->all());        
         return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón  creado con éxito');
    }

    /**
     * Display the specified resource.
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function update($id,Request $request, TipoCupon $tipoCupon)
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TipoCupon  $tipoCupon
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $tipoCupon = TipoCupon::find($id);
        if (is_null($tipoCupon)) {
            return $this->sendError('Tipo de cupón no encontrado');
        }
        $tipoCupon->delete();


        return $this->sendResponse($tipoCupon->toArray(), 'Tipo de Cupón eliminado con éxito');
    }
}
