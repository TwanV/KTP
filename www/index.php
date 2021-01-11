<?php

include '../util.php';
include '../solver.php';
include '../reader.php';

date_default_timezone_set('Europe/Amsterdam');

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' 
	&& $_POST['action'] == 'run')
{
	header('Location: webfrontend.php?kb=' . rawurlencode('Pacemaker_6.xml'));
	exit;
}

function process_file($file, array &$errors = array())
{
	if ($file['error'] != 0)
		return "Er is een fout opgetreden bij het uploaden.";

	$reader = new KnowledgeBaseReader;
	$errors = $reader->lint($file['tmp_name']);

	$unique_name = sha1(microtime() . uniqid('kb', true)) . '.xml';

	if (count($errors) > 0)
		return false;

	if (!move_uploaded_file($file['tmp_name'], '../knowledgebases/' . $unique_name))
	{
		$errors[] = "De knowledge-base kon niet worden opgeslagen op de server.";
		return false;
	}
	
	return $unique_name;
}

$template = new Template('templates/double.phtml');
$template->errors = $errors;

echo $template->render();
