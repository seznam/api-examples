<?php

/**
 * API SSP
 * Čisté volání API SSP přes JSON pomocí nativních metod PHP
 */
class JsonSklik {

    /**
     * Session - Po přihlášení je vyžadované u každého volání pro autentizaci
     * @var string 
     */
    protected $session = '';
    
    /**
     * URL pro volání dotazu - API SSP
     * @var string 
     */
    protected $url = 'https://api.sklik.cz/ssp/json/';
        
    /**
     * Přihlášení uživatele pomocí uživatelského jména a hesla nebo pomocí tokenu
     * @see https://api.sklik.cz/ssp/login.html
     * @param boolean $token - rozhoduje jestli se bude uživatel přihlašovat pomocí tokenu
     */
    public function login($token = false) {
        $ses = array();
        
        if($token) {
            $request = json_encode(
                array('token'=>!!!'token')
            );
            $response = $this->call($this->url.'login/token', $request);        
        } else {
            $request = json_encode(
                array('username'=>!!!'email', 'password'=>!!!'heslo')
            );
            $response = $this->call($this->url.'login', $request);
        }

        $response = json_decode($response);
        if(isset($response->session)) {
            $this->session = $response->session;            
        }
    }
    /**
     * Volání metody.
     * @see https://api.sklik.cz/ssp/webs_list.html 
     * @return mixed
     */
    public function request() {
        $this->login();
        $method = '/webs/stats';
        $request = json_encode(
            array(
                'session' => $this->session,                 
                'dateFrom' => '2019-01-01', 
                'dateTo' => '2019-02-01',
                //'granularity' => 'yearly', //Optional
                //'byDevice' => False, //Optional
                //'bySource'=> False, //Optional
                //'allowEmptyStatistics' => True, //Optional
                //'webIds'=> [] //Optional    
            )
        );        
        $response = $this->call($this->url.$method, $request);
        return json_decode($response);
    }
    
    /**
     * Zavolání XML dotazu
     * @param string $url - kompletní adres
     * @param string $request - XML dotaz (ve formátu XML-RCP)
     * @see https://cs.wikipedia.org/wiki/XML-RPC#T%C4%9Blo
     * @return string
     */
    protected function call($url, $request) {
        $header[] = "Content-type: application/json";
        $header[] = "Content-length: " . strlen($request);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        $data = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        } else {
            curl_close($ch);
            return $data;
        }
    }
}
$test = new JsonSklik();
var_dump($test->request());
?>