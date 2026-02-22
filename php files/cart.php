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
    include_once("header.php");
    ?>
<body>
<br><br><br><br>

<div class="container"syle="margin-top:100px;">
<h1 class="display-4"align="center">Shopping Cart</h1></div>
<br><br><br><br>
<div class="container">
<div class="row">


        <?php

        $username=$_SESSION["userprimid"];
        require_once("vars.php");
        $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());

        // Check if color columns exist in cart table
        $check_columns = "SHOW COLUMNS FROM cart LIKE 'selected_color_id'";
        $column_result = mysqli_query($connection, $check_columns);
        $has_color_columns = mysqli_num_rows($column_result) > 0;

        if ($has_color_columns) {
            $q="SELECT *, selected_color_id, selected_color_name, selected_color_code FROM cart WHERE UserName='$username'";
        } else {
            $q="SELECT * FROM cart WHERE UserName='$username'";
        }
        $res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
        $rowcount=mysqli_affected_rows($connection);
        mysqli_close($connection);
        if($rowcount==0)
        {
            print"No items in Your Cart &nbsp; <a href='showcat.php'>Shop Here</a>";
        }
        else{
            $grandtotal=0;
            while($resarr=mysqli_fetch_array($res))
            {
            $grandtotal=$resarr[6]+$grandtotal;
            // Get color information for display (only if columns exist)
            $color_display = '';
            if ($has_color_columns && !empty($resarr['selected_color_name'])) {
                $color_code = $resarr['selected_color_code'] ?? '#000000';
                $color_display = "
                    <div style='display: flex; align-items: center; gap: 8px; justify-content: center; margin-top: 5px;'>
                        <div style='width: 16px; height: 16px; border-radius: 50%; background: $color_code; border: 1px solid #ccc;'></div>
                        <span style='font-size: 0.9em; color: #666;'>{$resarr['selected_color_name']}</span>
                    </div>";
            }

            print"<table style='text-align:center;'class='carttable'>
            <thead>
              <tr>
                <th scope='col'>Product Picture</th>
                <th scope='col'>Product Details</th>
                <th scope='col'>Quantity</th>
                <th scope='col'>Rate</th>
                <th scope='col'>Total Cost</th>
                <th scope='col'>Action</th>
              </tr>
            </thead>
            <tbody>
    <tr>
      <td><img src='uploads/$resarr[2]' class='cartimg'></td>
      <td>
        <div style='font-weight: 600; color: #2d3748;'>$resarr[3]</div>
        $color_display
      </td>
      <td>$resarr[5]</td>
      <td>₪$resarr[4]</td>
      <td style='font-weight: 600; color: #48bb78;'>₪$resarr[6]</td>
      <td>
        <a href='delcartitem.php?cartid=$resarr[0]'
           style='color: #f56565; text-decoration: none; font-weight: 500;'
           onclick='return confirm(\"Are you sure you want to remove this item?\")'>
           <i class='fas fa-trash'></i> Remove
        </a>
      </td>
    </tr></table></div></div><br><br>
   ";
   print "<div align='center' style='font-size: 1.2em; font-weight: 600; color: #2d3748;'>
            Grand Total - <span style='color: #48bb78;'>₪$grandtotal</span>
          </div> <br><br>";
   $_SESSION["billamount"]=$grandtotal;
   print"<div align='center'><a href='checkout.php'class='btn btn-success'>Checkout</a></div><br>";

            }

        }



        ?>

<br><br>

<br>

</div>  </div>


<br><br><br>
<?php
    include_once("footer.php");
    ?>
</body>
</html>
