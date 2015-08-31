<?php

    /**
    * @backupGlobals disabled
    * @backupStaticAttributes disabled
    */

    require_once 'src/Bar.php';

    $server = 'mysql:host=localhost;dbname=beer_test';
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    class BarTest extends PHPUnit_Framework_TestCase
    {
        protected function tearDown()
        {
            Bar::deleteAll();
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

    }

 ?>
