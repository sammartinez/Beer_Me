<?php

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Bar.php";
    require_once __DIR__."/../src/Item.php";
    require_once __DIR__."/../src/Patron.php";

    $app = New Silex\Application();

    $server = 'mysql:host=localhost;dbname=beer';
    $username = 'root';
    $password = 'root';

    $DB = new PDO($server, $username, $password);

    //Twig Path
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path'=>__DIR__."/../views"
));

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodParameterOverride();

    //Get Calls
    $app->get("/", function() use($app) {
        return $app['twig']->render("index.html.twig");
    });

    return $app;

?>
