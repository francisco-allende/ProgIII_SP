<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class isAdmin
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();

        $header = $request->getHeaderLine('Authorization');
        $response = new Response();
        try {
            if (!empty($header)) {
                $token = trim(explode("Bearer", $header)[1]);
                $data = AutentificadorJWT::ObtenerData($token);
                if ($data->tipo == "admin"){
                    $response = $handler->handle($request);
                }else{
                    $response->getBody()->write("Error, no tiene el permiso de administrador");
                }
            }else{
                $response->getBody()->write("Error, Token vacio");
            }
        }catch (\Throwable $th) {
            echo $th->getMessage();
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}