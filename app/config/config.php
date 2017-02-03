<?php

$settings = [
    "app" => [
        'domain'=>'http://phalcon.loc/',
        'modelsDir'=>"../app/models/"
    ],
    'mongodb'=>[
        'db'=>'rest',
        'server'=>'mongodb://localhost:27017',
        'options'=>[
            'connect' => true
        ],
        'driver_options'=>[]
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