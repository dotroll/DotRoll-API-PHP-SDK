<?php
/**
 * Http client exception
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client_ClientException extends Exception{
	/**
	 * Constructor
	 *
	 * Prepend default message to the $message received
	 *
	 * @param string $message default ''
	 * @param int    $code    default 0
	 */

	public function __construct($message = '', $code = 0) {
		$message = 'HTTP Client error - ' . $message;
		parent::__construct($message, $code);
	}
}