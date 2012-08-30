SimpleHTTPClient
==========

A simple abstraction layer for making HTTP requests via PHP.

## Usage

Let's begin with a basic example.  

```php
$client = new SimpleHTTPClient();
$response = $client->makeRequest('http://example.com', 'GET');
```

The `SimpleHTTPClient` class exposes a public method named `makeRequest` which takes a URL, an HTTP request method, an HTTP request header string (optional), and an HTTP request body string (optional).  The method returns an associative array containing the HTTP response status, header, and body.  The example above initializes an instance of `SimpleHTTPClient` and makes a basic HTTP GET request, storing the response in `$response`.

The basic response is structured like this:

```
Array (
    [status] => Array (
            [protocolVersion] => 1.1
            [statusCode] => 200
            [reasonPhrase] => OK
        )

    [header] => Array (
            [Content-type] => text/plain
            [Transfer-Encoding] => chunked
        )

    [body] => <html><head><title>Example Page</title></head><body>An example page body</body></html>
)
```
