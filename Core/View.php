<?php

namespace Core;
use \Twig\Extension\DebugExtension;

class View
{

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig_Loader_Filesystem('../App/Views');
            $twig = new \Twig_Environment($loader,[
                "debug"=>true
            ]);
            $twig->addExtension(new \Twig\Extension\DebugExtension());

            $twig->addGlobal('session', $_SESSION);
        }

        echo $twig->render($template, $args);
    }
}
