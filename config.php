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
            "engine" =>[
                //"restricted_headers" => ['Origin','Referer']
            ],
            "cache"=>["use"=>false],
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
            "cache"=>["use"=>false],
            "engine" =>[
                //"restricted_headers" => ['Origin','Referer']
            ],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"de"
            ]
        ],
        "forever21" => [
            "url"=>"http://www.forever21.com",
            "engine" =>[
                "encode_cookie" => false
            ],
            //"cache"=>["use"=>false],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"en"
            ]
        ],
        "brandalley" => [
            "url"=>"https://www-v6.brandalley.fr",
            //"cache"=>["use"=>false],
            "engine" =>[
                //"restricted_headers" => ['Origin','Referer']
                "encode_cookie" => true,
                "client_cookie" => [
                    "use"=>true
                ]
            ],
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки"
            ],
            "cookie"=>[
                //"AccessPrivateSale" => 'a:2:{i:0;s:31:"s:23:"xrayshopping@garan24.ru";";i:1;s:32:"9b43d56c65b0fe67996a0543e80c1b79";}'
            ]
        ],
        "geox" => [
            "url"=>"http://www.geox.com",
            "site"=>[
                "title"=>"GauzyMALL - удобные покупки",
                "lang"=>"en"
            ],
            //"cache"=>["use"=>true],
            "engine" =>[
                "client_cookie" => [
                    "use"=>true
                ]
                //"encode_cookie" => false,
            /*    "client_cookie" => [
                    "use"=>false,
                    "list"=>[]
                ],
            */
                //"restricted_headers" => ['Origin','Referer','User-Agent','Content-Type']
            ],
            "cookie"=>[
                "preferredCountry"=>"AT",
                "preferredLanguage"=>"EN",
                "countrySelected"=>"true"
            ]
        ],
        "ctshirts" => [
            "url"=>"http://www.ctshirts.com",
            //"proxy" => "62.252.196.194:3128",
            "cache"=>["use"=>true],
            "cookie" =>[
                "CTCountry"=>"gb",
                "GlobalE_Data"=>'{"countryISO":"gb","cultureCode":"ru","currencyCode":"GBP","apiVersion":"2.1.4","clientSettings":"{"AllowClientTracking":{"Value":"true"},"FullClientTracking":{"Value":"true"},"IsMonitoringMerchant":{"Value":"true"},"IsV2Checkout":{"Value":"true"}}"}'
            ]
        ],
        "tmlewin" => [
            "url"=>"https://www.tmlewin.co.uk",
            "restricted_headers" => ['Origin','Referer']
        ],
        "test" => [
            "url"=>"http://gm.bs2",
            "template"=>"forever21.php"
        ]
    ]
];
?>
