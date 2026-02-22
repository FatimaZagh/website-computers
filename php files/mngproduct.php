<?php session_start();?>
<html>
<head>
<title>
    Manage Products
</title>
</head>
<style>
 body{
     background-size: contain;
     background: url(assets/adminpr.jpg) no-repeat fixed;
     color:black;
 }
    .lnkbtn{
        border-style:solid;
        border-color:black;
        border-radius:5px;
        margin-top:15px;
        background-color:black;
        padding:10px;
        color:white;
        display:block;
        width:20%;
    }
    .lnkbtn:hover{
        background-color:white;
        border-color:white;
        color:black;
        border-radius:5px;
        text-decoration:none;
        transition:0.4s ease;
    }
    #acc:hover{
        color:white;
        text-shadow: 0 0 2px white;
    }
    @media (max-width:940px)
    {
    .lnkbtn{
        width:50%;
    }
}
    @media only screen and (max-width: 767px)
    {
        body {
          background-size:cover;
          background-position:50% 100%;
        }

    }
    </style>
<?php
require_once("extfiles.php");
?>
<body class="bg">

<!-- Admin Navigation Bar -->
<?php
require_once("adminnavbar.php");
?>

<!-- Manage Products -->
<br><br><br><br>
<div class="container lead"align="center">
    <h1 class="display-4"align="center">Manage Products & Categories</h1>
    <br>Admin can Manage All the Products & Categories From Here : <br><br>
    <a href="catmng.php"class="lnkbtn">View/Manage Categories</a><br>
    <a href="subcatmang.php"class="lnkbtn">View/Manage Sub-Categories</a><br>
    <a href="mngproduct.php"class="lnkbtn">Manage Products</a><br>
    <a href="#"class="lnkbtn">Add/Remove Admin</a><br>

</div>



<br><br><br><br>
<!-- Footer -->
<?php
require_once("footer.php");
?>
</body>
</html>
