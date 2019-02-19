<?php

namespace App;

use \App\Exceptions\CustomException;

class RestRouter
{
    private $controllerNamespace;
    private $serverRequest;
    private $routeElements;
    private $postData;
    private $dependencies;

    public function __construct($serverRequest, $postData = []){
        if(empty($serverRequest)){
            throw new \Exception('Param $serverRequest is missing');
        }

        $this->serverRequest = $serverRequest;
        $this->routeElements = explode('/', $serverRequest['REQUEST_URI']);

        // With PUT and DELETE, params are not available in the $_POST variable
        if ($this->serverRequest['REQUEST_METHOD'] == "PUT" || $this->serverRequest['REQUEST_METHOD'] == "DELETE") {
            parse_str(file_get_contents('php://input'), $postData);
        }

        $this->postData = $postData;
    }

    /**
     * Find and execute the action associated with the resource
     * @return null
     * @throws CustomException
     */
    public function handleRequest(){
        $this->setController();
        return $this->executeAction();
    }

    /**
     * Get the resource Id if it exists
     * @return bool|int
     */
    private function getResourceId(){
        if(!isset($this->routeElements[2])){
            return false;
        }

        // Separates resource Id from GET params
        $params     = explode('?',$this->routeElements[2]);
        $resourceId = $params[0];

        if(isset($params[1])){
            // Handles ?foo=bar&ping=pong
            parse_str($params[1],$getParams);
            $this->setParams($getParams);
        }

        if(isset($resourceId)){
            return $resourceId;
        }else{
            return false;
        }
    }

    /**
     * Locates and instantiates the controller associated with the resource
     * @throws CustomException
     */
    private function setController(){
        if(empty($this->routeElements[1])){
            throw new CustomException('No resource specified', 400);
        }

        $this->controller = $this->getControllerInstance();
        $this->setParams($this->postData);
    }

    private function getControllerInstance(){

        $controllerName = $this->getControllerName();

        if(!class_exists($controllerName)){
            throw new CustomException('This resource doesn\'t exist', 404);
        }

        $dependencies = [];

        if(!empty($this->dependencies) ){
            if(isset($this->dependencies[$controllerName])){
                $dependencies = $this->dependencies[$controllerName];
            }
        }
        return new $controllerName(...$dependencies);
    }

    private function getControllerName(){
        return $this->getControllersNamespace() .ucfirst($this->routeElements[1]).'Controller';
    }

    private function setParams($params){
        $params = array_merge($this->controller->getParams(), $params);
        $this->controller->setParams($params);
    }

    /**
     * Executes the action matching the resource and the HTTP method
     * @return null
     * @throws CustomException
     */
    private function executeAction(){

        $resourceId = $this->getResourceId();

        switch($this->serverRequest['REQUEST_METHOD']) {
            case 'GET':
                if ($resourceId) {
                    return $this->controller->get($resourceId);
                } else {
                    return $this->controller->index();
                }
                break;
            case 'POST':
                return $this->controller->create($this->postData);
                break;
            case 'PUT':
                if(empty($resourceId)){throw new CustomException('Param $id is missing');}
                return $this->controller->update($resourceId, $this->postData);
                break;
            case 'DELETE':
                if(empty($resourceId)){throw new CustomException('Param $id is missing');}
                return $this->controller->delete($resourceId);
                break;
        }

        return null;
    }

    public function setDependencies($dependencies){
        $this->dependencies = $dependencies;
    }

    private function getControllersNamespace()
    {
        return $this->controllerNamespace .( !empty($this->controllerNamespace) ? '\\' : '');
    }

    public function setControllerNamespace($controllerNamespace)
    {
        $this->controllerNamespace = $controllerNamespace;
    }
}