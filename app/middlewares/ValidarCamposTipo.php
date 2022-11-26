<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ValidarCamposTipo
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $reponse = new Response();
        $parametros = $request->getParsedBody();

        if(isset($parametros["tipo"]))
        {
            if($parametros["tipo"] != "")
            { 
                $reponse = $handler->handle($request);
            }else{
                $reponse->getBody()->write("Error, campo tipo de usuario vacio");
            }
        }else{
            $reponse->getBody()->write("Error, Falta completar el tipo de usuario");
        }

        return $reponse;
    }
}