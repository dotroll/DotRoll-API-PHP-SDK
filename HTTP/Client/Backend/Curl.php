<?php
/**
 * HTTP curl backend
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client_Backend_Curl implements HTTP_Client_Backend_BackendInterface{
	protected $proxy = false;

	/**
	 * Sets the proxy to use
	 * @param string $proxy
	 */
	public function setProxy($proxy) {
		$this->proxy = $proxy;
	}

	/**
	 * Make a flat array from a bidimensional
	 * @param $paramArray
	 */
	protected function flattenParams(&$paramArray) {
		foreach($paramArray as $key => &$value) {
			if (is_array($value)) {
				foreach($value as $vKey => $vValue) {
					$paramArray[$key.'[]'] = $vValue;
				}
				unset($paramArray[$key]);
			}
		}
	}

	/**
	 * Send a curl request
	 * @param HTTP_Client_Request $request
	 *
	 * @throws HTTP_Client_ClientException
	 * @return @HTTP_Client_Response
	 */
	public function sendRequest(HTTP_Client_Request $request) {
		//create resource
		$curlResource = curl_init();
		$uri = $request->getUri();

		$headers = array();

		//choose method
		switch ($request->getMethod()) {
			case HTTP_Client_Request::METHOD_POST:
				curl_setopt($curlResource, CURLOPT_POST, 'true');
				$rawBody = $request->getRawBody();
				if (empty($rawBody)) {
					if (!empty($_FILES)) {
						$params = $request->getParams();
						$this->flattenParams($params);
						foreach($_FILES as $key => $value) {
							$params[$key] = '@'.$value['tmp_name'];
						}
						curl_setopt($curlResource, CURLOPT_POSTFIELDS, $params);
					} else {
						curl_setopt($curlResource, CURLOPT_POSTFIELDS, http_build_query($request->getParams()));
						$headers['Content-Type'] = 'application/x-www-form-urlencoded';
					}
				} else {
					curl_setopt($curlResource, CURLOPT_POSTFIELDS, $rawBody);
				}
				break;
			case HTTP_Client_Request::METHOD_PUT:
				curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, "PUT");
				$rawBody = $request->getRawBody();
				if (empty($rawBody)) {
					if (!empty($_FILES)) {
						$params = $request->getParams();
						$this->flattenParams($params);
						foreach($_FILES as $key => $value) {
							$params[$key] = '@'.$value['tmp_name'];
						}
						curl_setopt($curlResource, CURLOPT_POSTFIELDS, $params);
					} else {
						curl_setopt($curlResource, CURLOPT_POSTFIELDS, http_build_query($request->getParams()));
						$headers['Content-Type'] = 'application/x-www-form-urlencoded';
					}
				} else {
					curl_setopt($curlResource, CURLOPT_POSTFIELDS, $rawBody);
				}
				break;
			case HTTP_Client_Request::METHOD_GET:
				$params = $request->getParams();
				if (!empty($params)) {
					$uri .= (false === strpos($uri, '?'))?'?':'&';
					$uri .= http_build_query($params);
				}
				break;
			case HTTP_Client_Request::METHOD_DELETE:
				curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, "DELETE");
				$params = $request->getParams();
				if (!empty($params)) {
					$uri .= (false === strpos($uri, '?'))?'?':'&';
					$uri .= http_build_query($params);
				}
				break;
			default:
				throw new HTTP_Client_ClientException('Method ('.$request->getMethod().') not implemented in HTTP_Client_Backend_Curl');
		}

		//set url
		curl_setopt($curlResource, CURLOPT_URL, $uri);


		$expect = false;
		$useragent = false;
		foreach ($request->getHeaders() as $header => $value) {
			if (array_key_exists($header, $headers)) {
				continue;
			}
			if ($header === 'Content-Type') {
				continue;
			}
			if (stripos($header, '\n') !== false || stripos($header, '\r') !== false ||
				stripos($value, '\n') !== false || stripos($value, '\r') !== false) {
				throw new Exception("Invalid header: " . $header . ":" . $value);
			}
			if ($header == 'User-Agent') {
				$useragent = $value;
			} else {
				$headers[] = $header . ": " . $value;
				if ($header == "Expect") {
					$expect = true;
				}
			}
		}
		if (!$expect) {
			//Work around LigHTTPd-s 417 bug
			$headers[] = "Expect:";
		}
		curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headers);

		if ($useragent) {
			curl_setopt($curlResource, CURLOPT_USERAGENT, $useragent);
		}

		//set port if needed
		if (null !== $request->getPort()) {
			curl_setopt($curlResource, CURLOPT_PORT, $request->getPort());
		}

		//set proxy if needed
		if ($this->proxy) {
			curl_setopt($curlResource, CURLOPT_PROXY, $this->proxy);
		} else {
			curl_setopt($curlResource, CURLOPT_PROXY, false);
		}

		//set HTTP auth if needed
		if ($request->getAuth()) {
			curl_setopt($curlResource, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt(
				$curlResource,
				CURLOPT_USERPWD,
				$request->getUserName()
					. ":"
					. $request->getUserPassword())
			;
		}

		//set https if needed
		if ($request->getHttps()) {
			curl_setopt($curlResource, CURLOPT_SSLVERSION,3);
			curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curlResource, CURLOPT_SSL_VERIFYHOST, 2);
		}

		//return values as string instead of outputting it
		curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);

		//send and return headers
		curl_setopt($curlResource, CURLOPT_HEADER, true);
		$curlResponse = curl_exec($curlResource);
		if ($curlResponse === false) {
			throw new HTTP_Client_ClientException('Curl call to "' . $request->getUri() . '" failed, ' . curl_error ($curlResource), curl_errno($curlResource));
		}
		return $curlResponse;
	}

	/**
	 * Fetch $proxy
	 * @return the $proxy
	 */
	public function getProxy() {
		return $this->proxy;
	}

}