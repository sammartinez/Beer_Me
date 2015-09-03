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

    //Get Calls ==================================================
        $app->get("/", function() use($app) {

            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => false, "sign_in" => false, 'team' => false));
        });

        $app->get("/signup", function() use($app) {

            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => true, "sign_in" => false, 'team' => false, 'customer_signup' => false, 'business_signup' => false));
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


    //Get Login Call
    $app->get("/login", function() use($app) {
        $username = $_GET['username'];
        $user = Patron::search($username);
        $all_bars = Bar::getAll();

        $bar = Bar::search($username);

        if ($bar == NULL && $user == NULL) {

            return $app['twig']->render("index.html.twig", array('about' => false, 'sign_up' => false, "sign_in" => false, 'team' => false));

        } elseif($bar == NULL) {

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
            ));}

    });

    //Sign Up Routes:

    $app->get("/show_customer_signup", function() use($app) {
        return $app['twig']->render('index.html.twig', array('about' => false, 'sign_up' => true, "sign_in" => false, 'team' => false,
            'customer_signup' => true,
            'business_signup' => false
        ));
    });

    $app->get("/show_business_signup", function() use($app) {
        return $app['twig']->render('index.html.twig', array('about' => false, 'sign_up' => true, "sign_in" => false, 'team' => false,
            'customer_signup' => false,
            'business_signup' => true
        ));
    });

    $app->post("/customer_signup", function() use($app) {
        $new_user = new Patron($_POST['username'], $_POST['email']);
        $new_user->save();

        return $app['twig']->render("signup_confirmation.html.twig");
    });

    $app->post("/business_signup", function() use($app) {
        $new_bar = new Bar($_POST['name'], $_POST['phone'], $_POST['address'], $_POST['website']);
        $new_bar->save();

        return $app['twig']->render("signup_confirmation.html.twig");
    });

    //Get Show email search

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

    //Get Show User Tokens
    $app->get("/show_user_tokens/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $tokens = $user->getTokens();

        return $app['twig']->render("patron.html.twig", array(
            'user' => $user,
            'user_tokens' => $tokens,
            // 'all_bars' => $all_bars,
            'preferred_bars' => false,
            'send_token' => false,
            'token_form' => true,
            'edit_user' => false
            ));
    });

    //Get Show User edit
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

    //Get Show preferred bars
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

    //Get Show Bar Tokens
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
        $bar_id = $menu_item[0];
        $bar = Bar::find($bar_id);
        return $app['twig']->render('redeem_token.html.twig', array(
            'token' => $token,
            'item' => $item,
            'bar' => $bar
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
        $bar = Bar::find($bar_id);
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

    //Get call to link to a bar with what the token is valid for
    $app->get('/view_token/{token_id}', function($token_id) use ($app) {
        $token = Token::find($token_id);
        $menu_item = $token->getMenuItem();
        $bar_id = $menu_item[0];
        $bar = Bar::find($bar_id);

        $item_id = $menu_item[1];
        $item = Item::find($item_id);



        return $app['twig']->render("view_token.html.twig", array(
            'bar' => $bar,
            'item' => $item,
            'token' => $token
        ));
    });


    //Get Show Menu Items

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


    //Get Show Bar Edit

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

    //Get find_friend {id}
    $app->get("/find_friend/{id}", function($id) use($app) {
        $user = Patron::find($id);
        $friend_username = $_GET['search_email'];
        $friend = Patron::search($friend_username);
        // $friend_bars = $friend->getPreferredBars();
        $selected_bar = [];
        $shopping_cart = null;
        $displayed_cart = null;

        if ($friend != NULL){
            return $app['twig']->render("send_token.html.twig", array(
                'user' => $user,
                'friend' => $friend,
                'friend_bars' => $friend->getPreferredBars(),
                'selected_bar' => $selected_bar,
                'shopping_cart' => $shopping_cart,
                'displayed_cart' => $displayed_cart
        ));
        } else {
            return $app['twig']->render("patron.html.twig", array(
                'user' => $user,
                'user_tokens' =>$user->getTokens(),
                'all_bars' => Bar::getAll(),
                'preferred_bars' => false,
                'send_token' => true,
                'token_form' => false,
                'edit_user' => false
                ));
        }
    });

    //Get Select Bar {id}
    $app->get("/select_bar/{id}/{friend_id}", function($id, $friend_id) use($app) {
        $user = Patron::find($id);
        $friend = Patron::find($friend_id);
        $friend_bars = $friend->getPreferredBars();
        $selected_bar = [];
        $shopping_cart = null;
        $displayed_cart = null;

        return $app['twig']->render("send_token.html.twig", array(
            'user' => $user,
            'friend' => $friend,
            'friend_bars' => $friend_bars,
            'selected_bar' => $selected_bar,
            'shopping_cart' => $shopping_cart,
            'displayed_cart' => $displayed_cart
        ));
    });


    //Patch Calls =================================================

    //Patch Edit User {id}
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

    //Patch Edit Bar {id}
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

    //Delete Calls ==================================================
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

    //Post Calls ====================================================

    //Post Add Preferred bar {id}
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

    //Post Select Bar {id}/{friend_id}
    $app->post("/select_bar/{id}/{friend_id}", function($id, $friend_id) use($app) {
        $user = Patron::find($id);
        $friend = Patron::find($friend_id);
        $friend_bars = $friend->getPreferredBars();
        $selected_bar = Bar::find($_POST['select_bar']);
        $shopping_cart = null;
        $displayed_cart = null;

        return $app['twig']->render("send_token.html.twig", array(
            'user' => $user,
            'friend' => $friend,
            'friend_bars' => $friend_bars,
            'selected_bar' => $selected_bar,
            'shopping_cart' => $shopping_cart,
            'displayed_cart' => $displayed_cart
        ));
    });

    //Post Add Token {id}/{friend_id}/{bar_id}
    $app->post("/add_token/{id}/{friend_id}/{bar_id}", function($id, $friend_id, $bar_id) use($app) {
        $user = Patron::find($id);
        $friend = Patron::find($friend_id);
        $friend_bars = $friend->getPreferredBars();
        $selected_bar = Bar::find($bar_id);
        $item_id = $_POST['item_id'];
        $item = Item::find($item_id);
        $menu_id = $selected_bar->getMenuId($item);
        $new_token = new Token($friend_id, $menu_id, $id);
        $new_token->save();

        $mail = new PHPMailer();
        // $mail->SMTPDebug = 3;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'beerme.token@gmail.com';
        $mail->Password = 'b33rm3123';
        $mail->STMPSecure = 'ssl';
        $mail->Port = 587;

        $email_confirmation = $friend->getEmail();
        $user_name = $friend->getName();

        $mail->From = 'beerme.token@gmail.com';
        $mail->FromName = 'Beer Me!';
        $mail->addAddress($email_confirmation, $user_name);
        $mail->addReplyTo('beerme.token@gmail.com', 'Beer Me!');
        $mail->isHTML(true);

        $mail->Subject = 'Somebody sent you a token!';
        $mail->Body = "<a href='http://localhost:8000/confirmation/$friend_id'>Click here to view your token.</a>";
        $mail->AltBody = 'You received a token!  Log in to your account to view your token.';
        $mail->send();

        return $app['twig']->render("token_confirmation.html.twig", array('user' => $user, 'friend' => $friend
        ));
    });

    /* Route from email link back to patron page */
    $app->get("/confirmation/{id}", function($id) use($app) {
        $user = Patron::find($id);
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

    // $app->post("/add_token/{id}/{friend_id}/{bar_id}", function($id, $friend_id, $bar_id) use($app) {
    //     $user = Patron::find($id);
    //     $friend = Patron::find($friend_id);
    //     $friend_bars = $friend->getPreferredBars();
    //     $selected_bar = Bar::find($bar_id);
    //     $item_id = $_POST['item_id'];
    //     $item = Item::find($item_id);
    //     $menu_id = $selected_bar->getMenuId($item);
    //     $new_token = new Token($friend_id, $menu_id, $id);
    //     // var_dump($new_token);
    //     $shopping_cart = [$_POST['shopping_cart']];
    //     // $shopping_cart = array();
    //     // var_dump($shopping_cart);
    //     array_push($shopping_cart, $new_token);
    //     var_dump($shopping_cart);
    //     // if ($shopping_cart != array()) {
    //     //     $shopping_cart = array_push($shopping_cart, $new_token);
    //     // } else {
    //     //     $shopping_cart = array();
    //     //     $shopping_cart = array_push($shopping_cart, $new_token);
    //     // }
    //
    //
    //     // $item = $selected_bar->getItem($menu_id);
    //     $displayed_cart = array($_POST['displayed_cart']);
    //     $displayed_cart = array_push($displayed_cart, $item);
    //
    //     return $app['twig']->render("send_token.html.twig", array(
    //         'user' => $user,
    //         'friend' => $friend,
    //         'friend_bars' => $friend_bars,
    //         'selected_bar' => $selected_bar,
    //         'shopping_cart' => $shopping_cart,
    //         'displayed_cart' => $displayed_cart
    //     ));
    // });
    //
    // $app->post("submit_token/{id}/{friend_id}", function ($id, $friend_id) use ($app) {
    //     $user = Patron::find($id);
    //     $friend = Patron::find($friend_id);
    //     $shopping_cart = $_POST['current_shopping_cart'];
    //     foreach($shopping_cart as $token) {
    //         $patron_id = $token['patron_id'];
    //         $menu_id = $token['menu_id'];
    //         $sender_id = $token['sender_id'];
    //         $new_token = new Token($patron_id, $menu_id, $sender_id);
    //         $new_token->save();
    //     }
    //     return $app['twig']->render("token_confirmation.html.twig", array('user' => $user, 'friend' => $friend));
    // });


    return $app;
?>
