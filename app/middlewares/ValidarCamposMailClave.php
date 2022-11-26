<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ValidarCamposMailClave
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $reponse = new Response();
        $parametros = $request->getParsedBody();

        if(isset($parametros["mail"]) && isset($parametros["clave"]))
        {
            if($parametros["mail"] != "" && $parametros["clave"] != "")
            { 
                $reponse = $handler->handle($request);
            }else{
                $reponse->getBody()->write("Error, campos vacios");
            }
        }else{
            $reponse->getBody()->write("Error, Faltan completar los campos");
        }

        return $reponse;
    }
}