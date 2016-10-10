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
            "url"=>"http://www.ctshirts.com",
            //"proxy" => "62.252.196.194:3128",
            "proxies" => [


                "82.145.208.37:80",
                "80.249.102.46:80",
                "82.145.209.242:80",
                "82.145.208.20:80",
                "178.62.2.78:3128",
                "81.139.247.236:80",
                "212.46.131.164:80",
                "213.177.255.60:80",
                "195.50.71.240:80",
                "195.62.28.38:80",
                "195.50.71.239:80"
            ],

            "cookie" =>[
                "CTCountry"=>"gb",
                "GlobalE_CT_Data"=>'{"CUID":"45091cb2-b06f-484e-baf7-788ed874145d"}',
                "GlobalE_CT_Tracked"=>'{"SESID":150891858,"AllowFullTracking":true}',
                "GlobalE_Data"=>'{"countryISO":"RU","cultureCode":"ru","currencyCode":"RUB","apiVersion":"2.1.4","clientSettings":"{"AllowClientTracking":{"Value":"true"},"FullClientTracking":{"Value":"true"},"IsMonitoringMerchant":{"Value":"true"},"IsV2Checkout":{"Value":"true"}}"}'
            ],
            "filters" => [
                "/£(\d+\.\d+)/" => ""
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
