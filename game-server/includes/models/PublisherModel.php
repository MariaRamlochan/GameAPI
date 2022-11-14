<?php

class PublisherModel extends BaseModel {

    /**
     * A model class for the `publisher` database table.
     * It exposes operations that can be performed on publishers records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all publishers from the `publisher` table.
     * @return array A list of publishers. 
     */
    public function getAll() {
        $sql = "SELECT * FROM publisher";
        $data = $this->rows($sql);
        return $data;
    }

        /**
     * Get a list of Publishers whose title matches or contains the provided value.       
     * @param string $title 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($title) {
        $sql = "SELECT * FROM publisher WHERE Title LIKE :title";
        $data = $this->run($sql, [":title" => $title . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an Pubvlishers by its id.
     * @param int $publisher_id the id of the publisher.
     * @return array an array containing information about a given publisher.
     */
    public function getPublisherById($publisher_id) {
        $sql = "SELECT * FROM publisher WHERE PublisherId = ?";
        $data = $this->run($sql, [$publisher_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of publishers of a given developer.
     * @param int $publisher_id the id of the developer.
     * @return array a list of publishers.
     */
    public function getPublisherByDeveloperId($publisher_id) {
        $sql = "SELECT * FROM publisher WHERE DeveloperID = ?";
        $data = $this->run($sql, [$publisher_id])->fetchAll();
        return $data;
    }

    /**
     * Retrieve a list of publishers of a given publisher.
     * @param int $publisher_id the id of the publisher.
     * @return array a list of publishers.
     */
    public function getPublisherByGameId($publisher_id) {
        $sql = "SELECT * FROM publisher WHERE GameID = ?";
        $data = $this->run($sql, [$publisher_id])->fetchAll();
        return $data;
    }
}
