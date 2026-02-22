<html xmlns:cellspacing="http://www.w3.org/1999/xhtml">
<head>
<title>
    Login
</title>
<?php  
session_start();
ob_start(); 
require_once("extfiles.php");
?>
<style>
    
body{
    background-color:#fafafa;}
    ::placeholder{
      
        font-size:13px;
        background-color:#fafafa;
    }
    table tr td{
       padding-top:10px;
      
       padding-bottom:10px;
    }
    input{
        border-radius:4px;
        border-style:solid;
        padding:5px;
        
        
    }
    table{
        margin-top:40px;
    }
    .logbtn{
        background-color: #0095f6;
  border: none;
  color: white;
  padding-top: 10px;
  padding-bottom: 10px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  width:100%;
  border-radius:4px;
  cursor: pointer;
    }
    .logbtn:hover{
        background-color:#0086dd;
    }
    .signup{
        text-decoration:none;
    }
    .signup:hover{
        text-decoration:none;
    }
    .back{
    background-color:white;margin-left:600px;margin-right:600px;
    }
    .forpass{
        color:#204891;
        font-size:12px;
        

    }
    .forpass:hover{
        text-decoration:none;
        
    }
    @media (max-width:940px)
    {

    .back{
    margin-left:50px;
    margin-right:50px;
    }


    }

    </style>
</head>
<body style="background:#fafafa;">
    <?php  
    require_once("header.php");
    ?>
    <br><br><br>
    <div class="back">
<h1 class="display-4"align="center">Order Summary</h1>
<form name="login"method="post">
<table align="center"cellspacing:10;>
 <tr>
<td>
    <div class="container">
    <?php 
    require_once("vars.php");
    $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
        $username=$_SESSION["userprimid"];
    	$q="select * from ordertable where username='$username' order by orderdate desc limit 1";				
        $res=mysqli_query($connection,$q) or die("Error in query " . mysqli_error($connection));      
        $resarr=mysqli_fetch_array($res);//ordertable array
        print "Thanks for placing order on our website. Your order number is <a href='userorderdetails.php?oid=$resarr[0]'>$resarr[0]</a>"; //Showing Order Summary

        $un=$_SESSION["userprimid"];
        $q2="select * from cart where username='$username'";
        $res2=mysqli_query($connection,$q2) or die("Error in query " . mysqli_error($connection));
        while($resarr2=mysqli_fetch_array($res2))
        {
        $q3="insert into orderproducts(productid,prodpic,prodname,rate,qty,totalcost,orderno) values('$resarr2[1]','$resarr2[2]','$resarr2[3]','$resarr2[4]','$resarr2[5]','$resarr2[6]','$resarr[OrderID]')";
        mysqli_query($connection,$q3) or die("Error in query3 " . mysqli_error($connection));
        //update manageproduct set stock=10-2=8
        $q4="update manageproduct set stock=stock-$resarr2[5] where productid='$resarr2[1]'";
        mysqli_query($connection,$q4) or die("Error in query 4" . mysqli_error($connection));
        }
        $q5="delete from cart where username='$un'";
        $un=$_SESSION["userprimid"];	
        mysqli_query($connection,$q5);
        mysqli_close($connection);
        


    ?>
    </div>
</td>
</tr>
<tr align="center">
    <td><hr align="center"color="black"></td>
</tr>
<tr align="center">
    <td><a href="showcat.php"class="forpass">Shop More</a></td>
</tr>


<tr align="center">
    <td></td>
</tr>
</tr>    
</table>
</form>
</div>


<br><br>



<br><br><br>

<?php  
    require_once("footer.php");
    ?>
</body>
</html>
