<?php

namespace lib;

use http\Header;

class System extends Router
{
    private $url;
    private $exploder;
    private $area;
    private $controller;
    private $runController;
    private $action;
    private $params;

    public function __construct()
    {
        //$this->setUrl();
        //$this->setExploder();
        //$this->setArea();
        //$this->setController();
        //$this->setAction();
        //$this->setParams();
    }

    private function setUrl()
    {
        $this->url = isset($_GET["url"]) ? $_GET["url"] : 'home/index';
    }

    private function setExploder()
    {
        $this->exploder = exploder('/', $this->url);
    }

    private function setArea()
    {
        $this->area = $this->routerOnDefault;

        foreach ($this->routers as $index => $value)
        {
            if($this->onDefault && $this->exploder[0] == $index && !empty($value))
            {
                $this->area = $value;
                $this->onDefault = false;
            }
        }

        if(!define('APP_AREA'))
        {
            define('APP_AREA', $this->area);
        }
    }

    public function getArea()
    {
        return $this->area;
    }

    private function setController()
    {
        $this->controller = $this->onDefault
                                    ?
                                    $this->exploder[0]
                                    :
                                    ((!isset($this->exploder[1]) || is_null($this->exploder[1]) || empty($this->exploder[1])) ? 'home' : $this->exploder[1]);
    }

    public function getController()
    {
        return $this->controller;
    }

    private function setAction()
    {
        $this->action = $this->onDefault
                                ?
                                ((!isset($this->exploder[1]) || is_null($this->exploder[1]) || empty($this->exploder[1])) ? 'index' : $this->exploder[1])
                                :
                                ((!isset($this->exploder[2]) || is_null($this->exploder[2]) || empty($this->exploder[2])) ? 'index' : $this->exploder[2]);
    }

    public function getAction()
    {
        return $this->action;
    }

    private function setParams()
    {
        if($this->onDefault)
        {
            unset($this->exploder[0], $this->exploder[1]);
        }
        else
            {
                unset($this->exploder[0], $this->exploder[1], $this->exploder[2]);
            }

        if(end($this->exploder) == null)
        {
            array_pop($this->exploder);
        }

        if(empty($this->exploder))
        {
            $this->params = array();
        }
        else
            {
                foreach ($this->exploder as $value)
                {
                    $params[] = $value;
                }

                $this->params = $params;
            }
    }

    public function getParam($index)
    {
        return isset($this->params[$index]) ? $this->params[$index] : null;
    }

    private function validateController()
    {
        if(!class_exists($this->runController))
        {
            header("HTTP/1.0 404 Not Fount");
            define('ERROR', 'Não foi localizado o controller: ' . $this->controller);
            include("content/{$this->area}/shared/404_error.phtml");
            exit();
        }
    }

    private function  validateAction()
    {
        if(!method_exists($this->runController, $this->action))
        {
            header("HTTP/1.0 404 Not Fount");
            define('ERROR', 'Não foi localizado a action: ' . $this->action);
            include("content/{$this->area}/shared/404_error.phtml");
            exit();
        }
    }

    public function run()
    {
        $this->runController = 'controller\\' . $this->area . '\\' . $this->controller . 'Controller';

        self::validateController();

        $this->runController = new $this->runController();

        self::validateAction();

        $action = $this->action;

        $this->runController->$action();
    }
}
