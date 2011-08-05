<?php
/**
 * A HTTP client request object
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client_Request {
	/**
	 * @var string HTTP_VERSION
	 */
	const HTTP_VERSION = 'HTTP/1.1';

	/**
	 * Request methods
	 * @var int METHOD_POST    Post method
	 * @var int METHOD_GET     Get method
	 * @var int METHOD_PUT     Put method
	 * @var int METHOD_DELETE  Delete method
	 * Currently unimplemented request method types:
	 * @var int METHOD_OPTIONS Options method
	 * @var int METHOD_HEAD    Head method
	 * @var int METHOD_TRACE   Trace method
	 * @var int METHOD_CONNECT Connect method
	 */
	const METHOD_POST    = 'POST';
	const METHOD_GET     = 'GET';
	const METHOD_OPTIONS = 'OPTIONS';
	const METHOD_HEAD    = 'HEAD';
	const METHOD_PUT     = 'PUT';
	const METHOD_DELETE  = 'DELETE';
	const METHOD_TRACE   = 'TRACE';
	const METHOD_CONNECT = 'CONNECT';

	/**
	 * Request resource identifier
	 * @var string $uri
	 */
	protected $uri;

	/**
	 * The port to curl to
	 * @var int $port
	 */
	protected $port;

	/**
	 * HTTP headers (key => value)
	 * @var array $headers
	 */
	protected $headers = array();

	/**
	 * User name for http auth
	 * @var string $userName
	 */
	protected $userName = null;

	/**
	 * User password for http auth
	 * @var string $userPasswd
	 */
	protected $userPasswd = null;

	/**
	 * HTTP params (key => value)
	 * @var array $params
	 */
	protected $params = array();

	/**
	 * Authenticaded request
	 * @var boolean $auth
	 */
	protected $auth = false;

	/**
	 * Secure request
	 * @var boolean $auth
	 */
	protected $https = false;

	/**
	 * Raw request body
	 * @var string $body
	 */
	protected $rawBody = '';

	/**
	 * Constructor
	 * @param string $uri
	 */
	public function __construct($uri, $method = self::METHOD_GET) {
		if (
			$method != self::METHOD_GET &&
			$method != self::METHOD_POST &&
			$method != self::METHOD_PUT &&
			$method != self::METHOD_DELETE
		) {
			throw new HTTP_Client_ClientException('Method ('.$method.') not implemented');
		}
		$this->method = (string)$method;
		$this->uri = (string)$uri;

		if (stripos((string)$uri, 'https') === 0) {
			$this->https = true;
		}
	}

	/**
	 * Add a header
	 * @param string $key
	 * @param string $value
	 */
	public function addHeader($key, $value) {
		$this->headers[(string)$key] = (string)$value;
	}

	/**
	 * Return all headers in a key-value array
	 * @return array
	 */
	public function getHeaders() {
		return $this->headers;
	}

	/**
	 * Add a parameter
	 * @param string $key
	 * @param string $value
	 * @return HTTP_Client_Request
	 */
	public function addParam($key, $value) {
		$this->params[(string)$key] = (string)$value;
		ksort($this->params);
		return $this;
	}

	/**
	 * Add an associative array of parameters
	 * @param string $key
	 * @param string $value
	 * @return HTTP_Client_Request
	 */
	public function addParams($paramArray) {
		if(!is_array($paramArray)) {
			return $this;
		}
		foreach ($paramArray as $key => $value) {
			//if (is_array($value)) $value = urldecode(http_build_query(array($key => $value)));
			if (is_array($value)) {
				$this->params[(string)$key] = $value;
			} elseif (is_object($value)) {
				$this->params[(string)$key] = get_object_vars($value);
			} else {
				$this->params[(string)$key] = (string)$value;
			}
		}
		ksort($this->params);
		return $this;
	}

	/**
	 * get all params as an associative array
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * Set raw request body
	 * @param string $value
	 * @return HTTP_Client_Request
	 */
	public function setRawBody($value) {
		$this->rawBody = $value;
		return $this;
	}

	/**
	 * get raw request body
	 * @return string
	 */
	public function getRawBody() {
		return $this->rawBody;
	}

	/**
	 * Return the request URI
	 * @return string
	 */
	public function getUri() {
		return $this->uri;
	}

	/**
	 * Return the request method
	 * @return int
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * Set basic http authentication
	 *
	 * @param string $userName
	 * @param string $password
	 *
	 * @return HTTP_Client_Request
	 */
	public function setAuth($userName, $password) {
		$this->userName   = (string)$userName;
		$this->userPasswd = (string)$password;
		$this->auth = true;
		return $this;
	}

	/**
	 * Get HTTP auth username
	 */
	public function getUsername() {
		return $this->userName;
	}

	/**
	 * Get HTTP auth password
	 */
	public function getUserPassword() {
		return $this->userPasswd;
	}

	/**
	 * Get if http auth is needed
	 */
	public function getAuth() {
		return $this->auth;
	}

	/**
	 * Get if request is secure
	 */
	public function getHttps() {
		return $this->https;
	}

	/**
	 * Fetch $port
	 * @return the $port
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Set  $port
	 * @param $port the $port to set
	 */
	public function setPort($port) {
		$this->port = $port;
		return $this;
	}

}