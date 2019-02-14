<?php

/**
 * API DRAK JSON
 * Čisté volání API DRAK přes JSON pomocí nativních metod PHP
 */
class JsonSklik {

    /**
     * Session - Po přihlášení je vyžadované u každého volání pro autentizaci
     * @var string 
     */
    protected $session = '';
    
    /**
     * URL pro volání dotazu - API verze DRAK
     * @var string 
     */
    protected $url = 'https://api.sklik.cz/drak/json/';
    
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
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
    
    /**
     * Přihlášení uživatele pomocí uživatelského jména a hesla
     */
    public function login() {
        $ses = array();
        $request = json_encode(
            array('token'!!!)
        );
        $response = $this->call($this->url.'/client.loginByToken', $request);        
        $response = json_decode($response);
        if(isset($response->session)) {
            $this->session = $response->session;            
        }
    }
    /**
     * Volání metody. 
     * @return mixed
     */
    public function request() {
        $this->login();
        $method = 'client.stats';
        $request = json_encode(
            array(
                array('session'=>$this->session),
                array(
                    'dateFrom' => '2017-02-20',
                    'dateTo' => '2017-02-22',
                    'granularity' => 'total'
                )
            )
        );        
        $response = $this->call($this->url.'/'.$method, $request);
        return json_decode($response);
    }
}
$test = new JsonSklik();
echo $test->request();
?>