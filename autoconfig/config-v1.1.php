<?php
header("Content-type: text/xml");

set_include_path('..');

$configs = parse_ini_file('config.ini', true);

$email = filter_input(INPUT_GET, 'emailaddress');
list($local, $domain) = explode('@', $email);

$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$dom->preserveWhiteSpace = true;

$clientConfig = $dom->createElement('clientConfig');
$clientConfigVersion = $dom->createAttribute('version');
$clientConfigVersion->value = 1.1;
$clientConfig->appendChild($clientConfigVersion);

$emailProvider = $dom->createElement('emailProvider');
$emailProviderID = $dom->createAttribute('id');
$emailProviderID->value = $configs['email']['provider'];
$emailProvider->appendChild($emailProviderID);

$emailProvider->appendChild($dom->createElement('domain', $domain));
$emailProvider->appendChild($dom->createElement('displayName', $configs['email']['name']));
$emailProvider->appendChild($dom->createElement('displayShortName', $configs['email']['shortName']));

$serverImapTypeImap = $dom->createAttribute('type');
$serverImapTypeImap->value = 'imap';

$serverImap = $dom->createElement('incomingServer');
$serverImap->appendChild($serverImapTypeImap);
$serverImap->appendChild($dom->createElement('hostname', $configs['imap']['hostname']));
$serverImap->appendChild($dom->createElement('port', $configs['imap']['port']));
$serverImap->appendChild($dom->createElement('socketType', $configs['imap']['socketType']));
$serverImap->appendChild($dom->createElement('authentication', 'password-cleartext'));
$serverImap->appendChild($dom->createElement('username', '%EMAILADDRESS%'));

$emailProvider->appendChild($serverImap);


$serverPop3Type = $dom->createAttribute('type');
$serverPop3Type->value = 'pop3';

$serverPop3 = $dom->createElement('outgoingServer');
$serverPop3->appendChild($serverPop3Type);
$serverPop3->appendChild($dom->createElement('hostname', $configs['pop3']['hostname']));
$serverPop3->appendChild($dom->createElement('port', $configs['pop3']['port']));
$serverPop3->appendChild($dom->createElement('socketType', $configs['pop3']['socketType']));
$serverPop3->appendChild($dom->createElement('authentication', 'password-cleartext'));
$serverPop3->appendChild($dom->createElement('username', '%EMAILADDRESS%'));

$emailProvider->appendChild($serverPop3);

$serverSmtpType = $dom->createAttribute('type');
$serverSmtpType->value = 'pop3';

$serverSmtp = $dom->createElement('outgomingServer');
$serverSmtp->appendChild($serverSmtpType);
$serverSmtp->appendChild($dom->createElement('hostname', $configs['smtp']['hostname']));
$serverSmtp->appendChild($dom->createElement('port', $configs['smtp']['port']));
$serverSmtp->appendChild($dom->createElement('socketType', $configs['smtp']['socketType']));
$serverSmtp->appendChild($dom->createElement('authentication', 'password-cleartext'));
$serverSmtp->appendChild($dom->createElement('username', '%EMAILADDRESS%'));

$emailProvider->appendChild($serverSmtp);

$clientConfig->appendChild($emailProvider);

$dom->appendChild($clientConfig);

echo $dom->saveXML();
exit;
