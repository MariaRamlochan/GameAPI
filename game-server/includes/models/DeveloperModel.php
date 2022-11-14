<?php

class DeveloperModel extends BaseModel {

    /**
     * A model class for the `developer` database table.
     * It exposes operations that can be performed on developers records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all developers from the `developer` table.
     * @return array A list of developers. 
     */
    public function getAll() {
        $sql = "SELECT * FROM developer";
        $data = $this->rows($sql);
        return $data;
    }

        /**
     * Get a list of Developers whose title matches or contains the provided value.       
     * @param string $title 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($title) {
        $sql = "SELECT * FROM developer WHERE Title LIKE :title";
        $data = $this->run($sql, [":title" => $title . "%"])->fetchAll();
        return $data;
    }

    /**
     * Retrieve an Pubvlishers by its id.
     * @param int $developer_id the id of the developer.
     * @return array an array containing information about a given developer.
     */
    public function getPublisherById($developer_id) {
        $sql = "SELECT * FROM developer WHERE PublisherId = ?";
        $data = $this->run($sql, [$developer_id])->fetch();
        return $data;
    }

    /**
     * Retrieve a list of developers of a given developer.
     * @param int $developer_id the id of the developer.
     * @return array a list of developers.
     */
    public function getPublisherByPublisherId($developer_id) {
        $sql = "SELECT * FROM developer WHERE PublisherID = ?";
        $data = $this->run($sql, [$developer_id])->fetchAll();
        return $data;
    }

    /**
     * Retrieve a list of developers of a given developer.
     * @param int $developer_id the id of the developer.
     * @return array a list of developers.
     */
    public function getDeveloperByGameId($developer_id) {
        $sql = "SELECT * FROM developer WHERE GameID = ?";
        $data = $this->run($sql, [$developer_id])->fetchAll();
        return $data;
    }
}
