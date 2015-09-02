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
            $GLOBALS['DB']->exec("INSERT INTO bars (name, phone, address, website) VALUES (
                '{$this->getName()}',
                '{$this->getPhone()}',
                '{$this->getAddress()}',
                '{$this->getWebsite()}');");
                $this->id = $GLOBALS['DB']->lastInsertId();
        }

        //Delete Solo Method
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM bars WHERE id = {$this->getId()};");
        }

        //Update Method
        function update($new_name, $new_phone, $new_address, $new_website)
        {
            $GLOBALS['DB']->exec("UPDATE bars SET name = '{$new_name}', phone = '{$new_phone}', address = '{$new_address}', website = '{$new_website}' WHERE id = {$this->getId()};");
            $this->setName($new_name);
            $this->setPhone($new_phone);
            $this->setAddress($new_address);
            $this->setWebsite($new_website);
        }

        //Tokens Method
        function getAllTokens()
        {
            $results = $GLOBALS['DB']->query
                ("SELECT tokens.* FROM
                    bars JOIN menus ON (bars.id = menus.bar_id)
                    JOIN tokens ON (tokens.menu_id = menus.id)
                    WHERE bars.id = {$this->getId()};
            ");

            $tokens = array();
            foreach($results as $token) {
                $patron_id = $token['patron_id'];
                $menu_id = $token['menu_id'];
                $sender_id = $token['sender_id'];
                $id = $token['id'];
                $new_token = new Token($patron_id, $menu_id, $sender_id, $id);
                array_push($tokens, $new_token);
            }
            return $tokens;
        }

        //Items Method
        function addItem($item)
        {
            $GLOBALS['DB']->exec("INSERT INTO menus (bar_id, item_id)
                        VALUES ({$this->getId()}, {$item->getId()});");

        }

        function getAllItems()
        {
            $results = $GLOBALS['DB']->query("SELECT items.* FROM
                bars JOIN menus ON (bars.id = menus.bar_id)
                JOIN items ON (menus.item_id = items.id)
                WHERE bars.id = {$this->getId()}
                ORDER BY items.cost;
                ");
            $items = array();
            foreach($results as $item) {
                $description = $item['description'];
                $cost = $item['cost'];
                $id = $item['id'];
                $new_item = new Item($description, $cost, $id);
                array_push($items, $new_item);
            }
            return $items;
        }

        //Static Methods
        static function getAll()
        {
            $returned_bars = $GLOBALS['DB']->query("SELECT * FROM bars ORDER BY name;");
            $bars = array();
            foreach ($returned_bars as $bar) {
                $name = $bar['name'];
                $phone = $bar['phone'];
                $address = $bar['address'];
                $website = $bar['website'];
                $id = $bar['id'];
                $new_bar = new Bar($name, $phone, $address, $website, $id);
                array_push($bars, $new_bar);
            }
            return $bars;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM bars;");
            $GLOBALS['DB']->exec("DELETE FROM menus;");
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

        static function search($search_name)
        {
            $found_bar = NULL;
            $bars = Bar::getAll();
            foreach($bars as $bar) {
                $name = $bar->getName();
                if($name == $search_name) {
                    $found_bar = $bar;
                }
            }
            return $found_bar;
        }

    }

 ?>
