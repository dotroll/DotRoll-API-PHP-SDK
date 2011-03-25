<?php
/**
 * Http backend interface
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
interface HTTP_Client_Backend_BackendInterface {
	/**
	 * Send a request
	 *
	 * @TODO 1001 After refactoring check your tests!
	 *
	 * @param HTTP_ClientRequest $request
	 *
	 * @throws HTTP_ClientException
	 * @return string Raw HTTP response with header
	 */
	public function sendRequest(HTTP_Client_Request $request);

	/**
	 * Sets the proxy URL to use.
	 * @param string $proxy|false to disable
	 */
	public function setProxy($proxy);

	/**
	 * Returns proxy URL
	 * @return string proxy url|false
	 */
	public function getProxy();
}