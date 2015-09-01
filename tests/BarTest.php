<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once 'src/Bar.php';
    require_once 'src/Token.php';
    require_once 'src/Item.php';

    $server = 'mysql:host=localhost;dbname=beer_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BarTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Bar::deleteAll();
            Token::deleteAll();
            Item::deleteAll();
        }

        function testGetName()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);

            $result = $test_bar->getName();

            $this->assertEquals($name, $result);
        }

        function testGetPhone()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);

            $result = $test_bar->getPhone();

            $this->assertEquals($phone, $result);
        }

        function testGetAddress()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);

            $result = $test_bar->getAddress();

            $this->assertEquals($address, $result);
        }

        function testGetWebsite()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);

            $result = $test_bar->getWebsite();

            $this->assertEquals($website, $result);
        }

        function testGetId()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);

            $result = $test_bar->getId();

            $this->assertEquals(null, $result);
        }

        function testSave()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website, $id = null);
            $test_bar->save();

            $result = Bar::getAll();

            $this->assertEquals($test_bar, $result[0]);
        }

        function testGetAll()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);
            $test_bar->save();

            $name2 = "ABC Pub";
            $phone2 = "444-444-4444";
            $address2 = "321 CBA Street";
            $website2 = "http://www.sesamestreet.com";
            $test_bar2 = new Bar($name2, $phone2, $address2, $website2);
            $test_bar2->save();

            $result = Bar::getAll();

            $this->assertEquals([$test_bar2, $test_bar], $result);
        }

        function testDeleteAll()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);
            $test_bar->save();

            $name2 = "ABC Pub";
            $phone2 = "444-444-4444";
            $address2 = "321 CBA Street";
            $website2 = "http://www.sesamestreet.com";
            $test_bar2 = new Bar($name2, $phone2, $address2, $website2);
            $test_bar2->save();

            Bar::deleteAll();
            $result = Bar::getAll();

            $this->assertEquals([], $result);
        }

        function testUpdate()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);
            $test_bar->save();

            $name2 = "ABC Pub";
            $phone2 = "444-444-4444";
            $address2 = "321 CBA Street";
            $website2 = "http://www.sesamestreet.com";
            $test_bar2 = new Bar($name2, $phone2, $address2, $website2, $test_bar->getId());

            $test_bar->update($name2, $phone2, $address2, $website2);

            $this->assertEquals($test_bar, $test_bar2);
        }

        function testFind()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);
            $test_bar->save();

            $name2 = "ABC Pub";
            $phone2 = "444-444-4444";
            $address2 = "321 CBA Street";
            $website2 = "http://www.sesamestreet.com";
            $test_bar2 = new Bar($name2, $phone2, $address2, $website2);
            $test_bar2->save();

            $result = Bar::find($test_bar->getId());

            $this->assertEquals($test_bar, $result);
        }

        function testDelete()
        {
            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);
            $test_bar->save();

            $name2 = "ABC Pub";
            $phone2 = "444-444-4444";
            $address2 = "321 CBA Street";
            $website2 = "http://www.sesamestreet.com";
            $test_bar2 = new Bar($name2, $phone2, $address2, $website2);
            $test_bar2->save();

            $test_bar->delete();
            $result = Bar::getAll();

            $this->assertEquals([$test_bar2], $result);
        }

        function testAddItem()
        {

            $test_item = new Item("tacos", 2.25);
            $test_item->save();

            $name = "Side Street";
            $phone = "555-555-5555";
            $address = "123 ABC. Street";
            $website = "http://www.sidestreetpdx.com";
            $test_bar = new Bar($name, $phone, $address, $website);
            $test_bar->save();

            $test_bar->addItem($test_item);

            $result = $test_bar->getAllItems();

            $this->assertEquals($test_item, $result[0]);

        }




        // function testGetAllTokens()
        // {
        //
        //   $name = "Side Street";
        //   $phone = "555-555-5555";
        //   $address = "123 ABC. Street";
        //   $website = "http://www.sidestreetpdx.com";
        //   $test_bar = new Bar($name, $phone, $address, $website);
        //   $test_bar->save();
        //
        //   $test_token = new Token(2, )
        // }

    }

 ?>
