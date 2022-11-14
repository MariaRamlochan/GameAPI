<?php

class AuthorModel extends BaseModel {

    /**
     * A model class for the `author` database table.
     * It exposes operations that can be performed on authors records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all authors from the `author` table.
     * @return array A list of authors. 
     */
    public function getAll() {
        $sql = "SELECT * FROM author";
        $data = $this->rows($sql);
        return $data;
    }

        /**
     * Get a list of Authors whose name matches or contains the provided value.       
     * @param string $name 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($name) {
        $sql = "SELECT * FROM author WHERE Title LIKE :name";
        $data = $this->run($sql, [":name" => $name . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an Authors by its id.
     * @param int $author_id the id of the author.
     * @return array an array containing information about a given author.
     */
    public function getAuthorsById($author_id) {
        $sql = "SELECT * FROM author WHERE author_id = ?";
        $data = $this->run($sql, [$author_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of authors from a given game.
     * @param int $game_id the id of the game.
     * @return array a list of authors.
     */
    public function getAuthorsByGameId($game_id) {
        $sql = "SELECT * FROM author WHERE GameID = ?";
        $data = $this->run($sql, [$author_id])->fetchAll();
        return $data;
    }
}