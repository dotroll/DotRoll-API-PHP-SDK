<?php

/**
 * HTTP_Client_Response URL type query parser
 *
 * @copyright Copyright (c) 2010 Dotroll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class HTTP_Client_Parser_Url implements HTTP_Client_Parser_ParserInterface {
	/**
	 * @param HTTP_Client_Response $response
	 */
	static function parse(HTTP_Client_Response $response) {
		parse_str($response->getResponseText(), $result);
		if (!$result) {
			throw new HTTP_Client_Parser_ParserException('URL parse failed: ' . $response->getResponseText());
		}
		return $result;
	}
}