<?php

class GameModel extends BaseModel {

    /**
     * A model class for the `game` database table.
     * It exposes operations that can be performed on games records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all games from the `game` table.
     * @return array A list of games. 
     */
    public function getAll() {
        $sql = "SELECT * FROM game";
        $data = $this->rows($sql);
        return $data;
    }

}
