SimpleHTTPClient
========

Make simple HTTP requests from PHP.


## Usage

### Simple GET
A basic example.  

```php
$client = new SimpleHTTPClient();
$response = $client->makeRequest('http://example.com', 'GET');
```

The `SimpleHTTPClient` class exposes a public method named `makeRequest` which takes a URL, an HTTP request method, a request body string (optional), and a request header string (optional).  The method returns an associative array containing the HTTP response status, header, and body.  The example above initializes an instance of `SimpleHTTPClient` and makes a basic HTTP GET request, storing the response in `$response`.

The response array is structured like this:

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

To determine whether a request returned a successful response or to handle different HTTP error codes simply test the value of `$response['status']['statusCode']`.  To understand the intended content type of the body simply test the value of `$response['header']['Content-type']`.  To work with the response body simply use `$response['body']`.


### Simple POST

Making POST requests is trivial too.

```php
$response = $client->makeRequest('http://example.com', 'POST', 'foo=bar&bah=bat&blue=yellow');
```

The example above shows that we can pass the POST body as a string.  We can also pass an associative array of parameters with a POST request.

```php
$postData = array(
    'foo' => 'bar',
    'bah' => 'bat',
    'blue' => 'yellow',
);

$response = $client->makeRequest('http://example.com', 'POST', $postData);
```


### Specifying request header attributes

An array of request header attributes can be passed into `makeRequest`.

```php
$requestHeader = array(
    'Content-type: application/json',
    'Accept: application/json',
);
$postData = array(
    'foo' => 'bar',
    'bah' => 'bat',
    'blue' => 'yellow',
);

$response = $client->makeRequest('http://example.com', 'POST', json_encode($postData), $requestHeader);
```

To define header attributes on a request with an empty request body simply pass `null` for the third parameter to `makeRequest`.

```php
$requestHeader = array(
    'Accept: application/json',
);

$response = $client->makeRequest('http://example.com', 'GET', null, $requestHeader);
```

License
========

This repository is made available to the public as open source software under the [MIT License](http://opensource.org/licenses/MIT).
