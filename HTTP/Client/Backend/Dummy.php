<?php
/**
 * HTTP dummy backend
 *
 * Special http client backend for testing
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client_Backend_Dummy implements HTTP_Client_Backend_BackendInterface {
	/**
	 * An array of expected results
	 * @var string[] $expectedResults
	 */
	protected $expectedResults = array();

	/**
	 * An array of expected results
	 * @var HTTP_Client_Request[] $expectedResults
	 */
	protected $expectedRequests= array();

	/**
	 * The proxy url to use, or false
	 * @var string|bool $proxy
	 */
	protected $proxy = false;

	/**
	 * Add an expected result
	 *
	 * @param string              $responseText This should be a valid HTML response, with header
	 *                                          Otherwise a 200 OK header will be added
	 * @param HTTP_Client_Request $request      The expected request object
	 */
	public function addExpectation($responseText, HTTP_Client_Request $request) {
		$this->expectedRequests[] = $request;
		if(0 !== strpos($responseText, 'HTTP/1.')) {
			$responseHeader = "HTTP/1.1 200 OK\r\nServer: Apache/2.2.14 (Win32) DAV/2 mod_ssl/2.2.14 OpenSSL/0.9.8l mod_autoindex_color PHP/5.3.1 mod_apreq2-20090110/2.7.1 mod_perl/2.0.4 Perl/v5.10.1\r\nX-Powered-By: PHP/5.3.1\r\nContent-Length: ".sizeof($responseText)."\r\nKeep-Alive: timeout=5, max=100\r\nConnection: Keep-Alive\r\nContent-Type: text/html; charset=iso-8859-1\r\n\r\n";
			$responseText = $responseHeader.$responseText;
		}
		$this->expectedResults[] = $responseText;
		return $this;
	}

	/**
	 * Send a dummy request, and return expected result
	 * @param HTTP_Client_Request $request
	 *
	 * @throws HTTP_Client_ClientException
	 * @return string
	 */
	public function sendRequest(HTTP_Client_Request $request) {
		$expectedRequest = array_shift($this->expectedRequests);
		$response = array_shift($this->expectedResults);
		if(empty($response)) {
			throw new HTTP_Client_ClientException('No more stored response');
		}

		if($request != $expectedRequest) {
			$requestArray = (array)$request;
			$expectedRequestArray = (array)$expectedRequest;
			foreach($requestArray as $key => $value) {
				if (!array_key_exists($key, $expectedRequestArray) || ($expectedRequestArray[$key] !== $value)) {
					if (!array_key_exists($key, $expectedRequestArray)) {
						$key = trim($key, "\0A*");
						throw new HTTP_Client_ClientException("Key '$key' not found in expected result");
					}
					throw new HTTP_Client_ClientException(
						"\r\nExpected: '".trim($key, "\0A*")."' => '".print_r($expectedRequestArray[$key], true) . "'\r\n" .
						"Found:    '".trim($key, "\0A*")."' => '".print_r($value, true) . "'\r\n"
					);
					break;
				}
			}
			foreach($expectedRequestArray as $key => $value) {
				if (!array_key_exists($key, $requestArray)) {
					throw new HTTP_Client_ClientException("Expected key '$key' not found in actual result");
				}
			}

			throw new HTTP_Client_ClientException('Invalid request');
		}
		return $response;
	}

	/**
	 * Sets the proxy to use
	 * @param string $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

	/**
	 * Fetch $proxy
	 * @return the $proxy
	 */
	public function getProxy() {
		return $this->proxy;
	}

}