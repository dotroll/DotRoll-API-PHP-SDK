<?php

/**
 * This class provides a JSON parser for the HTTP parser interface
 */
class HTTP_Client_Parser_JSONParser implements HTTP_Client_Parser_ParserInterface {
	/**
	 * This function parses a HTTP response by JSON rules
	 * @param HTTP_Client_Response $response
	 * @return array
	 */
	static function parse(HTTP_Client_Response $response) {
		try {
			$data = json_decode($response->getResponseText(), true);
			if (JSON_ERROR_NONE !== json_last_error()) {
				throw new HTTP_Client_Parser_ParserException("Invalid JSON sequence: " . $data);
			}
			return $data;
		} catch (Exception $e) {
			throw new HTTP_Client_Parser_ParserException($e->getMessage(), $e->getCode(), $e);
		}
	}
}