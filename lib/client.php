<?php
/**
 * In-progress experiment for making multiple asynchronous requests.
 */
class HTTPClient {
    // Keep track of an internal array of requests.
    private $requests = array();
    private $sockets = array();
    private $responses = array();

    // Default timeout for requests.
    private $defaultTimeout = 10;
    private $defaultReadSize = 8192;

    /**
     * 
     */
    public function makeRequest($request) {
        // Add the request to the internal array of requests.
        $this->requests[] = $request;
        $requestID = end(array_keys($this->requests));
        $this->responses[$requestID] = null;

        // TODO: Parse the $request array to get protocol, host, port, and path.
        //       Map defaults to port 80 for HTTP, port 443 for HTTPS, etc.
        $protocol = 'http';
        $host = 'www.google.com';
        $port = 80;
        $path = '/';

        // Make the request.
        $socket = stream_socket_client(
            "$host:$port",
            $errno,
            $errstr,
            $this->defaultTimeout,
            STREAM_CLIENT_CONNECT   // TODO: Try to make this async.
            //STREAM_CLIENT_ASYNC_CONNECT|STREAM_CLIENT_CONNECT
        );
        if ($socket) {
            $this->sockets[$requestID] = $socket;
            $requestString = "GET $path HTTP/1.1\r\n"
                . "Host: $host\r\n"
                . "\r\n";
            fwrite($socket, $requestString);
        } else {
            echo "Stream failed to open correctly.";
        }

        // Return ID for accessing the response.
        return $requestID;
    }

    /**
     * 
     */
    public function processSocketData() {
        if (count($this->sockets)
            && stream_select(
                $this->sockets,
                $w = null,
                $e = null,
                $this->defaultTimeout
            )) {

            // Read response data from sockets, appending data to
            // the respective string in the responses array.
            foreach ($this->sockets as $key => $socket) {
                echo "Processing socket: $key\n";   // TODO: remove this line
                $streamData = fread($socket, $this->defaultReadSize);
                if (empty($streamData)) {
                    fclose($socket);
                    unset($this->sockets[$key]);
                } else {
                    $this->responses[$key] .= $streamData;
                }
            }
        }
    }

    /**
     * 
     */
    public function isResponseComplete($requestID) {
        return (isset($this->requests[$requestID])
            && !isset($this->sockets[$requestID])
            && isset($this->responses[$requestID])
        );
    }

    /**
     * 
     */
    public function getResponseData($requestID) {
        return isset($this->responses[$requestID]) ? $this->responses[$requestID] : false;
    }
}

$client = new HTTPClient();
$requestID = $client->makeRequest(array(
    'request' => array(
        'method' => 'GET',
        'url' => 'http://www.google.com:80',
    ),
    'header' => array(
    ),
    'body' => array(
    ),
));

// Drive the client to process incoming socket data.
while (!$client->isResponseComplete($requestID)) {
    echo "Response for request $requestID is not complete:\n".$client->getResponseData($requestID)."\n\n";
    $client->processSocketData();
}

echo "Response for request $requestID is complete:\n".$client->getResponseData($requestID)."\n\n";
