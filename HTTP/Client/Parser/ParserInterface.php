<?php

interface HTTP_Client_Parser_ParserInterface {
	/**
	 * Parse the $response
	 * @param HTTP_Client_Response $response
	 * @return array Associative array of received variables
	 */
	static function parse(HTTP_Client_Response $response);
}