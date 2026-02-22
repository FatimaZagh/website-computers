<?php session_start();?>
<html>
    <head>
        <title>
            Categories
        </title>

        <?php require_once("extfiles.php")?>
        <style>
            body{
                background-color:white;
                color:black;
            }

        .col-sm{
            margin:10px;
            background-color:white;
            border-style:solid;
            border-color:white;
            padding:10px;
            border-radius:4px;
            color:black;

        }
        .col-sm:hover >.prodlinks{
            color:white;
        }
        .col-sm:hover{
            border-style:solid;
            border-color:black;
            background-color:black;

            border-radius:4px;

        }
        .prodlinks{
            text-decoration:none;
            color:black;

        }
        .prodlinks:hover{
            text-decoration:none;
            color:black;
        }
       .col-sm:hover >a > img{
        transform: scale(1.08);
        transition: transform .2s;
       }
       @media (max-width:940px)
{
    .col-sm:hover >a > img{
        transform: scale(1);
    }

}


        </style>
    </head>
    <body>
        <?php require_once("header.php")?>
<h1 class="lead display-4"style="margin-top:100px;"align="center">Products
</h1>
<br><br><br>

<div class="container">
  <div class="row">

        <?php
        require_once("vars.php");
        $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
        $q="select *  from managecat";
        $res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
        $rowcount=mysqli_affected_rows($connection);
        mysqli_close($connection);
        if($rowcount==0)
        {
            print"<option>No Categories</option>";
        }
        else{
            while($resarr=mysqli_fetch_array($res))
            {
            print"<div class='col-sm'>
            <a href='showsubcat.php?catid=$resarr[0]'class='prodlinks'>
            <img src='uploads/$resarr[2]'style='width:300px;height:300px;object-fit:contain;'>
            <span class='lead'style='display:block;'>$resarr[1]
            </span></a>
          </div>";
            }
        }

        ?>

</div>
</div>

<!-- Recently Viewed Products Section -->
<div id="recently-viewed-section" class="recently-viewed-strip" style="display: none;">
    <div class="container">
        <h4 class="strip-title">
            <span><i class="fas fa-history"></i> Recently Viewed</span>
            <button class="clear-all-btn" onclick="recentlyViewedManager.clearRecentlyViewed()">Clear All</button>
        </h4>
        <div class="products-scroll-container">
            <div id="recently-viewed-products" class="products-scroll">
                <!-- Products will be loaded here by JavaScript -->
            </div>
        </div>
    </div>
</div>

<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>


        <?php require_once("footer.php")?>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>
    </body>
</html>