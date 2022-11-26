<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './utils/AutentificadorJWT.php';

//Importo Middlewares
require_once './middlewares/ValidarCamposMailClave.php';
require_once './middlewares/ValidarCamposTipo.php';
require_once './middlewares/isAdmin.php';
require_once './middlewares/EstaLogeado.php';

//Importo controllers
require_once './controllers/UsuarioController.php';
require_once './controllers/CriptoController.php';
require_once './controllers/VentaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path
$app->setBasePath('/Segundo_Parcial/parcial/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();


// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');  
    $group->post('/registrar', \UsuarioController::class . ':Registrar')->add(new ValidarCamposMailClave())->add(new ValidarCamposTipo());
    $group->post('/login', \UsuarioController::class . ':Verificar')->add(new ValidarCamposMailClave());
  });

  $app->group('/cripto', function (RouteCollectorProxy $group) {
    $group->get('[/]', \CriptoController::class . ':TraerTodos');
    $group->get('/search_by_country/{nacionalidad}', \CriptoController::class . ':TraerPorPais');
    $group->get('/search_by_id/{id}', \CriptoController::class . ':TraerPorId')->add(new EstaLogeado());  
    $group->post('/alta', \CriptoController::class . ':Alta')->add(new isAdmin());
    $group->post('/modificar', \CriptoController::class . ':Modificar')->add(new isAdmin());
    $group->delete('/borrar', \CriptoController::class . ':Borrar')->add(new isAdmin());
    $group->get('/pdf/', \CriptoController::class . ':TraerPDF')->add(new EstaLogeado());
  });

$app->group('/ventas', function (RouteCollectorProxy $group) {
  $group->get('[/]', \VentaController::class . ':TraerTodos');  
  $group->get('/{nacionalidad}/{fecha_inicio}/{fecha_fin}', \VentaController::class . ':TraerPorPaisYFecha');  
  $group->get('/{nombre_cripto}', \VentaController::class . ':TraerPorNombreCripto');
  $group->post('/alta', \VentaController::class . ':Alta');
  })->add(new EstaLogeado());

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP Francisco Allende");
    return $response;

});

$app->run();


