<?php
ob_start();
if( empty(session_id()) && !headers_sent()){
    session_start();}
 ?>

<html>
    <head>

<style>
.user{
    color:#d4d4d4;

    font-size:20px;
}
#userpic{
color:white;
}
#userpic:hover{
   background-color:black;
   filter: invert(100%);
   color:white;

}


</style>

</head>
<body>
<?php if( empty(session_id()) && !headers_sent()){
        session_start();}
        if(isset($_SESSION["usertype"])){
    $usertype=$_SESSION["usertype"];

    if($usertype=='admin'){

    require_once("adminnavbar.php");
}
    else{
        require_once("header.php");
    }
 } ?>
<nav align="center">
<div class="logo lead font-weight-normal"><a href="index.php"><img src="assets/logo.png"class="logoimg"style="width:60px;filter:invert(10);padding-top:8px;"></a></div>
<input type="checkbox"id="click">
<label for="click"class="menu-btn">
    <i><img src='assets/dropbtn.png'style='width:20px;'></i>
</label>
<ul style="margin-bottom:27px;">


    <li> <a href="index.php"class="lead">Home</a></li>
    <li> <a href="showcat.php"class="lead">Products</a></li>


    <!-- If the person is Logged in -->
    <?php
    if(isset($_SESSION["pname"])){
       print" <li>
       <a href='cart.php'class='lead'id='userpic'>Cart
       </a></li>
       <li><a href='myorders.php'class='lead'id='userorder'>My Orders</a></li>";
    }
    ?>



    <li> <a href="contactus.php"class="lead">Contact us</a></li>
    <li>
    <?php
    if(isset($_SESSION["pname"]))
    {
        print'<a href="userprofile.php"id="userpic"class="lead">';
        print$_SESSION["pname"];

        print'&nbsp;&nbsp;<img src="assets/user-24.png"></a></li></span>';
    }
    else{
        print'<a href="login.php"id="userpic"class="lead">LogIn &nbsp;&nbsp;';
        print'<img src="assets/add-user-24.png"></a></li>';
    }


    // <img src='assets/cartpic.png'width='31px'style='padding-left:10px;'> cart image
    ?>


</ul>
</nav>

<!-- Pass login status to JavaScript for recently viewed feature -->
<script>
window.userLoggedIn = <?php echo isset($_SESSION['userprimid']) ? 'true' : 'false'; ?>;
window.userEmail = <?php echo isset($_SESSION['userprimid']) ? "'" . addslashes($_SESSION['userprimid']) . "'" : 'null'; ?>;
</script>
