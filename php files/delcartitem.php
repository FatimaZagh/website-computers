<?php
ob_start();
if(!isset($_SESSION["pname"]))
{
    header("location:login.php");
}
$cartid=$_GET["cartid"];
require_once("vars.php");
$connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
$q="delete from cart where cartid='$cartid'";
$res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
$rowcount=mysqli_affected_rows($connection);
mysqli_close($connection);
 header("location:cart.php");


?>