<?php
ob_start();
if(!isset($_SESSION["pname"]))
{
    header("location:login.php");       //check for login
}
if($_SESSION["usertype"]!=="admin"){
    header("location:login.php");           //admin pages security
   }


if(!isset($_SESSION["pname"]))
{
    header("location:login.php");
}
$pid=$_GET["pid"];
require_once("vars.php");
$connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
$q="delete from manageproduct where productid='$pid'";
$res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
$rowcount=mysqli_affected_rows($connection);
mysqli_close($connection);
 header("location:viewproducts.php");


?>