<?php

    class Patron
    {
        private $name;
        private $email;
        private $id;

        function __construct($name, $email, $id = null)
        {
            $this->name = $name;
            $this->email = $email;
            $this->id = $id;
        }

        function setName($new_name)
        {
            $this->name = (string) $new_name;
        }

        function getName()
        {
            return $this->name;
        }

        function setEmail($new_email)
        {
            $this->email = (string) $new_email;
        }

        function getEmail()
        {
            return $this->email;
        }

        function getId()
        {
            return $this->id;
        }

        //Delete a single patron:
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons WHERE id = {$this->getId()};");
        }

        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO patrons (name, email) VALUES
                ('{$this->getName()}',
                '{$this->getEmail()}');
            ");

            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        static function getAll()
        {
            $returned_patrons = $GLOBALS['DB']->query("SELECT * FROM patrons;");
            $patrons = array();
            foreach ($returned_patrons as $patron) {
                $name = $patron['name'];
                $email = $patron['email'];
                $id = $patron['id'];
                $new_patron = new Patron($name, $email, $id);
                array_push($patrons, $new_patron);
            }
            return $patrons;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM patrons;");
        }



        //Find patron by id OR email:
        static function find($search_id)
        {
            $found_patron = NULL;
            $patrons = Patron::getAll();
            foreach($patrons as $patron) {
                $patron_id = $patron->getId();
                if($patron_id == $search_id) {
                    $found_patron = $patron;
                }
            }
            return $found_patron;
        }

        static function search($search_email)
        {
            $found_patron = NULL;
            $patrons = Patron::getAll();
            foreach($patrons as $patron) {
                $patron_email = $patron->getEmail();
                if($patron_email == $search_email) {
                    $found_patron = $patron;
                }
            }
            return $found_patron;
        }



        //Edit patron's info:
        function updatePatron($new_name, $new_email)
        {
            $GLOBALS['DB']->exec("UPDATE patrons SET
                 name = '{$new_name}',
                 email = '{$new_email}'
             WHERE id = {$this->getId()};");
             $this->setName($new_name);
             $this->setEmail($new_email);
        }

        function getTokens()
        {
            $returned_tokens = $GLOBALS['DB']->query("SELECT * FROM tokens WHERE patron_id = {$this->getId()};");

            $tokens = array();
            foreach($returned_tokens as $token) {
                $token_id = $token['id'];
                $new_token = Token::find($token_id);
                array_push($tokens, $new_token);
            }
            return $tokens;
        }

        function addPreferredBar($bar)
        {
            $GLOBALS['DB']->exec("INSERT INTO preferbars (patron_id, bar_id)
                        VALUES ({$this->getId()}, {$bar->getId()});");
        }

        function getPreferredBars()
        {
            $returned_bars = $GLOBALS['DB']->query("SELECT * FROM preferbars WHERE patron_id = {$this->getId()};");

            $preferred_bars = array();
            foreach($returned_bars as $bar) {
                $bar_id = $bar['bar_id'];
                $new_bar = Bar::find($bar_id);
                array_push($preferred_bars, $new_bar);
            }
            return $preferred_bars;
        }

        function deleteBar($bar)
        {
            $GLOBALS['DB']->exec("DELETE * FROM preferbars WHERE patron_id = {$this->getId()} AND bar_id = {$bar->getId()};");
        }

    }





?>
