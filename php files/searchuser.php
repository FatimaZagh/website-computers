<?php session_start();?>
<html>
<head>
<title>
    Search User
</title>
<?php
require_once("extfiles.php");
?>
<style>
    #messagep{
        background-color:#3FEEE6;
        padding:10px;
        border-radius:5px;
    }
    #messagen{
        background-color:#FC4445;
        padding:10px;
        border-radius:5px;
    }


.button-40 {
  background-color: #111827;
  border: 1px solid transparent;
  border-radius: .75rem;
  box-sizing: border-box;
  color: #FFFFFF;
  cursor: pointer;
  flex: 0 0 auto;
  font-family: "Inter var",ui-sans-serif,system-ui,-apple-system,system-ui,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
  font-size: 1.125rem;
  font-weight: 600;
  line-height: 1.5rem;
  padding: .75rem 1.2rem;
  text-align: center;
  text-decoration: none #6B7280 solid;
  text-decoration-thickness: auto;
  transition-duration: .2s;
  transition-property: background-color,border-color,color,fill,stroke;
  transition-timing-function: cubic-bezier(.4, 0, 0.2, 1);
  user-select: none;
  -webkit-user-select: none;
  touch-action: manipulation;
  width: auto;
}

.button-40:hover {
  background-color: #374151;
}

.button-40:focus {
  box-shadow: none;
  outline: 2px solid transparent;
  outline-offset: 2px;
}

@media (min-width: 768px) {
  .button-40 {
    padding: .75rem 1.5rem;
  }
  
}
@media (max-width:940px)
{
    form{
        margin-bottom:200px;
    }
    .container{
        margin-bottom:50px;
    }
}
</style>
</head>

<body>
<?php
require_once("adminnavbar.php")
?>
<br><br><br><br>
<h1 class="container lead display-4"align="center">Search User</h1><br><br>
<form name="searchuser"method="POST">
<div class="container lead"align="center">
<input type="text"name="suser"placeholder="Search User Name"><br>
<br>
<input type="submit"name="sbtn"value="Search"class="button-40">
</div>

</form>



<?php
if(isset($_POST["sbtn"]))
{
$uname=$_POST["suser"];

require_once("vars.php");
$connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
$q="select * from signup_page where Username='$uname'";
$res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
$rowcount=mysqli_affected_rows($connection);
mysqli_close($connection);
 if($rowcount==1){
    // $resarr=mysqli_fetch_array($res);
    // print"$resarr[0]";
    print"<br><div align='center'><span class='lead'align='center'id='messagep'>User Exists</span></div>";
 }
 else{
    print"<br><div align='center'><span class='lead'align='center'id='messagen'>This User Does not Exists</span></div>";
 }
}
?>
<br><br><br><br><br><br><br><br><br>
<?php
require_once("footer.php")
?>
</body>
</html>