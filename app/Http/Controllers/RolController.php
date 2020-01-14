<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Validator;


/**
 * @group Administración de Roles
 *
 * APIs para la gestion de la tabla rol
 */
class RolController extends BaseController
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store', 'update', 'destroy']]);        
    }

    /**
     * Lista de la tabla rol paginada.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rol = Rol::paginate(15);
        return $this->sendResponse($rol->toArray(), 'Roles devueltos con éxito');
    }


    /**
     * Lista de los roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function roles_all()
    {
        $roles = Rol::get();
        return $this->sendResponse($roles->toArray(), 'Roles devueltos con éxito');
    }


    /**
     * Lista de los usuarios por rol.
     *
     * @return \Illuminate\Http\Response
     */
    public function rol_usuarios($id_rol)
    {
        $roles = Rol::with('usuarios')->find($id_rol);
        if (is_null($roles)) {
            return $this->sendError('Rol no encontrado');
        }
        return $this->sendResponse($roles->toArray(), 'Usuarios por rol devueltos con éxito');
    }

   
    /**
     * Agrega un nuevo elemento a la tabla rol
     *@bodyParam nombre string required Nombre del rol.
     * @response {
     *  "nombre": "Rol New"
     * }
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         //
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }
        
        $rol = Rol::create($request->all());        
        return $this->sendResponse($rol->toArray(), 'Rol creado con éxito');
    }

    /**
     * Lista de un rol en especifico 
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rol = Rol::find($id);
        if (is_null($rol)) {
            return $this->sendError('Rol no encontrado');
        }
        return $this->sendResponse($rol->toArray(), 'Rol devuelto con éxito');
    }

    
    /**
     * Actualiza un elemento a la tabla rol
     *@bodyParam nombre string required Nombre del rol.
     *@response {
     *  "nombre": "Rol New 1"
     * }
     * [Se filtra por el ID]
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',            
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }        

        $rol_search = Rol::find($id);        
        if (is_null($rol_search)) {
            return $this->sendError('Rol no encontrado');
        }

        $rol_search->nombre = $input['nombre'];
        $rol_search->save();

        return $this->sendResponse($rol_search->toArray(), 'Rol actualizado con éxito');

    }

    /**
     * Elimina un elemento de la tabla rol
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Rol  $rol
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $rol = Rol::find($id);
            if (is_null($rol)) {
                return $this->sendError('Rol no encontrado');
            }
            $rol->delete();
            return $this->sendResponse($rol->toArray(), 'Rol eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El rol no se puedo eliminar, esta asignado a un usuario', 'exception' => $e->errorInfo], 400);
        }
    }
}

