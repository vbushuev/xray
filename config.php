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
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"fr"
            ]
        ],
        "sportsdirect" => [
            "url"=>"http://www.sportsdirect.com",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"en"
            ]
        ],
        "c-and-a" => [
            "url"=>"http://www.c-and-a.com",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"de"
            ]
        ],
        "tchibo" => [
            "url"=>"https://www.eduscho.at",
            "cache"=>["use"=>false],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"de"
            ]
        ],
        "ernstings-family" => [
            "url"=>"https://www.ernstings-family.at",
            "cache"=>["use"=>true],
            "engine" =>[
                "restricted_headers" => ['Origin','Referer']
            ],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"de"
            ]
        ],
        "forever21" => [
            "url"=>"https://www.forever21.com",
            "engine" =>[
                "encode_cookie" => false
            ],
            //"cache"=>["use"=>false],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"en"
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
        ]
    ]
];
?>
