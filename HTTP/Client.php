<?php
/**
 * Http client
 *
 * Usage:
 *		$request = new HTTP_Client_Request('http://www.mittudomain.hu/ize.php');
 *		$params = array(
 *			'foo' => 'bar',
 *			'baz' => 'dough',
 *		);
 *		$request->addParams($params);
 *		$httpclient = new HTTP_Client();
 *		$response = $httpclient->sendRequest($request);
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client {
	/**
	 * @var HTTP_BackendInterface $backend
	 */
	protected $backend = null;
	protected $proxy = false;

	/**
	 * @param string $backend Name of some HTTP_BackendInterface If null,
	 *                        HTTP_BackendCurl will be used
	 */
	public function __construct($backend = null) {

		if (null === $backend) {
			$this->backend = new HTTP_Client_Backend_Curl();
		} else {
			if (class_exists($backend)) {
				$backendObject = new $backend();
				if (is_a($backendObject, 'HTTP_Client_Backend_BackendInterface')) {
					$this->backend = new $backend();
				}
			}
		}
		if (null === $this->backend) {
			throw new HTTP_Client_ClientException('Invalid http client backend: '.$backend);
		}

		$this->setProxy(false);
	}

	/**
	 * Set a proxy for backend
	 * @param string $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
		return $this;
	}

	/**
	 * Get the backend object
	 *
	 * @return HTTP_Client_BackendInterface
	 */
	public function getBackend() {
		return $this->backend;
	}

	/**
	 * Send a HTTP request
	 *
	 * @param HTTP_Client_Request $request
	 *
	 * @return HTTP_Client_Response;
	 */
	public function sendRequest(HTTP_Client_Request $request, $useProxy = false) {
		$proxy = false;
		if ($useProxy) {
			$this->backend->setProxy($this->proxy);
			$proxy = $this->proxy;
		} else {
			$this->backend->setProxy(false);
		}
		$rawResponse = $this->backend->sendRequest($request);
		$response = $this->processResponse($rawResponse);
		return $response;
	}

	/**
	 * Process raw response text from backend
	 *
	 * @param string $rawResponseText
	 *
	 * @return HTTP_Client_Response
	 */
	public function processResponse($rawResponseText) {
		$result = null;
		//$result gets set here
		preg_match('%
			^(?P<version>[^\s]+)\s(?P<statusCode>\d{3})\s(?P<statusText>[^\r\n]*)\r\n
			(?:(?P<headers>(?:.*?)\r\n)\r\n)	# headers and an empty line
			(?P<responseText>.*)$	# response text
			%msx', $rawResponseText, $result
		);
		$rawheaders = explode("\r\n", $result['headers']);
		$headers = array();
		foreach ($rawheaders as $header) {
			$header = explode(':', $header, 2);
			foreach ($header as $key => $value) {
				$header[$key] = trim($value);
			}
			if (array_key_exists(1, $header)) {
				if (isset($headers[$header[0]])) {
					if (!is_array($headers[$header[0]])) {
						$headers[$header[0]] = array($headers[$header[0]], $header[1]);
					} else {
						$headers[$header[0]][] = $header[1];
					}
				} else {
					$headers[$header[0]] = $header[1];
				}
			}
		}

		$response = new HTTP_Client_Response((int)$result['statusCode']);
		foreach ($headers as $header => $value) {
			$response -> setResponseHeader($header, $value);
		}

		$response -> setResponseText($result['responseText']);
		return $response;
	}
}
