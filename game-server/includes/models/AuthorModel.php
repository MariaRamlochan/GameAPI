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
        //$data = $this->rows($sql);
        $data = $this->paginate($sql);
        return $data;
    }

        /**
     * Get a list of Authors whose name matches or contains the provided value.       
     * @param string $name 
     * @return array An array containing the matches found.
     */
    public function getWhereLike($name) {
        $sql = "SELECT * FROM author WHERE name LIKE :name";
        $data = $this->paginate($sql, [":name" => $name . "%"]);
        return $data;
    }

    /**
     * Retrieve an Authors by its id.
     * @param int $author_id the id of the author.
     * @return array an array containing information about a given author.
     */
    public function getAuthorById($author_id) {
        $sql = "SELECT * FROM author WHERE author_id = ?";
        $data = $this->run($sql, [$author_id])->fetch();
        return $data;
    }

    /**
     * Create a list of authors
     */
    public function createAuthors($data) {
        $data = $this->insert("author", $data);
        return $data;
    }

    /**
     * Update a list of authors
     */
    public function updateAuthors($data, $where) {
        $data = $this->update("author", $data, $where);
        return $data;
    }

    /**
     * Delete a list of authors
     */
    public function deleteAuthors($where) {
        $data = $this->delete("author", $where);
        return $data;
    }
}
