<?php
require_once './models/Usuario.php';
require_once './utils/AutentificadorJWT.php';

class UsuarioController extends Usuario 
{
    public function Registrar($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $usuario = Usuario::instanciarUsuario($params['mail'], $params['tipo'], $params['clave']);

        $usuario->CrearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function Verificar($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $payload = "";
        $mail = $params['mail'];
        $clave = $params['clave'];

        $usuario = Usuario::ObtenerUsuarioPorMail($mail);

        if(!is_null($usuario) && $usuario != false)
        {
          if(password_verify(trim($clave), $usuario->getClave()))
          {
              $userData = array(
                  'id' => $usuario->getId(),
                  'mail' => $usuario->getMail(),
                  'tipo' => $usuario->getTipo(),
                  'clave' => $usuario->getClave());
              $payload = json_encode(array('Token' => AutentificadorJWT::CrearToken($userData), 'response' => 'OK', 'Tipo_Usuario' => $usuario->getTipo()));
          }else{
            $payload = json_encode(array('Login' => "Clave incorrecta"));
          }
        }else{
          $payload = json_encode(array('Login' => "No existe usuario $mail"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $usuario = Usuario::ObtenerUsuario($params['id']);
        if($usuario != false){
          $payload = json_encode($usuario);
        }else{
          $payload = json_encode(array("Error" => "No existe usuario con ese id"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::ObtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $fueModificado = Usuario::ModificarUsuario($params['mail'], $params['tipo'], $params['clave'], $params['id']);
        if($fueModificado){
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
        }else{
          $payload = json_encode(array("error" => "No se pudo modificar el usuario o no hubo ningun tipo de cambio"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
      $params = $request->getParsedBody();

      $fueBorrado = Usuario::BorrarUsuario($params['id']);
      if($fueBorrado){
        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));
      }else{
        $payload = json_encode(array("error" => "No se pudo borrar el usuario"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
