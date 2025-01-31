<?php

function reverseToBase64($obj)
{
    // Rebuild the CustomerInfo object
    $CustomerInfo = [
        'data' => [
            'getCustomerInfo' => [
                'firstName' => $obj['firstName'],
                'lastName'  => $obj['lastName'],
                'idNumber'  => $obj['idNumber'],
            ],
        ],
    ];

    // Encode CustomerInfo to base64
    $encodedCustomerInfo = base64_encode(json_encode($CustomerInfo));

    // Rebuild the MyNumbers object
    $MyNumbers = [
        'data' => [
            'queryMyNumbers' => array_map(function ($number) {
                return [
                    'clear'  => $number['number'],
                    'status' => $number['status'],
                ];
            }, $obj['myNumbers']),
        ],
    ];

    // Encode MyNumbers to base64
    $encodedMyNumbers = base64_encode(json_encode($MyNumbers));

    // Rebuild the responseData object
    $responseData = [
        'GetCustomerInfo' => $encodedCustomerInfo,
        'QueryMyNumbers'  => $encodedMyNumbers,
    ];

    // Encode the final responseData to base64
    $finalBase64 = base64_encode(json_encode($responseData));

    return $finalBase64;
}


$obj = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'idNumber' => '32515522',
    'myNumbers' => [
        ['number' => '0722000000', 'status' => 'active'],
        ['number' => '0733000000', 'status' => 'inactive'],
    ],
];

$base64Data = reverseToBase64($obj);
echo $base64Data;
