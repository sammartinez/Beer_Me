<?php

    class Item
    {
        private $description;
        private $cost;
        private $id;

        //Constructors
        function __construct($description, $cost, $id = null)
        {
            $this->description = $description;
            $this->cost = $cost;
            $this->id = $id;
        }

        //Getters
        function getDescription()
        {
            return $this->description;
        }

        function getCost()
        {
            return $this->cost;
        }

        function getId()
        {
            return $this->id;
        }

        //Setters
        function setDescription($new_description)
        {
            $this->description = $new_description;
        }

        function setCost($new_cost)
        {
            $this->cost = $new_cost;
        }

        //Save function
        function save()
        {
            $GLOBALS['DB']->exec("INSERT INTO items (description, cost) VALUES ('{$this->getDescription()}', {$this->getCost()});");
            $this->id = $GLOBALS['DB']->lastInsertId();
        }

        //Update function
        function update($new_description, $new_cost)
        {
            $GLOBALS['DB']->exec("UPDATE items SET description = '{$new_description}', cost = {$new_cost} WHERE id = {$this->getId()};");
            $this->setDescription($new_description);
            $this->setCost($new_cost);
        }

        //Delete single item
        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM items WHERE id = {$this->getId()};");
        }


        //Static functions
        static function getAll()
        {
            $returned_items = $GLOBALS['DB']->query("SELECT * FROM items;");
            $all_items = array();
            foreach ($returned_items as $item) {
                $description = $item['description'];
                $cost = $item['cost'];
                $id = $item['id'];
                $new_item = new Item($description, $cost, $id);
                array_push($all_items, $new_item);
            }
            return $all_items;
        }

        static function find($search_id)
        {
            $found_item = null;
            $items = Item::getAll();
            foreach($items as $item) {
                $item_id = $item->getId();
                if ($item_id == $search_id) {
                    $found_item = $item;
                }
            }
            return $found_item;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM items;");
        }
    }

?>
