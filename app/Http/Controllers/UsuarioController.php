<?php

namespace App\Http\Controllers;


use Validator;
use DateTime;
use Socialite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;  
use App\Models\Usuario;
use App\Models\Rol;
use App\Notifications\SignupActivate;
use Laravel\Passport\TokenRepository;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

/**
 * @group Administración de Usuario
 *
 * APIs para la gestion de la tabla usuario
 */
class UsuarioController extends BaseController 
{

    public $successStatus = 200;
    protected $server;
    protected $tokens;

    public function __construct(ResourceServer $server, TokenRepository $tokens) {
        $this->server = $server;
        $this->tokens = $tokens;
    }
    /**
     * Actualiza el perfil del usuario.
     * [Se filtra por el ID]
     *@authenticated
     *@bodyParam nombre string required Nombre del usuario.
     *@bodyParam identificacion string Identificacion del usuario.
     *@bodyParam tipo_identificacion boolean Tipo de identificacion del usuario.
     *@bodyParam direccion string Direccion del usuario.
     *@bodyParam ciudad string Ciudad del usuario.
     *@bodyParam departamento string Departamento del usuario.
     *@bodyParam telefono string Telefono del usuario.
     *
     * @response {      
     *  "nombre": "Gold User", 
     *  "identificacion" : "Manager",
     *  "tipo_identificacion" : 1,
     *  "direccion" : "Address 11",
     *  "ciudad" : "Ciudad",
     *  "departamento" : "Departamento",
     *  "telefono" : "311565634" 
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Usuario  $id
     * @return \Illuminate\Http\Response
     **/
    public function updateprofile($id, Request $request)
    {
        
        $input = $request->all();

        $validator = Validator::make($input, [ 
            'nombre' => 'required|string',
            'identificacion' => 'string',
            'tipo_identificacion' => 'boolean',
            'direccion' => 'string',
            'ciudad' => 'string',
            'departamento' => 'string',
            'telefono' => 'string',
        ]);

        if($validator->fails()){
            return $this->sendError('Error de validación', $validator->errors());       
        }
        
        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }

        $users->nombre = $input['nombre'];
        $users->identificacion = $input['identificacion'];
        $users->tipo_identificacion = $input['tipo_identificacion'];    
        $users->direccion = $input['direccion'];
        $users->ciudad = $input['ciudad'];
        $users->departamento = $input['departamento'];
        $users->telefono = $input['telefono'];             
        $users->save();
        

        return $this->sendResponse($users->toArray(), 'Usuario actualizado con éxito');

    }


    /**
     * Cambio de clave del usuario.
     * [El usuario debe estar logeado (Headers => Authorization => Bearer Token)]
     *@authenticated
     *@bodyParam mypassword string Contraseña actual del usuario.
     *@bodyParam password string Nueva contraseña del usuario.
     *@bodyParam c_password string Confirmación de la nueva contraseña del usuario.
     *
     * @response {      
     *  "mypassword": "Temporada Gold", 
     *  "password": "1234567890",
     *  "c_password" : "1234567890"     
     * }
     *
     * @param  \Illuminate\Http\Request  $request
     **/
    public function cambioclave(Request $request)
    {
        $validator  = Validator::make($request->all(), [
             'mypassword' => 'required|min:3',
             'password' => 'required|min:3',
             'c_password' => 'required|min:3|same:password',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $datos = $request->all();
        if(Hash::check($datos['mypassword'], Auth::user()->password)){
            $passhash = bcrypt($datos['password']); 
            \DB::table('usuario')
                 ->where('email', '=', Auth::user()->email)
                 ->update(['password' => $passhash]);
            $user = Usuario::find(Auth::user()->email);
            return $this->sendResponse($user->toArray(), 'Clave del usuario actualizada con éxito');

        }else{
            return $this->sendError('La contraseña actual no coincide.');
        }
    }

     /** 
     * Inicio de sesion del usuario 
     *
     *@bodyParam email string Email del usuario.
     *@bodyParam password string Contraseña del usuario.
     *
     *@response {      
     *  "email": "email@example.com", 
     *  "password": "1234567890"    
     * }
     * @return \Illuminate\Http\Response 
     */ 
    public function login(Request $request){ 

        
        $validator  = Validator::make($request->all(), [
             'email' => 'email|required|string',
             'password' => 'required|min:3|string'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $datos = $request->all();

        $userdata = [
            'email'   => $datos['email'],
            'password' => $datos['password'],
            'active' => 1,
            'deleted_at' => null,
        ]; 
        
        if(Auth::attempt($userdata)){ 
            $user = Auth::user(); 
            
            $tokenResult =  $user->createToken('MyApi');
            $token = $tokenResult->token;
            $token->expires_at = Carbon::now()->addDays(1);
            $token->save();
            
            $success['usuario'] =  $user;
            $success['token'] =  $tokenResult->accessToken; 
            $success['token_type'] = 'Bearer';
            $success['token_expire'] = Carbon::now()->addDays(1)->format('Y-m-d');
            
            return response()->json(['success' => $success], $this->successStatus); 
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    
    /**
     * Registrar usuario.
     *
     *@bodyParam nombre string required Nombre del usuario.
     *@bodyParam email string required Email del usuario.
     *@bodyParam password string Nueva contraseña del usuario.
     *@bodyParam c_password string Confirmación de la nueva contraseña del usuario.
     *@bodyParam identificacion string Identificacion del usuario.
     *@bodyParam tipo_identificacion boolean Tipo de identificacion del usuario.
     *@bodyParam direccion string Direccion del usuario.
     *@bodyParam ciudad string Ciudad del usuario.
     *@bodyParam departamento string Departamento del usuario.
     *@bodyParam telefono string Telefono del usuario.
     *@bodyParam id_rol int Id del rol del usuario.
     *
     *@response{      
     *  "nombre": "Gold Gold",
     *  "email" : "mail@example.com" 
     *  "password": "1234567890",
     *  "c_password" : "1234567890"
     *  "identificacion" : "Manager",
     *  "tipo_identificacion" : 1,
     *  "direccion" : "Address 11",
     *  "ciudad" : "Ciudad",
     *  "departamento" : "Departamento",
     *  "telefono" : "311565634",
     *  "id_rol" : 1 
     *  }
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response 
     */ 
    public function register(Request $request) 
    { 
       $validator = Validator::make($request->all(), [ 
            'nombre' => 'required|string', 
            'email' => 'required|string|email', 
            'password' => 'string|min:3',
            'c_password' => 'string|min:3|same:password', 
            'identificacion' => 'string',
            'tipo_identificacion' => 'boolean',
            'direccion' => 'string',
            'ciudad' => 'string',
            'departamento' => 'string',
            'telefono' => 'string',
            'id_rol' => 'integer', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 404);            
        }

        $rol = Rol::find($request->input('id_rol'));
        if (is_null($rol)) {            
            return response()->json(['error'=>'El Rol indicado no existe'], 404);
        }

        $user_search = Usuario::find($request->input('email'));
        if (!is_null($user_search)) {            
            return response()->json(['error'=>'El Usuario ya se encuentra registrado'], 404);
        }


        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $input['activation_token'] = str_random(60);
        $user = Usuario::create($input); 
        $user->notify(new SignupActivate($user));
        $success['token'] =  $user->createToken('MyApi')->accessToken; 
        $success['nombre'] =  $user->nombre;
        $success['token_type'] = 'Bearer';
        return response()->json(['success'=>$success], $this->successStatus); 
    }


    /**
     * Validar Token 
     * [Activo o inactivo durante la sesión del usuario] 
     *@authenticated
     * headers = {
            "Accept": "application/json",
            "Content-Type": "application/x-www-form-urlencoded",
            "Authorization": "Bearer $AccessToken"
        }

     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function validateToken(Request $request) {
        
        $psr = (new DiactorosFactory)->createRequest($request);

        try {
            $psr = $this->server->validateAuthenticatedRequest($psr);
            
            $token = $this->tokens->find(
                $psr->getAttribute('oauth_access_token_id')
            );

            $currentDate = new DateTime();
            $tokenExpireDate = new DateTime($token->expires_at);

            $isAuthenticated = $tokenExpireDate > $currentDate ? true : false;
                           
            return response()->json(array('authenticated' => $isAuthenticated), 200);
            
        } catch (OAuthServerException $e) {
            return $this->sendError('Algo salio mal con la autenticacion. Por favor, cierre la sesion y vuelva a iniciar sesion.'); 
            
        }
    }


    /**
     * Redirigir al usuario a la página de autenticación de Google
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * 
     * Obtener la información de usuario de Google.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return $this->sendError('Ocurrio un error al validar el provider');
        }        
        
        $authUser = Usuario::find($user->email); 
        if($authUser){

            $userdata = [
                'email'   => $authUser['email'],
                'password' => $authUser['password'],
                'active' => 1,
                'deleted_at' => null,
            ]; 

            if(Auth::attempt($userdata)){ 
                $usuario = Auth::user(); 
                $success['token'] =  $usuario->createToken('MyApi')->accessToken; 
                $success['token_type'] = 'Bearer';
                return response()->json(['success' => $success], $this->successStatus); 
            }else{ 
                return response()->json(['error'=>'Unauthorised'], 401); 
            } 

        }else{

            $usuario = Usuario::create([
                'nombre' => $user->name, 
                'email' => $user->email, 
                'active' => 1,
                'provider' => strtoupper($provider),
                'provider_id' => $user->id, 
            ]); 

            $success['token'] =  $usuario->createToken('MyApi')->accessToken; 
            $success['nombre'] =  $usuario->nombre;
            $success['token_type'] = 'Bearer';
            return response()->json(['success'=>$success], $this->successStatus);
        }

        


    }


    /**
     * 
     * Activar la cuenta del usuario
     * [Se requiere Token enviado al correo]
     * @return Response
     */
    public function signupActivate($token)
    {
        $user = Usuario::where('activation_token', $token)->first();
        if (!$user) {
            return $this->sendError('Este token de activación no es válido.');            
        }
        $user->active = true;
        $user->activation_token = '';
        $user->save();
        return $this->sendResponse($user->toArray(), 'Cuenta activada con éxito');
    }
    
    /** 
     * Detalles del usuario logeado 
     * [El usuario debe estar logeado (Headers => Authorization => Bearer Token)]
     *@authenticated
     * @return \Illuminate\Http\Response 
     */ 
    public function detailsuser() 
    { 
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus); 
    }


    /** 
     * Lista de los usuarios     
     * @return \Illuminate\Http\Response 
     */ 
    public function listausuarios(){
        $user = Usuario::with('rol')->paginate(15);
        $lista_usuarios = compact('user'); 

        return $this->sendResponse($lista_usuarios, 'Listado de usuarios devueltos con éxito');

    }


    /** 
     * Compras realizadas por usuario 
     * [Se filtra por el ID del usuario]
     */ 
    public function comprasrealizadas($id){

        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }


        $compras = Usuario::with('devolucions')
                        ->with('boleta_reservas')
                        ->with('palco_reservas')
                        ->where('usuario.email',$id)->get();                
        $compras_p = compact('compras'); 

        return $this->sendResponse($compras_p, 'Compras realizadas devueltas con éxito');

    }


     /** 
     * Temporadas compradas por usuario 
     * [Se filtra por el ID del usuario]
     * @return \Illuminate\Http\Response 
     */ 
    public function temporadascompradas($id){

        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }

        $temporadas = Usuario::with('venta_temporadas')
                        ->where('usuario.email',$id)->get();
        $temporadas_p = compact('temporadas'); 

        return $this->sendResponse($temporadas_p, 'Temporadas compradas devueltas con éxito');

    }


     /** 
     * Reservas realizadas por usuario 
     * [Se filtra por el ID del usuario] 
     * @return \Illuminate\Http\Response 
     */ 
    public function reservas($id){

        $users = Usuario::find($id);
        if (is_null($users)) {
            return $this->sendError('Usuario no encontrado');
        }

        $reservas = Usuario::with('vents')
                    ->with('devolucions')
                    ->where([ ['usuario.email','=',$id] ])
                    ->get();

        $reservas_p = compact('reservas'); 

        return $this->sendResponse($reservas_p, 'Reserevas realizadas devueltas con éxito');

    }


    /** 
     * Cierre de sesion del usuario 
     * [Logout all devices]
     * [El usuario debe estar logeado (Headers => Authorization => Bearer Token)]
     *@authenticated
     * @return \Illuminate\Http\Response 
     */  
    public function logout()
    {       
       $accessToken = Auth::user()->token();
        
       \DB::table('oauth_access_tokens')
            ->where('user_id', $accessToken->user_id)
            ->update([
                'revoked' => true
            ]);
        $accessToken->revoke();
        return response()->json(['data' => 'Usuario ha cerrado sesión en todos los dispositivos'], 200);       
    }

    /**
     * Elimina un elemento de la tabla usuario
     *
     * [Se filtra por el ID]
     *
     * @param  \App\Models\Usuario  $usuario
     * @return \Illuminate\Http\Response
     */
    public function destroy($email)
    {
        try {
            
            $usuario = Usuario::find($email);
            if (is_null($usuario)) {
                return $this->sendError('Usuario no encontrado');
            }            
            $usuario->delete();
            return $this->sendResponse($usuario->toArray(), 'Usuario eliminado con éxito');

        }catch (\Illuminate\Database\QueryException $e){
            return response()->json(['error' => 'El registro del usuario no se puedo eliminar, es usado en otra tabla', 'exception' => $e->errorInfo], 400);
        }
        
        
    } 

}