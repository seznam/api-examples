<?php

/**
 * API DRAK XML-RPC
 * Čisté volání API DRAK přes XML-RPC pomocí nativních metod PHP
 * !!Řetězec požadavku, musí začínat <?xml version=\"1.0\" ?> a nesmí tomu předcházet mezery 
 */
class XmlRpcSklik {

    /**
     * Session - Po přihlášení je vyžadované u každého volání pro autentizaci
     * @var string 
     */
    protected $session = '';
    
    /**
     * URL pro volání dotazu - API verze DRAK
     * @var string 
     */
    protected $url = 'https://api.sklik.cz/drak/RPC2';
    
    /**
     * Zavolání XML dotazu
     * @param string $url - kompletní adres
     * @param string $request - XML dotaz (ve formátu XML-RCP)
     * @see https://cs.wikipedia.org/wiki/XML-RPC#T%C4%9Blo
     * @return string
     */
    protected function call($url, $request) {
        $header[] = "Content-type: text/xml";
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
        $request = "<?xml version=\"1.0\"?>
            <methodCall>
                <methodName>client.loginByToken</methodName>
                <params>
                    <param>
                        <string>token"!!!"</string>
                    </param>
                </params>
            </methodCall>";
        $response = $this->call($this->url, $request);
        $xml = new XMLReader();
        $xml->xml($response);
        while ($xml->read()) {
            if ($xml->name === 'member') {
                if (preg_match('/session\s+[^, ]*/', $xml->readString(), $ses) == 1) {
                    $this->session = substr(trim($ses[0]),8); 
                    break;
                }
            } 
        }
    }
    /**
     * Volání metody. 
     * @return string
     */
    public function request() {
        $this->login();
        $request = "<?xml version=\"1.0\" ?>
            <methodCall>
                <methodName>client.stats</methodName>
                <params>
                    <param>
                        <value>
                            <struct>
                                <member>
                                    <name>session</name>
                                    <value>
                                        <string>".$this->session."</string>
                                    </value>
                                </member>
                            </struct>
                        </value>
                    </param>
                    <param>
                        <value>
                            <struct>
                                <member>
                                    <name>dateFrom</name>
                                    <value>
                                        <dateTime.iso8601>20170529T16:00:00</dateTime.iso8601>
                                    </value>
                                </member>
                                <member>
                                    <name>dateTo</name>
                                    <value>
                                        <dateTime.iso8601>20171229T16:00:00</dateTime.iso8601>
                                    </value>
                                </member>
                                <member>
                                    <name>granularity</name>
                                    <value>
                                        <string>total</string>
                                    </value>
                                </member>
                                <member>
                                    <name>includeFulltext</name>
                                    <value>
                                        <boolen>TRUE</boolen>
                                    </value>
                                </member>
                            </struct>
                        </value>
                    </param>
                </params>
            </methodCall>";
        $response = $this->call($this->url, $request);
        return $response;
    }
}

?>