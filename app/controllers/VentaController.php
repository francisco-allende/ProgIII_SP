<?php
require_once './models/Venta.php';
require_once './utils/AutentificadorJWT.php';
require_once './controllers/ArchivoController.php';

class VentaController extends Venta 
{
  public static function Alta($request, $response, $args)
  {
      $params = $request->getParsedBody();
      
      $fecha = $params['fecha'];
      $cantidad = $params['cantidad'];
      $nombre_cripto = $params['nombre_cripto'];
      $usuario_comprador = $params['usuario_comprador'];
      
      $imgPath = "{$nombre_cripto}_{$usuario_comprador}_{$fecha}.jpg";
      
      $venta = Venta::instanciarVenta($fecha, $cantidad, $nombre_cripto, $usuario_comprador, $imgPath);
      $venta->CrearVenta();
      
      $retorno = ArchivoController::UploadPhoto($imgPath);
      if($retorno == 1 || $retorno){
        $payload = json_encode(array("mensaje" => "Venta realizada y foto guardada con exito"));
      }else{
        $payload = json_encode(array("mensaje" => "Venta realizada con exito. No se pudo guardar la foto"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
      $lista = Venta::ObtenerTodos();
      $payload = json_encode(array("listaVentas" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPorPaisYFecha($request, $response, $args)
  {
      $nacionalidad = $args['nacionalidad'];
      $fecha_inicio = $args['fecha_inicio'];
      $fecha_fin = $args['fecha_fin'];

      $venta = Venta::ObtenerVentaPorPaisYFecha($nacionalidad, $fecha_inicio, $fecha_fin);
      if($venta != false){
        $payload = json_encode($venta);
      }else{
        $payload = json_encode(array("Error" => "No existe venta de ese pais en esa fecha"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPorNombreCripto($request, $response, $args)
  {
      $nombre_cripto = $args['nombre_cripto'];

      $venta = Venta::ObtenerVentaPorNombreCripto($nombre_cripto);
      if($venta != false){
        $payload = json_encode($venta);
      }else{
        $payload = json_encode(array("Error" => "No existe venta de cripto con ese nombre"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }
}
?>