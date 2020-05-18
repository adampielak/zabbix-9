<?php

require_once '../include/config.inc.php';
//require_once '../include/actions.inc.php';

include('config.php');

require_once 'lib/ZabbixApi.class.php';
use ZabbixApi\ZabbixApi;
$api = new ZabbixApi($zabURL.'api_jsonrpc.php', ''. $zabUser .'', ''. $zabPass .'');

$api->userLogout();

setcookie("zabgraphs_session", "", time() - 3600, "/");
header("location:index.php");

?>