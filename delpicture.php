<?php
session_start();

if (!isset($_SESSION['LOGGED_ON']) || !$_GET)
	header('location:index.php');

$pic = explode(" ", $_GET['pic']);
$path = "./captured_pics/";
if ($_SESSION['ID'] === $pic[0])
{
	$pic = implode(" ", $pic);
	unlink("$path" . "$pic");
	header("location:index.php");
	echo "$pic";

	try
	{
		$connectionection = new PDO("mysql:host=localhost;dbname=db_camagru", "root", "simple");
		$connectionection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$req = $connectionection->prepare('DELETE FROM photos WHERE url = :url');
		$req->execute(array(
			':url' => $pic
		));

	}
	catch (Exception $e)
	{
		echo "couldn't delete picture from databse:" . $e->getMessage();
	}

}

else {
	header( "refresh:2;url=index.php" );
	echo "Too bad..";
}
?>
