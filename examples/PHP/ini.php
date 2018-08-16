<?php

/**
 * Příklad přihlášení a volání metody pomocí JSON a nativních PHP funkcí
 */
include_once './XMLRPC_JSON_clean.php';
$cc = new JsonSklik();
var_dump($cc->request());

/**
 * Příklad přihlášení a volání metody pomocí XML RPC a externí knihovny
 */
include_once './xmlrpc.inc';
include_once './XMLRPC_externalLib.php';
$cc = new XmlRpcSklik();
var_dump($cc->request());

/**
 * Příklad přihlášení a volání metody pomocí XML RPC a nativních PHP funkcí
 */
include_once './XMLRPC_clean.php';
$cc = new XmlRpcSklik();
var_dump($cc->request());

?>