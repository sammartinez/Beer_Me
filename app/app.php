<?php

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__.'/../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
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

    $app->get('/email', function() use($app) {
        return $app['twig']->render("email.html.twig", array('message' => null));
    });

    $app->get("/about", function() use($app) {
        return $app['twig']->render("about.html.twig");
    });

    /* Testing mail shit */
    $app->post("/email_send", function() use($app) {
        $mail = new PHPMailer();
        // $mail->SMTPDebug = 3;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'beerme.token@gmail.com';
        $mail->Password = 'b33rm3123';
        $mail->STMPSecure = 'tls';
        $mail->Port = 587;

        $mail->From = 'beerme.token@gmail.com';
        $mail->FromName = 'Beer Me!';
        $mail->addAddress($_POST['email'], $_POST['name']);
        $mail->addReplyTo('beerme.token@gmail.com', 'Beer Me!');
        $mail->isHTML(true);

        $mail->Subject = 'Somebody sent you a token!';
        $mail->Body = 'HEY YOU GUYS!  LOOK WAT I DONE DID!!!!!!.';
        $mail->AltBody = 'Received token.';

        $email = $_POST['email'];
        $name = $_POST['name'];
        if(!$mail->send()) {
            $message = 'Message could not be sent. <p>';
        } else {
            $message = 'Message has been sent.';
        }
        return $app['twig']->render("email.html.twig", array('message' => $message));
    });

    return $app;

?>
