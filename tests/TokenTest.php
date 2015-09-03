<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once 'src/Token.php';

    $server = 'mysql:host=localhost;dbname=beer_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class TokenTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Token::deleteAll();
        }

        function testGetPatronId()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);

            $result = $test_token->getPatronId();

            $this->assertEquals($patron_id, $result);
        }

        function testGetMenuId()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);

            $result = $test_token->getMenuId();

            $this->assertEquals($menu_id, $result);
        }

        function testGetSenderId()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);

            $result = $test_token->getSenderId();

            $this->assertEquals($sender_id, $result);
        }

        function testGetId()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);

            $result = $test_token->getId();

            $this->assertEquals(null, $result);
        }

        function testSave()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id, $id = null);
            $test_token->save();

            $result = Token::getAll();

            $this->assertEquals($test_token, $result[0]);
        }

        function testGetAll()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);
            $test_token->save();

            $patron_id2 = 4;
            $menu_id2 = 5;
            $sender_id2 = 6;
            $test_token2 = new Token($patron_id2, $menu_id2, $sender_id2);
            $test_token2->save();

            $result = Token::getAll();

            $this->assertEquals([$test_token, $test_token2], $result);
        }

        function testDeleteAll()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);
            $test_token->save();

            $patron_id2 = 4;
            $menu_id2 = 5;
            $sender_id2 = 6;
            $test_token2 = new Token($patron_id2, $menu_id2, $sender_id2);
            $test_token2->save();

            Token::deleteAll();
            $result = Token::getAll();

            $this->assertEquals([], $result);
        }

        function testFind()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);
            $test_token->save();

            $patron_id2 = 4;
            $menu_id2 = 5;
            $sender_id2 = 6;
            $test_token2 = new Token($patron_id2, $menu_id2, $sender_id2);
            $test_token2->save();

            $result = Token::find($test_token->getId());

            $this->assertEquals($test_token, $result);
        }

        function testDelete()
        {
            $patron_id = 1;
            $menu_id = 2;
            $sender_id = 3;
            $test_token = new Token($patron_id, $menu_id, $sender_id);
            $test_token->save();

            $patron_id2 = 4;
            $menu_id2 = 5;
            $sender_id2 = 6;
            $test_token2 = new Token($patron_id2, $menu_id2, $sender_id2);
            $test_token2->save();

            $test_token->delete();
            $result = Token::getAll();

            $this->assertEquals([$test_token2], $result);
        }

        // function testGetPatronName()
        // {
        //     //Arrange
        //     $patron_id = 1;
        //     $menu_id = 2;
        //     $sender_id = 3;
        //     $test_token = new Token($patron_id, $menu_id, $sender_id);
        //     $test_token->save();
        //
        //     $name = "Kyle Pratuch";
        //     $email = "kyle.pratuch@gmail.com";
        //     $test_patron = new Patron ($name, $email);
        //     $test_patron->save();
        //
        //     //Act
        //     $test_token->getPatronName();
        //     $result = $name;
        //
        //     //Assert
        //     $this->assertEquals();

        // }

    }

 ?>
