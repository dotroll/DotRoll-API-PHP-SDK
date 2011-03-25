<?php
/**
 * This SDK is aimed to provide understandably easy access to the DotRoll REST API
 *
 * Usage:
 *   $dotRoll = new DotRollApi('myUserName', 'myPassword12', true);
 *   $prices = $dotRoll->get('domain/prices/HUF');
 *
 * @copyright Copyright (c) 2010 DotRoll Kft. (http://www.dotroll.com)
 * @author Zoltan Siegl <siegl.zoltan@dotroll.com>
 */

require('./HTTP/Client.php');
require('./HTTP/Client/ClientException.php');
require('./HTTP/Client/Request.php');
require('./HTTP/Client/Response.php');
require('./HTTP/Client/Backend/BackendInterface.php');
require('./HTTP/Client/Backend/Curl.php');

/**
 * DotRoll API class
 *
 * This is the main class to be used in the SDK. This implements all the functions
 * needed to access services of the DotRoll REST API
 *
 * @copyright Copyright (c) 2010 DotRoll Kft. (http://www.dotroll.com)
 * @author Siegl Zoltan <siegl.zoltan@dotroll.com>
 */
class DotRollApi {
	/**
	 * Constructor
	 *
	 * This boots up DotRoll API
	 *
	 * @param string  $userName   A username that is registered at DotRoll and
	 *                            is eligable to access DotRoll API
	 * @param string  $password   The password for the user above
	 * @param string  $apiKey     The API key provided by DotRoll support staff
	 * @param boolean $useSandBox Use the API in sandbox mode? If true, test mode
	 *                            is in use, changes will not effect production
	 *                            database.
	 */
	public function __construct($userName, $password, $apiKey, $useSandBox = true) {
		$this->selfTest();
		$this->httpClient = new HTTP_Client();
		$this->userName = $userName;
		$this->password = $password;
		$this->apiKey   = $apiKey;
	}

	/**
	 * Send a get request for the DotRoll rest API
	 *
	 * @param string $uri
	 */
	public function get($uri) {
		$uri = rtrim($uri, '/');
		$request = new HTTP_Client_Request(
			self::API_URL.'/'.self::API_VERSION.'/'.$uri,
			HTTP_Client_Request::METHOD_GET
		);
		$request->setAuth($this->userName, $this->password);
		$request->addParam('api_key', $this->apiKey);
		$result = $this->httpClient->sendRequest($request);
		return json_decode($result->getResponseText());
	}

	/**
	 * Send a delete request for the DotRoll rest API
	 *
	 * @param string $uri
	 */
	public function delete($uri) {
		$uri = rtrim($uri, '/');
		$request = new HTTP_Client_Request(
			self::API_URL.'/'.self::API_VERSION.'/'.$uri,
			HTTP_Client_Request::METHOD_DELETE
		);
		$request->setAuth($this->userName, $this->password);
		$result = $this->httpClient->sendRequest($request);
		return json_decode($result->getResponseText());
	}

	/**
	 * Send a post request for the DotRoll rest API
	 *
	 * @param string $uri
	 */
	public function post($uri, $data) {
		$uri = rtrim($uri, '/');
		$request = new HTTP_Client_Request(
			self::API_URL.'/'.self::API_VERSION.'/'.$uri,
			HTTP_Client_Request::METHOD_POST
		);
		$request->setAuth($this->userName, $this->password);
		$request->addParams($data);
		$request->addParam('api_key', $this->apiKey);
		$result = $this->httpClient->sendRequest($request);
		return json_decode($result->getResponseText());
	}

	/**
	 * Send a put request for the DotRoll rest API
	 *
	 * @param string $uri
	 */
	public function put($uri, $data) {
		$uri = rtrim($uri, '/');
		$request = new HTTP_Client_Request(
			self::API_URL.'/'.self::API_VERSION.'/'.$uri,
			HTTP_Client_Request::METHOD_POST
		);
		$request->setAuth($this->userName, $this->password);
		$request->addParams($data);
		$result = $this->httpClient->sendRequest($request);
		return json_decode($result->getResponseText());
	}

	/**
	 * Get domain prices in the given currency
	 * 'HUF', 'EUR', and 'USD' is currently accepted
	 * @param string $currency
	 */
	public function getDomainPrices($currency) {
		return $this->get('domain/prices/'.$currency);
	}

	/**
	 * Get hosting prices in the given currency
	 * 'HUF', 'EUR', and 'USD' is currently accepted
	 * @param string $domainName The domain name to check
	 */
	public function getHostingPrices($currency) {
		return $this->get('hosting/prices/'.$currency);
	}

	/**
	 * Get virtual personal server prices in the given currency
	 * 'HUF', 'EUR', and 'USD' is currently accepted
	 * @param string $domainName The domain name to check
	 */
	public function getVPSPrices($currency) {
		return $this->get('vps/prices/'.$currency);
	}

	/**
	 * Get availablity of a domain name
	 *
	 * @param string $currency
	 */
	public function getDomainAvailablity($domainName) {
		return $this->get('domain/search/'.$domainName);
	}

	/**
	 * Create a new domain contact
	 *
	 * @param string $firstName
	 * @param string $lastName
	 * @param bolean $isOrganisation
	 * @param string $identity
	 * @param string $vatNumber
	 * @param string $euVatNumber
	 * @param string $passport
	 * @param string $registryNumber
	 * @param string $orgLongName
	 * @param string $domainPartnerType
	 * @param string $addressName
	 * @param string $addressCountry
	 * @param string $addressState
	 * @param string $addressLocality
	 * @param string $addressPostalCode
	 * @param string $addressStreet
	 * @param string $addressStreetNumber
	 * @param string $email
	 * @param string $phone
	 * @param string $fax
	 *
	 * @return int|boolean The id of the created domain contact if success
	 *                     or false if failed
	 */
	public function createDomainContact(
		$firstName,
		$lastName,
		$isOrganisation,
		$identity,
		$vatNumber,
		$euVatNumber,
		$passport,
		$registryNumber,
		$orgName,
		$orgLongName,
		$domainPartnerType,
		$addressName,
		$addressState,
		$addressLocality,
		$addressPostalCode,
		$addressStreet,
		$addressStreetNumber,
		$email,
		$phone,
		$fax
	) {
		$data = array(
			'firstName'         => $firstName,
			'lastName'          => $lastName,
			'isOrganisation'    => $isOrganisation,
			'identity'          => $identity,
			'vatNumber'         => $vatNumber,
			'euVatNumber'       => $euVatNumber,
			'passport'          => $passport,
			'registryNumber'    => $registryNumber,
			'orgLongName'       => $orgLongName,
			'domainPartnerType' => $domainPartnerType,
			'state'             => $addressState,
			'locality'          => $addressLocality,
			'postalCode'        => $addressPostalCode,
			'street'            => $addressStreet,
			'name'              => $addressName,
			'streetNumber'      => $addressStreetNumber,
			'email'             => $email,
			'phone'             => $phone,
			'fax'               => $fax
		);
		return $this->post('domain/contact', $data);
	}

	/**
	 * Register a new domain
	 * @param string  $domainName     The domain name to be registered
	 * @param integer $ownerContactId The id received when creating contact with
	 *                                createDomainContact()
	 * @param integer $adminContactId The id received when creating contact with
	 *                                createDomainContact()
	 * @param integer $techContactId  The id received when creating contact with
	 *                                createDomainContact()
	 * @param integer $years          The term in years to register domain for
	 * @param string  $nameserver1    Nameserver 1 (if empty, default dotrol ns will be used)
	 * @param string  $nameserver2    Nameserver 2 (if empty, default dotrol ns will be used)
	 * @param boolean $priority       Priority registration (for .hu domains only)
	 */
	public function registerDomain(
		$domainName,
		$ownerContactId,
		$techContactId,
		$years,
		$nameserver1 = null,
		$nameserver2 = null,
		$priority = false
	) {
		$data = array(
			'ownerContactId' => $ownerContactId,
			'techContactId'  => $techContactId,
			'years'          => $years,
			'ns1'            => $nameserver1 = null,
			'ns2'            => $nameserver2 = null,
			'priority'       => $priority = false
		);
		return $this->post('domain/'.$domainName, $data);
	}


	/**
	 * Test if every needed PHP extension is installed
	 */
	protected function selfTest() {
		if (!function_exists('curl_init')) {
			throw new Exception('DotRoll PHP SDK needs the CURL PHP extension.');
		}

		if (!function_exists('json_decode')) {
			throw new Exception('DotRoll PHP SDK needs the JSON PHP extension.');
		}
	}

	/**
	 * API Version to use
	 * @var string API_VERSION
	 */
	const API_VERSION = '1.0';

	/**
	 * DotRoll REST API base URL
	 * @var string API_URL
	 */
	const API_URL = 'http://webservices.dotroll.com/rest';

	/**
	 * Domain-partner kapcsolat típus: HUREG tulaj
	 */
	const DOMAIN_PARTNER_TYPE_HUREG_OWNER=1;
	/**
	 * Domain-partner kapcsolat típus: HUREG kontakt
	 */
	const DOMAIN_PARTNER_TYPE_HUREG_CONTACT=2;
	/**
	 * Domain-partner kapcsolat típus: COMREG kontakt
	 */
	const DOMAIN_PARTNER_TYPE_COMREG_CONTACT=3;
	/**
	 * Domain-partner kapcsolat típus: EUREG tulaj
	 */
	const DOMAIN_PARTNER_TYPE_EUREG_OWNER=4;
	/**
	 * Domain-partner kapcsolat típus: EUREG kontakt
	 */
	const DOMAIN_PARTNER_TYPE_EUREG_CONTACT=5;
	/**
	 * Domain-partner kapcsolat típus: HUREG tulaj
	 */
	const DOMAIN_PARTNER_TYPE_COMREG_OWNER=6;
	/**
	 * COMNET kontakt
	 */
	const DOMAIN_PARTNER_TYPE_VERISIGN_CONTACT=7;
	/**
	 * ORG kontakt
	 */
	const DOMAIN_PARTNER_TYPE_ORG_CONTACT=8;

	/**
	 * HTTP_Client instance
	 * @var HTTP_Client $httpClient
	 */
	protected $httpClient;

	/**
	 * DotRoll API user name
	 * @var string $userName
	 */
	protected $userName;

	/**
	 * DotRoll API password
	 * @var string $password
	 */
	protected $password;

	/**
	 * The API key, provided by DotRoll support staff
	 * @var string $apiKey
	 */
	protected $apiKey;


}