<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("672995869836-j6fftq3lnioi879o3f4ut2pqa0su4e0m.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/webbanmypham/google-callback.php");
$client->addScope("email");
$client->addScope("profile");

header("Location: " . $client->createAuthUrl());
exit();
