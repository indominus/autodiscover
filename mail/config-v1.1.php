<?php
header("Content-type: text/xml");

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
$emailProviderID->value = 'mail.devx.biz';
$emailProvider->appendChild($emailProviderID);

$emailProvider->appendChild($dom->createElement('domain', $domain));
$emailProvider->appendChild($dom->createElement('displayName', 'DevX Mail Server'));
$emailProvider->appendChild($dom->createElement('displayShortName', 'DevX Mail'));

foreach (['imap' => 143, 'imaps' => 993, 'pop3' => 110, 'pop3s' => 995, 'smtp' => 25, 'smtps' => 465] AS $protocol => $port) {
    
    $incomingServer = $dom->createElement('incomingServer');
    
    $incomingServerType = $dom->createAttribute('type');
    $incomingServerType->value = $protocol;
    
    $incomingServer->appendChild($incomingServerType);
    
    $incomingServer->appendChild($dom->createElement('hostname', 'mail.devx.biz'));
    $incomingServer->appendChild($dom->createElement('port', $port));
    $incomingServer->appendChild($dom->createElement('socketType', in_array($port, [143, 110, 25]) ? 'STARTTLS' : 'SSL'));
    $incomingServer->appendChild($dom->createElement('authentication', 'password-encrypted'));
    $incomingServer->appendChild($dom->createElement('username', '%EMAILADDRESS%'));
    
    $emailProvider->appendChild($incomingServer);
}

$clientConfig->appendChild($emailProvider);

$dom->appendChild($clientConfig);

echo $dom->saveXML();
exit;
