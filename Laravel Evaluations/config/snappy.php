<?php

return array(


    'pdf' => array(
        'enabled' => true,
        'binary' => '"'.env('SNAPPY_PDF').'"',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => true,
        'binary' => '"'.env('SNAPPY_IMG').'"',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
