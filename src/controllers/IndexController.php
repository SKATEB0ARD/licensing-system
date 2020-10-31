<?php
namespace Licensing\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class IndexController {

    public function index(Request $request, Response $response, $args = []) {
        $response->getBody()->write("License System v1.0");

        return $response;
    }

}