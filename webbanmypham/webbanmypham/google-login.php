<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("672995869836-d608kojgdmj1f3gma8nh16cuvbaec2so.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost:85/webbanmypham/google-callback.php");
$client->addScope("email");
$client->addScope("profile");

header("Location: " . $client->createAuthUrl());
exit();
