<?php
require_once("autoload.php");
date_default_timezone_set('Europe/Moscow');

$env = [
    //"host" => "www.kik.de",
    "host" => "www.ctshirts.com",
    "cache" => [
        "path"=>"cache",
        "use" => true
    ],
    "hosts" => [
        "baby-walz" => [
            "url"=>"https://www.baby-walz.fr",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ]
        ],
        "eduscho" => [
            "url"=>"https://www.eduscho.at",
            "js" => "tchibo.js",
            "template" => "tchibo.php",
            "css" => "tchibo.css",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ]
        ],
        "tchibo" => [
            "url"=>"http://www.eduscho.at",
            "cache"=>["use"=>false],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ]
        ],
        "ernstings-family" => [
            "url"=>"https://www.ernstings-family.at",
            "cache"=>["use"=>false],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ]
        ],
        "forever21" => [
            "url"=>"https://www.forever21.com",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ]
        ],
        "ctshirts" => [
            "url"=>"http://www.ctshirts.com",
            //"proxy" => "62.252.196.194:3128",
            "cookie" =>[
                "CTCountry"=>"gb",
                "GlobalE_Data"=>'{"countryISO":"gb","cultureCode":"ru","currencyCode":"GBP","apiVersion":"2.1.4","clientSettings":"{"AllowClientTracking":{"Value":"true"},"FullClientTracking":{"Value":"true"},"IsMonitoringMerchant":{"Value":"true"},"IsV2Checkout":{"Value":"true"}}"}'
            ],
            "filters" => [
                "/£(\d+\.\d+)/" => ""
            ]
        ],

        "zara" => [
            "url"=>"http://www.zara.com",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ],
            "cookie" =>[
                "storepath" => 'fr/en',
                "socControl" => 'http%3A%2F%2Fwww.zara.com/fr/en/'
            ]
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
