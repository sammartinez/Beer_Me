<?php

    class Bar
    {
        private $name;
        private $phone;
        private $address;
        private $website;
        private $id;

        //Constructor
        function __construct($name, $phone, $address, $website, $id = null)
        {
            $this->name = $name;
            $this->phone = $phone;
            $this->address = $address;
            $this->website = $website;
            $this->id = $id;
        }

        //Getters
        function getName()
        {
            return $this->name;
        }

        function getPhone()
        {
            return $this->phone;
        }

        function getAddress()
        {
            return $this->address;
        }

        function getWebsite()
        {
            return $this->website;
        }

        function getId()
        {
            return $this->id;
        }

        //Setters
        function setName($new_name)
        {
            $this->name = $new_name;
        }

        function setPhone($new_phone)
        {
            $this->phone = $new_phone;
        }

        function setAddress($new_address)
        {
            $this->address = $new_address;
        }

        function setWebsite($new_website)
        {
            $this->website = $new_website;
        }

        //Save method
        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO bars (name, address, phone, website) VALUES (
                '{$this->getName()}','{$this->getAddress()}',
                '{$this->getPhone()}', '{$this->getWebsite()}');");
                $this->id = $GLOBALS['DB']->lastInsertId();
        }

        //Delete Solo Method
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM bars WHERE id = {$this->getId()};");
        }

        //Update Method
        function update($new_name, $new_address, $new_phone, $new_website)
        {
            $GLOBALS['DB']->exec("UPDATE bars SET name = '{$new_name}', address = '{$new_address}', phone = '{$new_phone}', website = '{$new_website}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
            $this->setAddress($new_address);
            $this->setPhone($new_phone);
            $this->setWebsite($new_website);
        }

        //Static Methods
        static function getAll()
        {
            $returned_bars = $GLOBALS['DB']->query("SELECT * FROM bars;");
            $bars = array();
            foreach ($returned_bars as $bar) {
                $name = $bar['name'];
                $address = $bar['address'];
                $phone = $bar['phone'];
                $website = $bar['website'];
                $id = $bar['id'];
                $new_bar = new Bar($name, $address, $phone, $website, $id);
                array_push($bars, $new_bar);
            }
            return $bars;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM bars;");
            //Delete from all join tables that are assoicated with the bar_id..
        }

        static function find($search_id)
        {
            $found_bar = null;
            $all_bars = Bar::getAll();
            foreach ($all_bars as $bar) {
                if ($bar->getId() == $search_id) {
                    $found_bar = $bar;
                }
            }
            return $found_bar;
        }

    }

 ?>
