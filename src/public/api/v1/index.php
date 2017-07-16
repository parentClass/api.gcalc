<?php

header('Access-Control-Allow-Origin: *');

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$pdo = new PDO("mysql:host=localhost;"."dbname=id1855654_thenotes","id1855654_admin","09159177693cT");

$app = new \Slim\App(["settings"=>$config]);

$app->post('/validate_user', function (Request $request, Response $response) use ($pdo) {
	$status = array();
	if(!empty($request->getParsedBody()['username']) && !empty($request->getParsedBody()['passkey'])){
		$username = $request->getParsedBody()['username'];
		$password = md5($request->getParsedBody()['passkey']);

		$outcome = $pdo->query("SELECT * FROM gcalc_users WHERE username='".$username."' AND passkey='".$password."'");
		$outcome = $outcome->fetchAll(PDO::FETCH_ASSOC);

		if(!empty($outcome)){
			array_push($status, [
				"status" => 200,
				"validate_user_success" => true,
				"is_valid" => true
			]);
		}else{
			array_push($status, [
				"status" => 200,
				"validate_user_success" => true,
				"is_valid" => false
			]);
		}

	}else{
		array_push($status, [
			"status" => 200,
			"validate_user" => false,
			"error_occurred" => [
				"1" => "username or passkey is empty"
			]
		]);
	}

    return json_encode($status);
});

$app->run();
