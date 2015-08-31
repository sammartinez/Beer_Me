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

        function testGetCost()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);

            //Act
            $result = $test_item->getCost();

            //Assert
            $this->assertEquals($cost, $result);
        }

        function testGetId()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = 1;
            $test_item = new Item($description, $cost, $id);

            //Act
            $result = $test_item->getId();

            //Assert
            $this->assertEquals($id, $result);
        }

        function testSave()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);

            //Act
            $test_item->save();
            $result = Item::getAll();

            //Assert
            $this->assertEquals($test_item, $result[0]);
        }

        function testGetAll()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);
            $test_item->save();

            $description2 = "Lagunitas IPA";
            $cost2 = 7.00;
            $test_item2 = new Item($description2, $cost2, $id);
            $test_item2->save();

            //Act
            $result = Item::getAll();

            //Assert
            $this->assertEquals([$test_item, $test_item2], $result);
        }

        function testUpdate()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);
            $test_item->save();

            $new_description = "Pliny the Younger";
            $new_cost = 10.00;

            //Act
            $test_item->update($new_description, $new_cost);

            //Assert
            $this->assertEquals($new_description, $test_item->getDescription());
            $this->assertEquals($new_cost, $test_item->getCost());
        }

        function testDelete()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);
            $test_item->save();

            $description2 = "Lagunitas IPA";
            $cost2 = 7.00;
            $test_item2 = new Item($description2, $cost2, $id);
            $test_item2->save();

            //Act
            $test_item->delete();
            $result = Item::getAll();

            //Assert
            $this->assertEquals($test_item2, $result[0]);
        }

        function testFind()
        {
            //Arrange
            $description = "Pliny the Elder";
            $cost = 5.00;
            $id = null;
            $test_item = new Item($description, $cost, $id);
            $test_item->save();

            $description2 = "Lagunitas IPA";
            $cost2 = 7.00;
            $test_item2 = new Item($description2, $cost2, $id);
            $test_item2->save();

            //Act
            $result = Item::find($test_item2->getId());

            //Assert
            $this->assertEquals($test_item2, $result);
        }
    }



?>
