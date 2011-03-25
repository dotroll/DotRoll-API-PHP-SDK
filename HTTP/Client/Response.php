<?php
/**
 * A HTTP client response object
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client_Response {

	/**
	 * Response status groups
	 * @var unknown_type STATUS_INFORMATIONAL
	 */
	const STATUS_INFORMATIONAL = 1;
	const STATUS_SUCCESS       = 2;
	const STATUS_REDIRECTION   = 3;
	const STATUS_CLIENTERROR   = 4;
	const STATUS_SERVERERROR   = 5;

	/**
	 * @var string HTTP_VERSION
	 */
	protected $version = 'HTTP/1.1';

	/**
	 * Response status code
	 * @var int $statusCode
	 */
	protected $statusCode = 501;

	/**
	 * @var int $state Request state
	 */
	protected $state = self::STATUS_SERVERERROR;

	/**
	 * @var array $responseHeaders
	 */
	protected $responseHeaders = array();

	/**
	 * @var string $responseText
	 */
	protected $responseText = null;

	/**
	 * Construc http response
	 * @param $statusCode
	 */
	public function __construct($statusCode) {
		$this->statusCode = (int)$statusCode;
		$state = intval($statusCode / 100);
		if($state >=0 && $state <= 5) {
			$this->state = $state;
		} else {
			$this->state = self::STATUS_SERVERERROR;
			$this->statusCode = 500;
		}
	}

	public function setResponseHeader($header, $value) {
		$this->responseHeaders[$header] = $value;
	}

	public function getResponseHeaders() {
		return $this->responseHeaders;
	}

	/**
	 * Set response text
	 * @param string $text
	 * @return HTTP_Client_Request
	 */
	public function setResponseText($text) {
		$this->responseText = (string)$text;
		return $this;
	}

	/**
	 * Get the status code
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * Get the current state
	 * @return int
	 */
	public function getState() {
		return $this->state;
	}
	/**
	 * Fetch $responseText
	 * @return the $responseText
	 */
	public function getResponseText() {
		return $this->responseText;
	}

}