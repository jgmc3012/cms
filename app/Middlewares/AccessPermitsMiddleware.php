<?php

namespace App\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;

class AccessPermitsMiddleware implements MiddlewareInterface
{

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $page = $request->getUri()->getPath();

// Inicializando la respuesta considerando que el usuario si tiene permisos para tomar la accion que desea

        $response = $handler->handle($request);

// Si el usuario intenta tomar una accion que no tiene permisos para realizar se le devuelve una respuesta vacia

        if (preg_match('/^\/dashboard\/users/', $page)) {
            if ($_SESSION['user']['id_rol']>2) {
                $response = new EmptyResponse(401);
            }
        }

        if (preg_match('/^\/dashboard\/category/', $page)) {
            if ($_SESSION['user']['id_rol']>2) {
                $response = new EmptyResponse(401);
            }
        }

        return $response;
    }
}