<?php
/**
 * Author: Camille BAUDRAS
 */

namespace App\Controllers;

abstract class RestControllerAbstract implements RestControllerInterface
{

    const RESOURCE_NOT_FOUND = 404;
    const RESOURCE_NOT_MODIFIED = 400;
    const INTERNAL_ERROR = 500;
    const RESOURCE_CREATED = 201;

    private $params = [];

    protected function returnJson($data, $code = 200){
        http_response_code($code);
        header('Content-type: application/json');

        echo json_encode($data); exit;
    }

    /**
     * Return GET and POST params from the request
     * @return array
     */
    public function getParams(){
        return $this->params;
    }

    public function setParams($params){
        $this->params = $params;
    }
}