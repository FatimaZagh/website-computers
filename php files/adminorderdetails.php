<?php session_start();
ob_start(); 
if(!isset($_SESSION["pname"]))
{
    header("location:login.php");
}

?>
<html>
<head>
    <title>
        Cart
    </title>
    </head>
    <?php
    include_once("extfiles.php");
    ?>
    <style>
        .col-sm:hover{
            color:black;
            background-color:white;
        }
        .prodlinks{
            text-decoration:none;
            color:black;
        }
        .prodlinks:hover{
            text-decoration:none;
            color:black;
        }

        .carttable{
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
            table-layout: fixed;
           
        }
        .carttable , tr,td{
            overflow:hidden;
        }
        .cartimg{
            width:100px;
            height:100px;
            object-fit:contain;
        }
        @media (max-width:940px)
{
    .cartimg{
        width:50px;
        height:50px;
        object-fit:contain;
    }
}
       
        </style>
</head>
<?php
    include_once("adminnavbar.php");
    ?>
<body>
<br><br><br><br>

<div class="container"syle="margin-top:100px;">
<h1 class="display-4"align="center">Order Details</h1></div>
<br><br><br><br>
<div class="container">
        
    
        <?php
       $orderid=$_GET["oid"];
        $username=$_SESSION["userprimid"];
        require_once("vars.php");
        $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
        $q="select * from orderproducts where orderno='$orderid'";
			$res=mysqli_query($connection,$q) or die("Error in query " . mysqli_error($connection));
			$rescount=mysqli_affected_rows($connection);
			if($rescount==0)
			{
				print "No details found";
			}
			else
			{
				print "<table class='memtble'align='center'>
				<tr>
					<th>Picture</th>
					<th>Name</th>
					<th>Rate</th>
					<th>Quantity</th>
					<th>Total Cost</th>
				</tr>";
				while($resarr=mysqli_fetch_array($res))
				{				
					print "
					<tr>
					<td><img src='uploads/$resarr[2]' height='75'></td>
					<td>$resarr[3]</td>
					<td>$resarr[4]</td>
					<td>$resarr[5]</td>
					<td>$resarr[6]</td>
					</tr>";
				}
				print "</table><br/>";
				
         
        }
   
    
        
        ?>
        
</div><br><br>
<div align="center">
<span class="lead"align="center"style="background-color:#fafafa;padding:10px;">
</span></div>
<br>



<br><br><br>
<?php
    include_once("footer.php");
    ?>
</body>
</html>
