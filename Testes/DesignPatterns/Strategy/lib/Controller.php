<?php

namespace lib;

class Controller extends System
{
    private $path;
    private $pathRender;

    protected $title;
    protected  $description;
    protected $keywords;
    protected $image;
    protected $captionController;
    protected $captionAction;
    protected $captionParams;

    public $data;
    public $layout;

    public function __construct()
    {
        parent::__construct();
    }

    private function setPath($render)
    {
        if(is_array($render))
        {
            foreach ($render as $value)
            {
                $file = 'view/' . $this->getArea() . '/' . $this->getController() . '/' . $value . '/' . 'phtml';
                self::fileExists($file);
                $this->path[] = $file;
            }
        }
        else
            {
                $this->pathRender = is_null($render) ? $this->getAction() : $render;

                $this->path = 'view/' . $this->getArea() . '/' . $this->getController() . '/' . $render . '/' . 'phtml';

                file_exists($this->path);
            }
    }

    private function fileExists($file)
    {
        if(!file_exists($file))
        {
            die('Não foi localizado o arquivo' . $file);
        }
    }

    public function view($render = null)
    {
        $this->title =  $this->title ?? 'Meu titulo';
        $this->description = $this->description ?? 'Minha descrição';
        $this->keywords = $this->keywords ?? 'Minha palavra chave';

        $this->setPath($render);

        $this->layout = is_null($this->layout) ? $render : "contant/{$this->getArea()}/shared/{$this->layout}.phtml";
        
        if(file_exists($this->layout))
        {
            $this->$render($this->layout);
        }
        else
            {
                die('Não foi possível localizar o layout ' . $this->layout);
            }
    }

    public function render($file = null)
    {
        if(is_array($this->data) && count($this->data) > 0)
        {
            extract($this->data, EXTR_PREFIX_ALL, 'view');
            extract(array
                        (
                            'controler' => (is_null($this->captionController) ? '' : $this->captionController),
                            'action' => (is_null($this->captionAction) ? '' : $this->captionAction),
                            'params' => (is_null($this->captionParams) ? '' : $this->captionParams),
                        ),
                         EXTR_PREFIX_ALL, 'caption'
            );

            if(!is_null($file) && is_array($file))
            {
                foreach ($file as $value)
                {
                    include ($value);
                }
            }
            else
                if(is_null($file) && is_array($this->path))
                {
                    foreach ($this->path as $value)
                    {
                        include($value);
                    }
                }
                else
                    {
                        $file = is_null($file) ? $this->path : $file;
                        file_exists($file) ? include ($file) : die('Arquivo não localizado ' . $file);
                    }
        }
    }

}