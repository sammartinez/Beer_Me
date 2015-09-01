<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */


    require_once "src/Patron.php";
    require_once "src/Token.php";
    require_once "src/Bar.php";
    require_once "src/Item.php";

    $server = 'mysql:host=localhost;dbname=beer_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class PatronTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Patron::deleteAll();
            Token::deleteAll();
            Bar::deleteAll();
            Item::deleteAll();
        }

        function testSave()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);

            //Act
            $test_patron->save();
            $result = Patron::getAll();

            //Assert
            $this->assertEquals($test_patron, $result[0]);
        }

        function testGetAll()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);
            $test_patron->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";
            $test_patron2 = new Patron ($name2, $email2);
            $test_patron2->save();

            //Act
            $result = Patron::getAll();

            //Assert
            $this->assertEquals([$test_patron, $test_patron2], $result);
        }

        function testDeleteAll()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);
            $test_patron->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";
            $test_patron2 = new Patron ($name2, $email2);
            $test_patron2->save();

            //Act
            Patron::deleteAll();
            $result = Patron::getAll();

            //Assert
            $this->assertEquals([], $result);
        }

        function testDelete()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);
            $test_patron->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";
            $test_patron2 = new Patron ($name2, $email2);
            $test_patron2->save();

            //Act
            $test_patron->delete();
            $result = Patron::getAll();

            //Assert
            $this->assertEquals([$test_patron2], $result);
        }

        function testFind()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);
            $test_patron->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";
            $test_patron2 = new Patron ($name2, $email2);
            $test_patron2->save();

            //Act
            $result = Patron::find($test_patron->getId());

            //Assert
            $this->assertEquals($test_patron, $result);
        }

        function testSearch()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);
            $test_patron->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";
            $test_patron2 = new Patron ($name2, $email2);
            $test_patron2->save();

            //Act
            $result = Patron::search($test_patron->getEmail());

            //Assert
            $this->assertEquals($test_patron, $result);
        }

        function testUpdatePatron()
        {
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_patron = new Patron ($name, $email);
            $test_patron->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";

            //Act
            $test_patron->updatePatron($name2, $email2);
            $result = Patron::getAll();

            //Assert
            $this->assertEquals($test_patron, $result[0]);
        }

        
        function testGetTokens()
        {
            //Arrange
            $name = "Kyle Pratuch";
            $email = "kyle.pratuch@gmail.com";
            $test_recipient = new Patron ($name, $email);
            $test_recipient->save();

            $name2 = "Jason Bethel";
            $email2 = "jlbethel@gmail.com";
            $test_sender = new Patron ($name2, $email2);
            $test_sender->save();

            $bar_name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($bar_name, $phone, $address, $website);
            $test_bar->save();

            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);
            $test_item->save();

            $test_bar->addItem($test_item);

            $patron_id = $test_recipient->getId();
            $sender_id = $test_sender->getId();
            $menu_id = 1;
            $test_token = new Token($patron_id, $menu_id, $sender_id);
            $test_token->save();

            $menu_id2 = 2;
            $test_token2 = new Token($patron_id, $menu_id2, $sender_id);
            $test_token2->save();

            //Act

            $result = $test_recipient->getTokens();

            //Assert
            $this->assertEquals([$test_token, $test_token2], $result);
        }
































    }
