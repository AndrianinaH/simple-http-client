SimpleHTTPClient
==========

A simple abstraction layer for making HTTP requests via PHP.

h2. Usage

Let's begin with a basic example.

    $client = new SimpleHTTPClient();
    $response = $client->makeRequest('http://example.com', 'GET');


