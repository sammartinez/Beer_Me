<?php

    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__.'/../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
    require_once __DIR__."/../src/Bar.php";
    require_once __DIR__."/../src/Item.php";
    require_once __DIR__."/../src/Patron.php";
    require_once __DIR__."/../src/Token.php";


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
            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => false, "sign_in" => false, 'team' => false));
        });

        $app->get("/signup", function() use($app) {
            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => true, "sign_in" => false, 'team' => false));
        });

        $app->get("/signin", function() use($app) {
            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => false, "sign_in" => true, 'team' => false));
        });

        $app->get("/about", function() use($app) {
            return $app['twig']->render("index.html.twig", array('about' => true, 'sign_up' => false, "sign_in" => false, 'team' => false));
        });

        $app->get("/team", function() use($app) {
            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => false, "sign_in" => false, 'team' => true));
        });

    $app->get("/login", function() use($app) {
        $username = $_GET['username'];
        $user = Patron::search($username);
        $all_bars = Bar::getAll();

        $bar = Bar::search($username);

        if($bar == NULL) {
            return $app['twig']->render("patron.html.twig", array(
                'user' => $user,
                'user_tokens' => $user->getTokens(),
                'all_bars' => $all_bars,
                'preferred_bars' => false,
                'send_token' => false,
                'token_form' => false,
                'edit_user' => false
            ));

            } else {
            return $app['twig']->render("bar.html.twig", array(
                'bar' => $bar,
                'tokens' => $bar->getAllTokens(),
                'items' => $bar->getAllItems(),
                'get_tokens' => false,
                'show_menu' => false,
                'edit_bar' => false
                ));
            }
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

    /* Routes for Bar Page */
    $app->get("/show_bar_tokens/{id}", function($id) use($app) {
        $bar = Bar::find($id);
        $tokens = $bar->getAllTokens();
        return $app['twig']->render("bar.html.twig", array(
        'bar' => $bar,
        'tokens' => $bar->getAllTokens(),
        'items' => $bar->getAllItems(),
        'get_tokens' => true,
        'show_menu' => false,
        'edit_bar' => false
        ));
    });

    $app->get('/token/{token_id}', function($token_id) use ($app) {
        $token = Token::find($token_id);
        $menu_item = $token->getMenuItem();
        $item_id = $menu_item[1];
        $item = Item::find($item_id);
        return $app['twig']->render('redeem_token.html.twig', array(
            'token' => $token,
            'item' => $item
        ));
    });

    $app->delete('/redeem_token/{token_id}', function($token_id) use ($app) {
        $token = Token::find($token_id);
        $token->delete();
        return $app['twig']->render("bar.html.twig", array(
            'bar' => $bar,
            'tokens' => $bar->getAllTokens(),
            'items' => $bar->getAllItems(),
            'get_tokens' => false,
            'show_menu' => false,
            'edit_bar' => false
        ));
    });

    $app->get('/redeem_token/{token_id}', function($token_id) use ($app) {
        $token = Token::find($token_id);
        $menu_item = $token->getMenuItem();
        $bar_id = $menu_item[0];
        $token->delete();
        $bar = Bar::find($bar_id);
        return $app['twig']->render("bar.html.twig", array(
            'bar' => $bar,
            'tokens' => $bar->getAllTokens(),
            'items' => $bar->getAllItems(),
            'get_tokens' => false,
            'show_menu' => false,
            'edit_bar' => false
        ));
    });

    $app->get("/show_menu_items/{id}", function($id) use($app) {
        $bar = Bar::find($id);
        $items = $bar->getAllItems();
        return $app['twig']->render("bar.html.twig", array(
        'bar' => $bar,
        'tokens' => $bar->getAllTokens(),
        'items' => $bar->getAllItems(),
        'get_tokens' => false,
        'show_menu' => true,
        'edit_bar' => false
        ));
    });

    $app->patch("/edit_item/{bar_id}/{item_id}", function($bar_id, $item_id) use($app) {
        $item = Item::find($item_id);
        $item->update($_POST['description'], $_POST['cost']);
        $bar = Bar::find($bar_id);
        return $app['twig']->render("bar.html.twig", array(
        'bar' => $bar,
        'tokens' => $bar->getAllTokens(),
        'items' => $bar->getAllItems(),
        'get_tokens' => false,
        'show_menu' => true,
        'edit_bar' => false
        ));
    });

    $app->post("/add_item/{bar_id}", function($bar_id) use($app) {
        $bar = Bar::find($bar_id);
        $item = new Item($_POST['description'], $_POST['cost']);
        $item->save();
        $bar->addItem($item);
        return $app['twig']->render("bar.html.twig", array(
        'item' => $item,
        'bar' => $bar,
        'tokens' => $bar->getAllTokens(),
        'items' => $bar->getAllItems(),
        'get_tokens' => false,
        'show_menu' => true,
        'edit_bar' => false
        ));
    });

    $app->delete('/delete_item/{bar_id}/{item_id}', function($bar_id, $item_id) use ($app) {
        $bar = Bar::find($bar_id);
        $item = Item::find($item_id);
        $item->delete();
        return $app['twig']->render("bar.html.twig", array(
        'item' => $item,
        'bar' => $bar,
        'tokens' => $bar->getAllTokens(),
        'items' => $bar->getAllItems(),
        'get_tokens' => false,
        'show_menu' => true,
        'edit_bar' => false
        ));
    });

    $app->get('/delete_item/{bar_id}/{item_id}', function($bar_id, $item_id) use ($app) {
        $bar = Bar::find($bar_id);
        $item = Item::find($item_id);
        $item->delete();
        return $app['twig']->render("bar.html.twig", array(
        'item' => $item,
        'bar' => $bar,
        'tokens' => $bar->getAllTokens(),
        'items' => $bar->getAllItems(),
        'get_tokens' => false,
        'show_menu' => true,
        'edit_bar' => false
        ));
    });

    $app->get("/show_bar_edit/{id}", function($id) use($app) {
        $bar = Bar::find($id);
        return $app['twig']->render("bar.html.twig", array(
            'bar' => $bar,
            'tokens' => $bar->getAllTokens(),
            'items' => $bar->getAllItems(),
            'get_tokens' => false,
            'show_menu' => false,
            'edit_bar' => true
        ));
    });

    $app->patch("/edit_bar/{id}", function($id) use($app) {
        $bar = Bar::find($id);
        $new_name = $_POST['name'];
        $new_phone = $_POST['phone'];
        $new_address = $_POST['address'];
        $new_website = $_POST['website'];
        $bar->update($new_name, $new_phone, $new_address, $new_website);
        return $app['twig']->render("bar.html.twig", array(
            'bar' => $bar,
            'tokens' => $bar->getAllTokens(),
            'items' => $bar->getAllItems(),
            'get_tokens' => false,
            'show_menu' => false,
            'edit_bar' => true
        ));
    });

    $app->get("/email_send", function() use($app) {
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

    $app->delete("/delete_preferred_bar/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $all_bars = Bar::getAll();
        $bar = $_POST['bar'];
        $found_bar = Bar::find($bar);
        $user->deleteBar($found_bar);
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


    return $app;
?>
