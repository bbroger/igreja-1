<?php

namespace Slim;
use Psr\Http\Message\ResponseInterface;

Class View extends Views\PhpRenderer {
    protected $layout;

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function render(ResponseInterface $response, $template, array $data = [])
    {
        if ($this->layout){
            $viewOutput = $this->fetch($template, $data);
            $layoutOutput = $this->fetch($this->layout, array('content' => $viewOutput));
            $response->getBody()->write($layoutOutput);
        } else {
            $output = parent::render($response, $template, $data);
            $response->getBody()->write($output);
        }
        return $response;
    }
}

?>