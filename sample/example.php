<?php

include('../DotRollApi.php');
$apiUser = "";		// A DotRoll rendszerében lévő felhasználónév
$apiPassword = "";	// A DotRoll rendszerében lévő felhasználóhoz tartozó jelszó
$apiKey = "";		// A DotRoll felhasználói felületén igényelt teszt vagy éles API kulcs.
$sandboxMode = false;	// Teszt üzemmód esetén true, élses környezetben false
$dotRoll = new DotRollApi($apiUser, $apiPassword, $apiKey, $sandboxMode);

switch ($_GET['method']) {
	case 'domainSearch' :
		if(isset($_POST['search'])) {
			echo $_POST['domainName'] . ": \n\r";
			print_r($dotRoll->getDomainAvailablity($_POST['domainName']));
		}
		break;
	case 'pricesList' :
		echo '<pre>';
		if (isset($_POST['domain'])) {
			print_r($dotRoll->getDomainPrices($_POST['currency']));
		}
		if (isset($_POST['hosting'])) {
			print_r($dotRoll->getHostingPrices($_POST['currency']));
		}
		if (isset($_POST['vps'])) {
			print_r($dotRoll->getVpsPrices($_POST['currency']));
		}
		echo '</pre>';
		break;
	case 'createContact' :
		echo '<pre>';
		if (isset($_POST['orgContact'])) {
			// Tulajdonos típusának megadása a domain TLD-től függően.
			$contactType = DotRollApi::DOMAIN_PARTNER_TYPE_HUREG_OWNER;
			print_r($request = $dotRoll->createDomainContact(
				$_POST['firstName'],
				$_POST['lastName'],
				$_POST['isOrganisation'],
				$_POST['identity'],	// Személyi igazolvány szám (magánszemély) kötelező
				$_POST['vatNumber'],
				$_POST['euVatNumber'],	//EU adószám (EU-n belüli, nem Magyarországon bejegyzett cégek esetén kötelező)
				$_POST['passport'],	// Útlevélszám (Nem magyar állampolgárságú magánszemély esetén kötelező)
				$_POST['registryNumber'],
				$_POST['orgName'],
				$_POST['orgLongName'],
				$contactType,
				'$addressName',
				$_POST['country'],
				$_POST['state'],
				$_POST['city'],
				$_POST['postalCode'],
				$_POST['street'],
				$_POST['streetNumber'],
				$_POST['email'],
				$_POST['phone'],
				$_POST['fax']));
			if($request->result == 'SUCCESS') {
				// Célszerű a sikeres contact létrehozást követően a contact adatokat és contact Id-t elmenteni.
			}
		}
		if (isset($_POST['adminContact'])) {
			// Admin contact típusának megadása a domain TLD-től függően.
			$contactType = DotRollApi::DOMAIN_PARTNER_TYPE_HUREG_CONTACT;
			print_r($request = $dotRoll->createDomainContact(
				$_POST['firstName'],
				$_POST['lastName'],
				$_POST['isOrganisation'],
				$_POST['identity'],	// Személyi igazolvány szám (magánszemély) kötelező
				$_POST['vatNumber'],
				$_POST['euVatNumber'],	//EU adószám (EU-n belüli, nem Magyarországon bejegyzett cégek esetén kötelező)
				$_POST['passport'],	// Útlevélszám (Nem magyar állampolgárságú magánszemély esetén kötelező)
				$_POST['registryNumber'],
				$_POST['orgName'],
				$_POST['orgLongName'],
				$contactType,
				'$addressName',
				$_POST['country'],
				$_POST['state'],
				$_POST['city'],
				$_POST['postalCode'],
				$_POST['street'],
				$_POST['streetNumber'],
				$_POST['email'],
				$_POST['phone'],
				$_POST['fax']));
			if($request->result == 'SUCCESS') {
				// Célszerű a sikeres contact létrehozást követően a contact adatokat és contact Id-t elmenteni.
			}
		}
		if (isset($_POST['techContact'])) {
			// Tech contact típusának megadása a domain TLD-től függően.
			$contactType = DotRollApi::DOMAIN_PARTNER_TYPE_HUREG_CONTACT;
			print_r($request = $dotRoll->createDomainContact(
				$_POST['firstName'],
				$_POST['lastName'],
				$_POST['isOrganisation'],
				$_POST['identity'],	// Személyi igazolvány szám (magánszemély) kötelező
				$_POST['vatNumber'],
				$_POST['euVatNumber'],	//EU adószám (EU-n belüli, nem Magyarországon bejegyzett cégek esetén kötelező)
				$_POST['passport'],	// Útlevélszám (Nem magyar állampolgárságú magánszemély esetén kötelező)
				$_POST['registryNumber'],
				$_POST['orgName'],
				$_POST['orgLongName'],
				$contactType,
				'$addressName',
				$_POST['country'],
				$_POST['state'],
				$_POST['city'],
				$_POST['postalCode'],
				$_POST['street'],
				$_POST['streetNumber'],
				$_POST['email'],
				$_POST['phone'],
				$_POST['fax']));
			if($request->result == 'SUCCESS') {
				// Célszerű a sikeres contact létrehozást követően a contact adatokat és contact Id-t elmenteni.
			}
		}
		echo '</pre>';
		break;
	case 'domainRegistration':
		echo '<pre>';
		if (isset($_POST['registration'])) {
			print_r($dotRoll->registerDomain($_POST['domainName'],
				$_POST['ownerContactId'],
				$_POST['adminContactId'],
				$_POST['techContactId'],
				$_POST['years'],
				$_POST['ns1'],	// opcionális
				$_POST['ns2'],	// opcionális
				NULL	// Csak homokozó módban a null érték sikeres regisztrációt jelent-t, különben az elvárt hiba üzenet kódja.
				));

		}
		echo '</pre>';
		break;
}
?>