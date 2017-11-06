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

foreach (['imap' => 993, 'pop3' => 995, 'smtp' => 465] AS $protocol => $port) {
    
    $server = $dom->createElement(in_array($protocol, ['smtp', 'smtps']) ? 'outgoingServer' : 'incomingServer');
    
    $serverType = $dom->createAttribute('type');
    $serverType->value = $protocol;
    
    $server->appendChild($serverType);
    
    $server->appendChild($dom->createElement('hostname', 'mail.devx.biz'));
    $server->appendChild($dom->createElement('port', $port));
    $server->appendChild($dom->createElement('socketType', in_array($port, [143, 110, 25]) ? 'STARTTLS' : 'SSL'));
    $server->appendChild($dom->createElement('authentication', 'password-encrypted'));
    $server->appendChild($dom->createElement('username', '%EMAILADDRESS%'));
    
    $emailProvider->appendChild($server);
}

$clientConfig->appendChild($emailProvider);

$dom->appendChild($clientConfig);

echo $dom->saveXML();
exit;
