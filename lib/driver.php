<?php
/**
 *
 */

// Load client.
require_once 'SimpleHTTPClient.php';
$client = new SimpleHTTPClient();

// Make request.
$request = array(
    'request' => array(
        'method' => 'GET',
        'url' => 'http://localhost:8000',
    ),
    'header' => array(
        'Accept' => 'application/json',
    ),
);
$response = $client->httpRequest($request);

// Process response.
echo "request:\n"; print_r($request); echo "\n\n";
echo "response:\n"; print_r($response); echo "\n\n";



// Show that makeRequest() catches exceptions.
$response = $client->makeRequest('http://localhost:8000', 'SILLY');
echo "response:\n"; print_r($response); echo "\n\n";
