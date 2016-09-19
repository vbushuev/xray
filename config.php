<?php
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');

$env = [
    //"host" => "www.kik.de",
    "host" => "www.baby-walz.fr",
    "cache" => "cache",

    "hosts" => [
        "baby-walz" => [
            "url"=>"http://www.baby-walz.fr"
        ],
        "ctshirts" => [
            "url"=>"http://ctshirts.com"
        ],
    "stores" => [
        "baby-walz" => [
            "host" => "www.baby-walz.fr"
        ],
        "ctshirts" => [
            "host" => "www.ctshirts.com"
        ],
        "yoox" => ["host" => "www.yoox.com"],
        "gymboree" => ["host" => "www.gymboree.com"],
        "crazy8" => ["host" => "www.crazy8.com"],
        "ralphlauren" => ["host" => "www.ralphlauren.com"],
        "6pm" => ["host" => "www.6pm.com"],
        "disneystore" => ["host" => "www.disneystore.com"],
        "vertbaudet" => ["host" => "www.vertbaudet.com"],
        "t-a-o" => ["host" => "www.t-a-o.com"],
        "zulily" => ["host" => "www.zulily.com"],
        "ernstings-family" => ["host" => "www.ernstings-family.de"],
    ]
];
?>
