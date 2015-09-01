<?php

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Bar.php";
    require_once __DIR__."/../src/Item.php";
    require_once __DIR__."/../src/Patron.php";

    $app = New Silex\Application();
    $app['debug'] = true;

    $server = 'mysql:host=localhost:8889;dbname=beer';
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
        return $app['twig']->render("index.html.twig", array('sign_in' => false, 'sign_up' => false));
    });

    $app->get("/signup", function() use($app) {
        return $app['twig']->render("index.html.twig", array('sign_in' => false, 'sign_up' => true));
    });

    $app->get("/signin", function() use($app) {
        return $app['twig']->render("index.html.twig", array('sign_in' => true, 'sign_up' => false));
    });

    return $app;

?>
