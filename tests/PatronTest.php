<?php
    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */


    require_once "src/Patron.php";
    // require_once "src/Bar.php";
    // require_once "src/Item.php";

    $server = 'mysql:host=localhost;dbname=beer_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class PatronTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Patron::deleteAll();
            $GLOBALS['DB']->exec("DELETE FROM tokens;");
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

        // function testAddToken()
        // {
        //     //Arrange
        //
        //     $name = "Kyle Pratuch";
        //     $email = "kyle.pratuch@gmail.com";
        //     $test_recipient = new Patron ($name, $email);
        //     $test_recipient->save();
        //
        //     $name2 = "Jason Bethel";
        //     $email2 = "jlbethel@gmail.com";
        //     $test_sender = new Patron ($name2, $email2);
        //     $test_sender->save();
        //
        //     $test_menu = 3;
        //
        //     //Act
        //     $test_recipient->addToken($test_sender, $test_menu);
        //     $result = $test_recipient->getTokens();
        //
        //     //Assert
        //     $this->assertEquals([$test_recipient->getId(), $test_menu, $test_sender->getId()])
        // }
































    }
