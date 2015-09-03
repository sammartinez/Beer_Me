<?php


    class Token
    {
        private $patron_id;
        private $menu_id;
        private $sender_id;
        private $id;

        //Constructors
        function __construct($patron_id, $menu_id, $sender_id, $id = null)
        {
            $this->patron_id = $patron_id;
            $this->menu_id = $menu_id;
            $this->sender_id = $sender_id;
            $this->id = $id;
        }

        //Getters
        function getPatronId()
        {
            return $this->patron_id;
        }

        function getMenuId()
        {
            return $this->menu_id;
        }

        function getSenderId()
        {
            return $this->sender_id;
        }

        function getId()
        {
            return $this->id;
        }

        //Save Method
        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO tokens (patron_id, menu_id, sender_id) VALUES (
                '{$this->getPatronId()}',
                '{$this->getMenuId()}',
                '{$this->getSenderId()}');");
                $this->id = $GLOBALS['DB']->lastInsertId();
        }

        //Delete single token
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM tokens WHERE id = {$this->getId()};");
        }

        //Static functions
        static function getAll()
        {
            $returned_tokens = $GLOBALS['DB']->query("SELECT * FROM tokens;");
            $tokens = array();
            foreach ($returned_tokens as $token) {
                $patron_id = $token['patron_id'];
                $menu_id = $token['menu_id'];
                $sender_id = $token['sender_id'];
                $id = $token['id'];
                $new_token = new Token($patron_id, $menu_id, $sender_id, $id);
                array_push($tokens, $new_token);
            }
            return $tokens;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM tokens;");
        }

        static function find($search_id)
        {
            $found_token = null;
            $all_tokens = Token::getAll();
            foreach ($all_tokens as $token) {
                if ($token->getId() == $search_id) {
                    $found_token = $token;
                }
            }
            return $found_token;
        }

        function getMenuItem()
        {
            $returned_items = $GLOBALS['DB']->query("SELECT * FROM menus WHERE id = {$this->getMenuId()};");

            $menu_items = array();
            foreach($returned_items as $item) {
                $bar_id = $item['bar_id'];
                array_push($menu_items, $bar_id);
                $item_id = $item['item_id'];
                // $id = ['id'];
                array_push($menu_items, $item_id);
            }
            return $menu_items;
        }

        function getPatronName()
        {
            $returned_patrons = $GLOBALS['DB']->query("SELECT name FROM patrons WHERE id = {$this->getPatronId()};");

            $names = array();
            foreach($returned_patrons as $name){
                $patron_name = $name['name'];
            }
            return $patron_name;
        }

        function getBarName()
        {
            $result = $GLOBALS['DB']->query(
                "SELECT name FROM
                    bars JOIN menus ON (bars.id = menus.bar_id)
                         JOIN tokens ON (tokens.menu_id = menus.id)
                         WHERE menus.id = {$this->getMenuId()};");

            $bar_name = $result->fetchColumn();
            return $bar_name;
        }

        function getBarId()
        {

        }
    }

 ?>
