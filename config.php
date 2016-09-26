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
        "kenzo" => [
            "url"=>"https://www.kenzo.com"
        ],
        "damart" => [
            "url"=>"http://www.damart.fr",
            "cookie"=>["GUEST_LANGUAGE_ID"=>"fr_FR"]
        ],
        "disneystore" => [
            "url"=>"http://www.disneystore.com"
        ],
    ]
];
?>
