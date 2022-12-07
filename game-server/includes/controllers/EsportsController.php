<?php

use GuzzleHttp\Client;

/**
 * A class for consuming the Ice and Fire API.
 *
 * @author Maria, Nafees
 */
class EsportsController extends WebServiceInvoker
{

    private $request_options = array(
        'headers' => array('Accept' => 'application/json')
    );

    public function __construct()
    {
        parent::__construct($this->request_options);
    }

    /**
     * Fetches and parses a list of esports from the Ice and Fire API.
     * 
     * @return array containing some information about esports. 
     */
    function getEsportsInfo()
    {
        $esports = array();
        $resource_uri = "https://api.opendota.com/api/teams";
        $esportsData = $this->invoke($resource_uri);

        if (!empty($esportsData)) {
            // Parse the fetched list of books.   
            $esportsData = json_decode($esportsData, true);
            //var_dump($booksData);exit;

            $index = 0;
            // Parse the list of books and retreive some  
            // of the contained information.
            foreach ($esportsData as $key => $esport) {
                $esports[$index]["name"] = $esport["name"];
                $esports[$index]["rating"] = $esport["rating"];
                $esports[$index]["wins"] = $esport["wins"];
                $esports[$index]["losses"] = $esport["losses"];
                // $esports[$index]["country"] = $esport["country"];
                // $esports[$index]["released"] = $esport["released"];
                //
                $index++;
            }
        }
        return $esports;
    }
}
