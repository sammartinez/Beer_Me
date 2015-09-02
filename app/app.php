<?php

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__.'/../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
    require_once __DIR__."/../src/Bar.php";
    require_once __DIR__."/../src/Item.php";
    require_once __DIR__."/../src/Patron.php";



    $app = New Silex\Application();
    $app['debug'] = true;

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
        return $app['twig']->render("index.html.twig", array('sign_in' => false, 'sign_up' => false));
    });

    $app->get("/signup", function() use($app) {
        return $app['twig']->render("index.html.twig", array('sign_in' => false, 'sign_up' => true));
    });

    $app->get("/signin", function() use($app) {
        return $app['twig']->render("index.html.twig", array('sign_in' => true, 'sign_up' => false));
    });


    $app->get("/login", function() use($app) {
        $username = $_GET['username'];
        $user = Patron::search($username);
        $all_bars = Bar::getAll();
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' => $user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => false,
            'send_token' => false,
            'token_form' => false,
            'edit_user' => false
            ));
    });

    $app->get("/show_email_search/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' =>$user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => false,
            'send_token' => true,
            'token_form' => false,
            'edit_user' => false
            ));
    });

    $app->get("/show_user_tokens/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' =>$user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => false,
            'send_token' => false,
            'token_form' => true,
            'edit_user' => false
            ));
    });

    $app->get("/show_user_edit/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' =>$user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => false,
            'send_token' => false,
            'token_form' => false,
            'edit_user' => true
            ));
    });

    $app->patch("/edit_user/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        $new_name = $_POST['name'];
        $new_email = $_POST['email'];
        $user->updatePatron($new_name, $new_email);
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' =>$user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => false,
            'send_token' => false,
            'token_form' => false,
            'edit_user' => false
            ));
    });


    $app->get("/show_preferred_bars/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' =>$user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => true,
            'send_token' => false,
            'token_form' => false,
            'edit_user' => false
            ));
    });

    $app->get("/about", function() use($app) {
        return $app['twig']->render("about.html.twig");
    });


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

    $app->post("/add_preferred_bar/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        $bar = Bar::find($_POST['add_bar']);
        $user->addPreferredBar($bar);
        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' =>$user->getTokens(),
            'all_bars' => $all_bars,
            'preferred_bars' => true,
            'send_token' => false,
            'token_form' => false,
            'edit_user' => false
            ));
    });

    // $app->delete("/delete_preferred_bar/{id}/{bar_id}", function($id, $bar_id) use($app) {
    //     $user = Patron::find($id);
    //     $all_bars = Bar::getAll();
    //     $bar = Bar::find($bar_id);
    //     $user->deleteBar($bar);
    //     return $app['twig']->render("patron.html.twig", array(
    //         'user' => $user,
    //         'user_tokens' =>$user->getTokens(),
    //         'all_bars' => $all_bars,
    //         'preferred_bars' => false,
    //         'send_token' => false,
    //         'token_form' => false,
    //         'edit_user' => false
    //         ));
    // });

    return $app;

?>
