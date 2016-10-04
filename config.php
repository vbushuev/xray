<?php
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');

$env = [
    //"host" => "www.kik.de",
    "host" => "www.ctshirts.com",
    "cache" => "cache",
    "hosts" => [
        "baby-walz" => [
            "url"=>"http://www.baby-walz.fr"
        ],
        "ctshirts" => [
            "url"=>"http://www.ctshirts.com"
        ],
        "kenzo" => [
            "url"=>"https://www.kenzo.com"
        ],
        "damart" => [
            "url"=>"http://www.damart.fr"
        ],
        "disneystore" => [
            "url"=>"http://www.disneystore.com"
        ],
        "esprit" => [
            "url"=>"http://www.esprit.com",
            "cookie"=>[
                "UserData"=>"%7B%22salutation%22%3A%22%22%2C%22name%22%3A%22%22%2C%22id%22%3A%22%22%2C%22theme%22%3A%22default%22%2C%22geoTargeting%22%3A%22disabled%22%2C%22geoTargetingUrl%22%3A%22%22%2C%22cookieVersion%22%3A%221.0%22%2C%22wasLayerDisplay%22%3A0%7D",
                "geoDisabled"=>"1",
                "language"=>"fr"

            ]
        ],
    ]
];
?>
