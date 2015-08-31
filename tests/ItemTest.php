<?php

    /**
    * @backupGlobals disabled
    * @backupStatic Attributes disabled
    */

    require_once "src/Item.php";

    $server = 'mysql:host=localhost;dbname=beer_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class ItemTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Item::deleteAll();
        }

        function testSetDescription()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);
            $new_description = "Pliny the Younger";

            //Act
            $test_item->setDescription($new_description);
            $result = $test_item->getDescription();

            //Assert
            $this->assertEquals($new_description, $result);
        }
    }



?>
