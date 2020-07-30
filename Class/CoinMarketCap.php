<?php
/**
 * Created by PhpStorm.
 * User: Шевченко Максим
 * Date: 27.07.2020
 * Time: 15:12
 */

class CoinMarketCap
{
    public $parameters = [];

    public $url = false;

    protected $APIDomain = 'https://pro-api.coinmarketcap.com';

    public $mapConvertDB = 'cmc_map_convert';

    public $convertDB = 'cmc_convert';

    protected $APIKey = 'b0beaf39-f06a-4c43-a74c-6e3d3a2d3373';

    /**
     * Returning the table name with a prefix
     * @param string $tableName
     * @return string
     */
    public static function returnPrefixtable($tableName = 'map_convert')
    {
        global $wpdb;
        $tname = $wpdb->get_blog_prefix() . $tableName;
        return $tname;
    }

    /**
     * receiving data for the conversion form, you need to withdraw currencies
     * @param int $limit
     * @return object stdClass
     */
    public function getMapConvert($limit = 1000)
    {
        global $wpdb;

        $db_name = self::returnPrefixtable($this->mapConvertDB);
        $count = $wpdb->get_col("SELECT COUNT(*) as count FROM {$db_name}");

        if($count[0]){
            $results = $wpdb->get_row("SELECT * FROM {$db_name} ");
            $results->json = json_decode($results->json);

        }else{
            $this->url = '/v1/cryptocurrency/map';
            $this->parameters = [
                'start' => '1',
                'limit' => $limit,
            ];

            $results = $this->getDataApi();
            $this->setDBMapConvert($results);
        }
        return $results;
    }

    /**
     * API data retrieval method
     * @return bool|mixed
     */
    protected function getDataApi()
    {
        if(!$this->url) return false;

        $url = $this->APIDomain . $this->url;
        $parameters = $this->parameters;

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: '.$this->APIKey
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL

        $curl = curl_init(); // Get cURL resource

        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        curl_close($curl); // Close request

        return json_decode($response);
    }

    /**
     * Writing data to the database
     * @param $data
     * @return bool|int
     */
    protected function setDBMapConvert($data)
    {
        if(!$data) return false;

        global $wpdb;
        $db_name = self::returnPrefixtable($this->mapConvertDB);
        $wpdb->query( "DELETE FROM {$db_name} ");

        $time = current_time('mysql');
        $json = json_encode($data->data);
        $sql = "INSERT INTO {$db_name} (created_at, json) VALUES ('{$time}', '{$json}'); ";
        $result = $wpdb->query($sql);

        return $result;
    }

    /**
     * Receiving data for conversion
     * @param $symbol
     * @return bool
     */
    public function getConvertedData($symbol)
    {
        if(!$symbol) return false;
        $this->url = '/v1/tools/price-conversion';
        $this->parameters = [
            'amount' => $symbol['amount'],
            'symbol' => (string)$symbol['symbol'],
            'convert' => (string)$symbol['convert'],
        ];

        $symbol_group = "{$this->parameters['symbol']}|{$this->parameters['convert']}";
        $results = $this->getCovertDB($symbol_group);
        $time_data = strtotime($results->created_at) + 5 * 60;
        $time_nuw = strtotime(current_time('mysql'));

        if(empty($results) || $time_data < $time_nuw){
            $results = $this->getDataApi();
            $this->setCovertBD($results,$symbol_group);
        }
        return $results;
    }

    /**
     * Writing an API request to the database
     * @param $data data received from API
     * @param $symbol_group currency symbols link
     * @return bool|int
     */
    protected function setCovertBD($data,$symbol_group)
    {
        if(empty($data)) return false;
        global $wpdb;
        $db_name = self::returnPrefixtable($this->convertDB);
        $time = current_time('mysql');
        $json = json_encode($data->data);
        $sql = "INSERT INTO {$db_name} (created_at,symbol_group, data) VALUES ('{$time}', '{$symbol_group}','{$json}'); ";

        $result = $wpdb->query($sql);

        return $result;

    }

    /**
     * obtaining data from the database for a bunch of currency symbols, you need to understand whether to make a request or not using the API
     * @param $symbol_group
     * @return array|bool|object|void|null
     */
    protected function getCovertDB($symbol_group)
    {
        if(!$symbol_group) return false;
        global $wpdb;
        $db_name = self::returnPrefixtable($this->convertDB);
        $sql = "SELECT *  FROM {$db_name} 
                    WHERE symbol_group = '{$symbol_group}' 
                    ORDER BY id DESC LIMIT 1";

        $results = $wpdb->get_row($sql);
        if(!empty($results->data))
            $results->data = json_decode($results->data);

        return $results;
    }

    /**
     * getting the latest data $limit from the database
     * @param int $limit number of last displayed records
     * @return array
     */
    public function getLastCover($limit = 5)
    {
        global $wpdb;
        $db_name = self::returnPrefixtable($this->convertDB);
        $sql = "SELECT *  FROM {$db_name} 
                    ORDER BY id DESC LIMIT {$limit}";
        $results = $wpdb->get_results($sql);
        if(!empty($results)){
            $array = [];
            foreach ($results as $result){
                $array[] = [
                    'id' => $result->id,
                    'created_at' => $result->created_at,
                    'symbol_group' => $result->symbol_group,
                    'data' => json_decode($result->data),
                ];
            }
        }
        return $array;
    }

}