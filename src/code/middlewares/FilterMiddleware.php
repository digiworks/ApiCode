<?php

namespace code\middlewares;

use code\utility\Escaper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FilterMiddleware implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $escaper = new Escaper();

        $contentType = $request->getHeaderLine('Content-Type');
        
        if (strstr($contentType, 'multipart/form-data')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (!is_null($contents)) {
                $post_values = $request->getBody();
                foreach ($post_values as $key => $value) {
                    if (is_string($value)) {
                        $post_values[$key] = $escaper->filterArray($value);
                    }
                }
                $request->withParsedBody($post_values);
            }
        }
        return $handler->handle($request);
    }

}
