<?php
header("Content-Type: application/json;charset=utf-8");
include_once('../../../../wp-load.php');
define("plugin_version",'1.0.2');

$enable  = get_option('qualpro_plugin_enable') == "1";
$data = [
        "code" => 0,
        "msg" => "pong",
        "data" => [
            "name" => "QualPro For WordPress",
            "enable" => $enable,
    	    "version" => constant("plugin_version")
    	    ]
    ];
echo json_encode($data);