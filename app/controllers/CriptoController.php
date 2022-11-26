<?php
require_once './models/Cripto.php';
require_once './utils/AutentificadorJWT.php';
require_once './controllers/ArchivoController.php';

use Fpdf\Fpdf;

class CriptoController extends Cripto 
{
  public function Alta($request, $response, $args)
  {
      $params = $request->getParsedBody();
      $imgPath = "{$params['nombre']}.jpg";
      
      $cripto = Cripto::instanciarCripto($params['precio'], $params['nombre'], $imgPath, $params['nacionalidad']);
      $cripto->CrearCripto();
      
      $retorno = ArchivoController::UploadPhoto($imgPath);
      if($retorno == 1 || $retorno){
        $payload = json_encode(array("mensaje" => "Cripto y foto creada con exito"));
      }else{
        $payload = json_encode(array("mensaje" => "Cripto creada con exito. No se pudo guardar la foto"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerTodos($request, $response, $args)
  {
    $lista = Cripto::ObtenerTodos();
    $payload = json_encode(array("lista" => $lista));

    $response->getBody()->write($payload);
      return $response
          ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPorId($request, $response, $args)
  {
      $id = $args['id'];

      $cripto = Cripto::ObtenerCriptoPorId($id);
      if($cripto != false){
        $payload = json_encode($cripto);
      }else{
        $payload = json_encode(array("Error" => "No existe cripto con ese id"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function TraerPorPais($request, $response, $args)
  {
      $nacionalidad = $args['nacionalidad'];

      $cripto = Cripto::ObtenerCriptoPorPais($nacionalidad);
      if($cripto != false){
        $payload = json_encode($cripto);
      }else{
        $payload = json_encode(array("Error" => "No existe cripto de ese pais"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function Modificar($request, $response, $args)
  {
      $params = $request->getParsedBody();
      $id = $params['id'];
      $precio = $params['precio'];
      $nombre = $params['nombre'];
      $nacionalidad = $params['nacionalidad'];
      $imgPath = "{$nombre}.jpg";

      $fueModificado = Cripto::ModificarCripto($id, $precio, $nombre, $imgPath, $nacionalidad);
      if(true){
			if(file_exists('./FotosCripto/'.$imgPath)) //file exist es para directoris y archivos. Aqui, ruta+archivo
			{
				ArchivoController::MoverPhoto($imgPath);
			}else{
				ArchivoController::UploadPhoto($imgPath);
			}
          	$payload = json_encode(array("mensaje" => "Cripto modificado con exito"));
      }else{
        $payload = json_encode(array("error" => "No se pudo modificar la cripto o no hubo ningun tipo de cambio"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
  }

  public function Borrar($request, $response, $args)
    {
      $params = $request->getParsedBody();

      $fueBorrado = Cripto::BorrarCripto($params['id']);
      if($fueBorrado){
        $payload = json_encode(array("mensaje" => "Cripto borrado con exito"));
      }else{
        $payload = json_encode(array("error" => "No se pudo borrar la cripto"));
      }

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

  public function TraerPDF($request, $response, $args)
  {        
			$criptos = Cripto::ObtenerTodos();
			if ($criptos) {
					$pdf = new FPDF();
					$pdf->AddPage();

					//tipo de letra del pdf 'arial black'
					$pdf->SetFont('Arial', 'B', 25);

					// titulo principal <h1>
					$pdf->Cell(160, 15, 'Segundo Parcial Programacion III', 1, 3, 'L');
					$pdf->Ln(3);

					$pdf->SetFont('Arial', '', 15);

					// titulo secundario <h2>
					$pdf->Cell(60, 4, 'Alumno: Francisco Allende', 0, 1, 'L');
					$pdf->Cell(20, 0, '', 'T');
					$pdf->Ln(3);
					
					// titulo de la tabla
					$pdf->Cell(15, 0, 'Tabla Criptos', 'L');
					$pdf->Ln(5);

					// Columnas de la clase venta 
					$header = array('Id', 'Precio', 'Nombre', 'Foto', 'Nacionalidad');
					
					// colores RGB del fondo de las filas de la tabla del pdf
					$pdf->SetFillColor(180, 36, 227);
					$pdf->SetTextColor(0, 0, 0);
					$pdf->SetDrawColor(50, 0, 0);
					$pdf->SetLineWidth(.3);
					$pdf->SetFont('Arial', 'B', 8);
					$w = array(20, 30, 30, 30, 40, 30);
					for ($i = 0; $i < count($header); $i++) {
							$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
					}
					$pdf->Ln();

					// Colores de los bordes de las filas de la tabla del pdf en rgb
					$pdf->SetFillColor(200, 200, 220);
					$pdf->SetTextColor(0);
					$pdf->SetFont('');
					// Data
					$fill = false;

					// Header
          // cada una de las columnas de la clase cripto para la tabla
					foreach ($criptos as $cripto) { 
							$pdf->Cell($w[0], 5, $cripto->getId(), 'LR', 0, 'C', $fill);
							$pdf->Cell($w[1], 5, $cripto->getPrecio(), 'LR', 0, 'C', $fill);
							$pdf->Cell($w[2], 5, $cripto->getNombre(), 'LR', 0, 'C', $fill);
							$pdf->Cell($w[3], 5, $cripto->getFoto(), 'LR', 0, 'C', $fill);
							$pdf->Cell($w[4], 5, $cripto->getNacionalidad(), 'LR', 0, 'C', $fill);
							
							$pdf->Ln();
							$fill = !$fill;
					}
					$pdf->Cell(array_sum($w), 0, '', 'T');

					// ruta del pdf, si la carpeta no existe la creo
          if(!file_exists('./PDF/'))
          {
              mkdir('./PDF/', 0777);
          }
					$pdf->Output('F', './PDF/' . 'criptos' .'.pdf', 'I');

					$payload = json_encode(array("Mensaje" => 'PDF Creado con exito en: ./PDF/'));
			} else {
					$payload = json_encode(array("Error" => 'No se pudo generar el PDF'));
			}
			$response->getBody()->write($payload);
			return $response
					->withHeader('Content-Type', 'application/json');
	
  }


}
