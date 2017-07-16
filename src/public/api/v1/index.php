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

$app->post('/insertion', function (Request $request, Response $response) use ($pdo) {

	$status = array();

	$user_id_outcome = $pdo->query("SELECT * FROM gcalc_users WHERE username='".$request->getParsedBody()['username']."'");
	$user_id_outcome = $user_id_outcome->fetchAll(PDO::FETCH_ASSOC);

	$code = $request->getParsedBody()['subject_code'];
	$title = $request->getParsedBody()['subject_title'];
	
	$description = "";
	if(empty($request->getParsedBody()['subject_description'])){
		$description = "No description is given to this subject.";
	}else{
		$description = $request->getParsedBody()['subject_description'];
	}

	$grade = $request->getParsedBody()['subject_grade'];
	$units = $request->getParsedBody()['subject_units'];
	
	$notes = "";
	if(empty($request->getParsedBody()['subject_note'])){
		$notes = "Note: Always strive for the best and be on top no matter what happen never care about 
					negative criticism you and you alone can understand your own.";
	}else{
		$notes = $request->getParsedBody()['subject_note'];
	}

	$user_id = 0;

	foreach ($user_id_outcome as $row) {
		$user_id = $row['id'];
	}

	$letter_grade = "";

	if($grade >= 1.0 && $grade < 1.25){
		$letter_grade = "A+";
	}else if($grade >= 1.25 && $grade < 1.50){
		$letter_grade = "A";
	}else if($grade >= 1.50 && $grade < 1.75){
		$letter_grade = "A-";
	}else if($grade >= 1.75 && $grade < 2.00){
		$letter_grade = "B+";
	}else if($grade >= 2.00 && $grade < 2.25){
		$letter_grade = "B";
	}else if($grade >= 2.25 && $grade < 2.50){
		$letter_grade = "B-";
	}else if($grade >= 2.50 && $grade < 2.75){
		$letter_grade = "C+";
	}else if($grade >= 2.75 && $grade < 3.00){
		$letter_grade = "C";
	}else if($grade >= 3.00 && $grade < 4.00){
		$letter_grade = "C-";
	}else{
		$letter_grade = "F";
	}

	$outcome = $pdo->query("SELECT * FROM gcalc_subjects WHERE subject_code='".$code."'");
	$outcome = $outcome->fetchAll(PDO::FETCH_ASSOC);

	if(empty($outcome)){
		$query = "INSERT INTO gcalc_subjects (subject_code,subject_name,subject_description)
						VALUES ('".$code."','".$title."','".$description."')";
		$outcome = $pdo->query($query);
	}

	$query = "INSERT INTO gcalc_grades (user_id,subject_code,number_grade,letter_grade,units,note)
				VALUES ('".$user_id."','".$code."','".$grade."','".$letter_grade."','".$units."','".$notes."')";
	$outcome = $pdo->query($query);

	if($outcome){
		array_push($status, [
			"status" => 200,
			"operations" => "success"
		]);
	}else{
		array_push($status, [
			"status" => 200,
			"operations" => "success"
		]);
	}

    return json_encode($status);
});

$app->get('/retrieve_grades/{username}', function (Request $request, Response $response) use ($pdo) {
	$user_id = 0;
	$data = array();

	$user_id_outcome = $pdo->query("SELECT * FROM gcalc_users WHERE username='".$request->getAttribute('username')."'");
	$user_id_outcome = $user_id_outcome->fetchAll(PDO::FETCH_ASSOC);
	foreach ($user_id_outcome as $row) {
		$user_id = $row['id'];
	}

	$outcome = $pdo->query("SELECT * FROM gcalc_grades WHERE user_id='".$user_id."'");
	$outcome = $outcome->fetchAll(PDO::FETCH_ASSOC);

	foreach ($outcome as $value) {

		$subject_data = $pdo->query("SELECT * FROM gcalc_subjects WHERE subject_code='". $value['subject_code'] ."'");
		$subject_data = $subject_data->fetchAll(PDO::FETCH_ASSOC);

		foreach ($subject_data as $row) {
			array_push($data,[
				"id" => $value['id'],
				"user_id" => $value['user_id'],
				"subject_code" => $value['subject_code'],
				"subject_title" => $row['subject_name'],
				"subject_description" => $row['subject_description'],
				"number_grade" => $value['number_grade'],
				"letter_grade" => $value['letter_grade'],
				"units" => $value['units'],
				"note" => $value['note']
			]);
		}
	}
    return json_encode($data);
});



$app->run();
