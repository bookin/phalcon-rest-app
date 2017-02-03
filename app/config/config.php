<?php

$settings = [
    "app" => [
        "modelsDir"      => "../app/models/"
    ],
    "rest"=>[
        'format'=>'json',
        'modules'=>[
            [
                'namespaces'=>'v1',
                'path'=>MODULES_PATH.DIRECTORY_SEPARATOR.'v1',
                'enable'=>true
            ]
        ]
    ]

];

return $settings;