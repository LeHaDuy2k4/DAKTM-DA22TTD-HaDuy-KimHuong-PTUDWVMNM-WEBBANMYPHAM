<?php
require 'vendor/autoload.php';

$client = new Google_Client();
$client->setClientId("672995869836-j6fftq3lnioi879o3f4ut2pqa0su4e0m.apps.googleusercontent.com");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/DAKTM-DA22TTD-HaDuy-KimHuong-PTUDWVMNM-WEBBANMYPHAM/webbanmypham/google-callback.php");
$client->addScope("email");
$client->addScope("profile");

header("Location: " . $client->createAuthUrl());
exit();
