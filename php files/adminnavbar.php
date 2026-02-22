<?php 
ob_start();
if( empty(session_id()) && !headers_sent()){
    session_start();}
 ?>
 
 <!-- ADMIN navigation bar -->
<style>

#userpic{
color:white;
}
#userpic:hover{
   background-color:black;
   filter: invert(100%);
   color:white;
   
}
</style>

    <nav>
    <div class="logo lead font-weight-normal"><a href="adminhome.php"><img src="assets/logo.png"style="width:75px;filter:invert(10);padding-top:8px;"class="logoimg"></a></div>
    <label><input type="checkbox"id="click">
    <label for="click"class="menu-btn">
        <i class="fas fa-bars"></i>
    </label>
    <ul style="margin-bottom:27px;">
        <li> <a href="adminhome.php"class="lead">Admin Panel</a></li>
        <li> <a href="managemenu.php"class="lead">Products & Categories</a></li>
        <li> <a href="manageproducts.php"class="lead">Add Products</a></li>

        <li>
        <?php
    if(isset($_SESSION["pname"]))
    {   
        print'<a href="userprofile.php"class="lead"id="userpic">';
        print $_SESSION["pname"];
        print'&nbsp;&nbsp;<img src="assets/user-24.png"></a></li>';
    }
    
?>

</li>

</ul>
</nav>