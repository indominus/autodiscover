<?php

// return header xml
header('Content-Type: text/xml');

// get email address
preg_match("/\<EMailAddress\>(.*?)\<\/EMailAddress\>/", file_get_contents("php://input"), $matches);

// set email vars
list($local, $domain) = explode('@', $matches[1]);

$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
$dom->preserveWhiteSpace = true;

$clientConfig = $dom->createElement('clientConfig');

/*
 * <clientConfig version="1.1">
  <emailProvider id="freenet.de">
    <domain>freenet.de</domain>
    <displayName>Freenet Mail</displayName>
    <displayShortName>Freenet</displayShortName>
    <incomingServer type="imap">
      <hostname>imap.freenet.de</hostname>
      <port>993</port>
      <socketType>SSL</socketType>
      <authentication>password-encrypted</authentication>
      <username>%EMAILADDRESS%</username>
    </incomingServer>
    <incomingServer type="imap">
      <hostname>imap.freenet.de</hostname>
      <port>143</port>
      <socketType>STARTTLS</socketType>
      <authentication>password-encrypted</authentication>
      <username>%EMAILADDRESS%</username>
    </incomingServer>
    <incomingServer type="pop3">
      <hostname>pop.freenet.de</hostname>
      <port>995</port>
      <socketType>SSL</socketType>
      <authentication>password-cleartext</authentication>
      <username>%EMAILADDRESS%</username>
    </incomingServer>
    <incomingServer type="pop3">
      <hostname>pop.freenet.de</hostname>
      <port>110</port>
      <socketType>STARTTLS</socketType>
      <authentication>password-cleartext</authentication>
      <username>%EMAILADDRESS%</username>
    </incomingServer>
    <outgoingServer type="smtp">
      <hostname>smtp.freenet.de</hostname>
      <port>587</port>
      <socketType>SSL</socketType>
      <authentication>password-encrypted</authentication>
      <username>%EMAILADDRESS%</username>
    </outgoingServer>
    <outgoingServer type="smtp">
      <hostname>smtp.freenet.de</hostname>
      <port>587</port>
      <socketType>STARTTLS</socketType>
      <authentication>password-encrypted</authentication>
      <username>%EMAILADDRESS%</username>
    </outgoingServer>
    <documentation url="http://kundenservice.freenet.de/hilfe/email/programme/config/index.html">
      <descr lang="de">Allgemeine Beschreibung der Einstellungen</descr>
      <descr lang="en">Generic settings page</descr>
    </documentation>
    <documentation url="http://kundenservice.freenet.de/hilfe/email/programme/config/thunderbird/imap-thunderbird/imap/index.html">
      <descr lang="de">TB 2.0 IMAP-Einstellungen</descr>
      <descr lang="en">TB 2.0 IMAP settings</descr>
    </documentation>
  </emailProvider>
</clientConfig>
 */
$autoDiscover = $dom->createElement('Autodiscover');
$autoDiscoverAttribute = $dom->createAttribute('xmlns');
$autoDiscoverAttribute->value = 'http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006';
$autoDiscover->appendChild($autoDiscoverAttribute);
$dom->appendChild($autoDiscover);

$response = $dom->createElement('Response');
$responseAttribute = $dom->createAttribute('xmlns');
$responseAttribute->value = 'http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a';
$response->appendChild($responseAttribute);
$autoDiscover->appendChild($response);

$account = $dom->createElement('Account');
$response->appendChild($account);

$account->appendChild($dom->createElement('AccountType', 'email'));
$account->appendChild($dom->createElement('Action', 'settings'));

$protocolImap = $dom->createElement('Protocol');
$protocolImap->appendChild($dom->createElement('Type', 'IMAP'));
$protocolImap->appendChild($dom->createElement('Server', 'mail.devx.biz'));
$protocolImap->appendChild($dom->createElement('Port', 993));
$protocolImap->appendChild($dom->createElement('LoginName', implode('@', array($local, $domain))));
$protocolImap->appendChild($dom->createElement('DomainRequired', 'on'));
$protocolImap->appendChild($dom->createElement('SPA', 'off'));
$protocolImap->appendChild($dom->createElement('SSL', 'on'));
$protocolImap->appendChild($dom->createElement('AuthRequired', 'on'));
$account->appendChild($protocolImap);

$protocolPop3 = $dom->createElement('Protocol');
$protocolPop3->appendChild($dom->createElement('Type', 'POP3'));
$protocolPop3->appendChild($dom->createElement('Server', 'mail.devx.biz'));
$protocolPop3->appendChild($dom->createElement('Port', 995));
$protocolPop3->appendChild($dom->createElement('LoginName', implode('@', array($local, $domain))));
$protocolPop3->appendChild($dom->createElement('DomainRequired', 'on'));
$protocolPop3->appendChild($dom->createElement('SPA', 'off'));
$protocolPop3->appendChild($dom->createElement('SSL', 'on'));
$protocolPop3->appendChild($dom->createElement('AuthRequired', 'on'));
$account->appendChild($protocolPop3);

$protocolSmtp = $dom->createElement('Protocol');
$protocolSmtp->appendChild($dom->createElement('Type', 'SMTP'));
$protocolSmtp->appendChild($dom->createElement('Server', 'mail.devx.biz'));
$protocolSmtp->appendChild($dom->createElement('Port', 465));
$protocolSmtp->appendChild($dom->createElement('LoginName', implode('@', array($local, $domain))));
$protocolSmtp->appendChild($dom->createElement('DomainRequired', 'on'));
$protocolSmtp->appendChild($dom->createElement('SPA', 'off'));
$protocolSmtp->appendChild($dom->createElement('SSL', 'on'));
$protocolSmtp->appendChild($dom->createElement('AuthRequired', 'on'));
$protocolSmtp->appendChild($dom->createElement('UsePOPAuth', 'on'));
$protocolSmtp->appendChild($dom->createElement('SMTPLast', 'off'));
$account->appendChild($protocolSmtp);

// print results
print_r($dom->saveXML());
exit;
