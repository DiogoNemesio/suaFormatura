<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../../../include.php');
}

require_once(CLASS_PATH . "/WhatsAPI/whatsprot.class.php");
require_once(CLASS_PATH . "/WhatsAPI/events/MyEvents.php");

$debug = true;

function onCredentialsBad($mynumber, $status, $reason)
{
    if ($reason == 'blocked')
        echo "\n\nO número está bloqueado \n";
    if ($reason == 'incorrect')
        echo "\n\nIdentidade errado. \n";
}

function onCredentialsGood($mynumber, $login, $password, $type, $expiration, $kind, $price, $cost, $currency, $price_expiration)
{
    echo "\n\nYour number $mynumber with the following password $password is not blocked \n";
}

echo "####################\n";
echo "#                  #\n";
echo "# WA Block Checker #\n";
echo "#                  #\n";
echo "####################\n";

echo "\n\nUsername (country code + number without + or 00): ";
$username = trim(fgets(STDIN));

$w = new WhatsProt($username, '', $debug);
$w->eventManager()->bind("onCredentialsBad", "onCredentialsBad");
$w->eventManager()->bind("onCredentialsGood", "onCredentialsGood");


$w->checkCredentials();

?>
