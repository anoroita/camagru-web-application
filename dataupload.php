<?php
session_start();

if (!isset($_SESSION['LOGGED_ON']) || !$_GET)
	header('location:index.php');

if ($_SESSION['LOGGED_ON'] && $_GET)
{
	if (!file_exists("./captured_pics"))
		mkdir("./captured_pics");
	$filter = "./sources/filters/" . $_GET['filter'] . ".png";
	$filedata = file_get_contents("./captured_pics/" . $_GET['data']);
	$filepath = "./captured_pics/";
	$filesql = $_SESSION['ID'] . " " . time() . '.png';
	$filename = $filepath . $_SESSION['ID'] . " " . time() . '.png';
	file_put_contents($filename, $filedata);

	if (file_exists($filter))
	{
		$dest = imagecreatefromstring($filedata);
		$src = imagecreatefrompng($filter);
		$src = imagescale($src, imagesx($dest) * 0.5);
		imagecopy($dest, $src, 0, 0, 0, 0, imagesx($src) - 1, imagesy($src) - 1);
		imagepng($dest, $filename);
	}
	try
	{
		$connection = new PDO("mysql:host=localhost;dbname=db_camagru", "root", "simple");
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$req = $connection->prepare('INSERT INTO Photos (username, timet, url, UserID) VALUES (:username, NOW() , :url, :userID)');
		$req->execute(array(
			':username' => $_SESSION['LOGGED_ON'],
			':url' => $filesql,
			':userID' => $_SESSION['ID']
		));
	}

	catch(PDOException $e)
	{
		echo "Couldn't write in Database: " . $e->getMessage();
	}

	header("location:index.php");
}

 ?>
