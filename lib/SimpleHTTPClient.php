<?php
/**
 * Class for simplifying HTTP requests by wrapping cURL
 * workflow.  The goal is to provide simpler, more direct
 * control over HTTP requests and responses.
 */
class SimpleHTTPClient {
    // HTTP response status; will contain associative array
    // representing the HTTP version, status code, and
    // reason phrase.
    private $responseStatus = null;

    // HTTP response header; will contain associative array
    // of header attributes returned from the cURL request.
    private $responseHeader = null;

    // HTTP response body; will contain string representing
    // the body of the response returned from the cURL
    // request.
    private $responseBody = null;

    // List of valid HTTP methods.
    // TODO: Perhaps we should set required structure for
    //       each of the HTTP methods for validation of the
    //       incoming request.
    private $httpMethods = array(
        'OPTIONS' => true,
        'GET' => true,
        'HEAD' => true,
        'POST' => true,
        'PUT' => true,
        'DELETE' => true,
        'TRACE' => true,
        'CONNECT' => true,
    );

    /**
     * Make an HTTP request.  Defaults to a simple GET
     * request if only the $url parameter is specified.
     * Returns the complete response header and body in a
     * PHP-friendly data structure.
     * 
     * @param String $url: A complete URL including URL parameters.
     * @param String $requestMethod: The HTTP request method to use for this request.
     * @param String $requestBody: The striing literal containing request body data (eg. POST params go here).
     * @return Array: Array containing response header and body as 'header' and 'body' keys.
     */
    public function makeRequest(
        $url,
        $requestMethod = 'GET',
        $requestBody = null,
        $requestHeader = null) {

        try {
            // Make request and return response.
            return $this->httpRequest(array(
                'request' => array(
                    'method' => $requestMethod,
                    'url' => $url,
                ),
                'header' => $requestHeader,
                'body' => $requestBody,
            ));
        } catch (Exception $e) {
            // Caught exception; return error message.
            return array('error' => $e->getMessage());
        }
    }

    /**
     * 
     */
    public function httpRequest($request) {
        // Validate request details.
        if (!isset($request['request'])
            || !isset($request['request']['method'])
            || !isset($request['request']['url'])) {

            throw new Exception('HTTP request not fully defined.');
        }

        // TODO: Support DELETE and PUT and POST and GET requests
        //       as well as other HTTP request methods defined in
        //       the HTTP standard.
        // 
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html
        if (!isset($this->httpMethods[$request['request']['method']])) {
            throw new Exception('Unsupported HTTP method specified.');
        }

        // Reinitialize response header and body.
        $this->responseHeader = null;
        $this->responseBody = null;

        // Set up cURL handler.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $request['request']['url']);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'handleResponseHeader'));
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'handleResponseBody'));
        if ($request['header'] !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request['header']);
        }

        // TODO: does CURLOPT_CUSTOMREQUEST work for all HTTP methods?
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request['request']['method']);

        if (isset($request['body'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request['body']);
        }

        // Execute the request and close the connection
        // handler as quickly as possible, recording how
        // long the request takes.
        $timeStart = microtime(true);
        curl_exec($ch);
        curl_close($ch);
        $timeDelta = microtime(true) - $timeStart;

        return array(
            'status' => $this->responseStatus,
            'header' => $this->responseHeader,
            'body' => $this->responseBody,
            'time' => $timeDelta,
        );
    }

    /**
     * Process an incoming response header following a
     * cURL request and store the header in
     * $this->responseHeader.
     * 
     * @param Object $ch: The cURL handler instance.
     * @param String $headerData: The header to handle; expects header to come in one line at a time.
     * @return Int: The length of the input data.
     */
    private function handleResponseHeader($ch, $headerData) {
        // If we haven't found the HTTP status yet, then try to match it.
        if ($this->responseStatus == null) {
            $regex = '/^\s*HTTP\s*\/\s*(?P<protocolVersion>\d*\.\d*)\s*(?P<statusCode>\d*)\s(?P<reasonPhrase>.*)\r\n/';
            preg_match($regex , $headerData, $matches);

            foreach (array('protocolVersion', 'statusCode', 'reasonPhrase') as $part) {
                if (isset($matches[$part])) {
                    $this->responseStatus[$part] = $matches[$part];
                }
            }
        }

        // Digest HTTP header attributes.
        if (!isset($responseStatusMatches) || empty($responseStatusMatches)) {
            $regex = '/^\s*(?P<attributeName>[a-zA-Z0-9-]*):\s*(?P<attributeValue>.*)\r\n/';
            preg_match($regex, $headerData, $matches);

            if (isset($matches['attributeName'])) {
                $this->responseHeader[$matches['attributeName']] = isset($matches['attributeValue']) ? $matches['attributeValue'] : null;
            }
        }

        return strlen($headerData);
    }

    /**
     * Process an incoming response body following a cURL
     * request and store the body in $this->responseBody.
     * 
     * @param Object $ch: The cURL handler instance.
     * @param String $bodyData: The body data to handle.
     * @param Int: The length of the input data.
     */
    private function handleResponseBody($ch, $bodyData) {
        $this->responseBody .= $bodyData;

        return strlen($bodyData);
    }
}
