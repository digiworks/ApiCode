<?php
namespace code\applications\response;

use Crell\ApiProblem\ApiProblem;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;




class UnauthorizedResponse extends Response
{
    public function __construct($message, $status = 401)
    {
        $problem = new ApiProblem($message, "about:blank");
        $problem->setStatus($status);

        $handle = fopen("php://temp", "wb+");
        $body = new Stream($handle);
        $body->write($problem->asJson(true));
        $headers = new Headers;
        $headers->addHeader("Content-type", "application/problem+json");
        parent::__construct($status, $headers, $body);
    }
}
