<?php session_start();
ob_start(); 
if(!isset($_SESSION["pname"]))
{
    header("location:login.php");
}

?>
<html xmlns:cellspacing="http://www.w3.org/1999/xhtml">
<head>
<title>
    Login
</title>
<?php  
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
<h1 class="display-4"align="center">CHECKOUT</h1>
<form name="orderplace"method="post">
<table align="center"cellspacing:10;>
 <tr>
    <td><textarea placeholder="Enter Shipping Address"name="address"class="form-control"></textarea>
</td>
</tr>
<tr>
    <td><p><b>Payment Method - </b>Cash On Delivery</p></td>
</tr>
<tr align="center">
    <td><input type="submit"class="logbtn" value="Place Order"name="order"></td>
</tr>
<tr align="center">
    <td><hr align="center"color="black"></td>
</tr>
<tr align="center">
    <td><a href="showcat.php"class="forpass">Continue Shopping</a></td>
</tr>


<tr align="center">
    <td></td>
</tr>
</tr>    
</table>
</form>
</div>


<br><br>
<?php
ob_start(); 
if(isset($_POST["order"]))
{
    $address=$_POST["address"];
    $pmethod="Cash on Delivery";
    $username=$_SESSION["userprimid"];
	$billamount=$_SESSION["billamount"];
    date_default_timezone_set('Asia/Kolkata');
    $orderdate=date("Y-m-d h:i:sa");
	$status="Order Received";
    require_once("vars.php");
    $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
    $q="insert into ordertable(ShippingAddress,PaymentMethod,Username,OrderDate,BillAmount,Status) values('$address','$pmethod','$username','$orderdate','$billamount','$status')";
	mysqli_query($connection,$q) or die("Error in query " . mysqli_error($connection));
    $rescount=mysqli_affected_rows($connection);
	mysqli_close($connection);
	if($rescount==1)
		{
    		header("location:ordersummary.php");
		}
	else
		{
			print "Problem while processing payment, try again";
		}
		
}
?>




<br><br><br>

<?php  
    require_once("footer.php");
    ?>
</body>
</html>
