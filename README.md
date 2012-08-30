SimpleHTTPClient
==========

A simple abstraction layer for making HTTP requests via PHP.

## Usage

Let's begin with a basic example.  

```php
$client = new SimpleHTTPClient();
$response = $client->makeRequest('http://example.com', 'GET');
```

The `SimpleHTTPClient` class exposes a public method named `makeRequest` which takes a URL, an HTTP request method, an HTTP request header string (optional), and an HTTP request body string (optional).  The method returns an associative array containing the HTTP response status, the HTTP response header, and the HTTP response body.  The example above initializes an instance of `SimpleHTTPClient` and makes a basic HTTP GET request, storing the response in `$response`.
