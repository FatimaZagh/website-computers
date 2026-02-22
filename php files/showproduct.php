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

        .col-sm:hover{
            border-style:solid;
            border-color:black;
            background-color:white;
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


        </style>
    </head>
    <body>
        <?php require_once("header.php")?>
<h1 class="lead display-4"style="margin-top:100px;"align="center">
<?php
        $scatid=$_GET["subcatid"];
        require_once("vars.php");
        $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
        $q="select *  from subcat where subcatid=$scatid";
        $res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
        $rowcount=mysqli_affected_rows($connection);
        mysqli_close($connection);
        $resarr=mysqli_fetch_array($res);
        print $resarr[2];

        ?>
</h1>
<div class="container"style="margin-top:100px;">
<div class="row">


        <?php
        $scatid=$_GET["subcatid"];
        require_once("vars.php");
        $connection=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection".mysqli_connect_error());
        $q="select *  from manageproduct where subcatid=$scatid";
        $res=mysqli_query($connection,$q) or die("Error in query" . mysqli_error($connection));
        $rowcount=mysqli_affected_rows($connection);
        mysqli_close($connection);
        if($rowcount==0)
        {
            print"No Categories";
        }
        else{
            while($resarr=mysqli_fetch_array($res))
            {
                print"<div class='col-sm'>
                <a href='proddetails.php?pid=$resarr[2]'class='prodlinks'>
                <img src='uploads/$resarr[8]'style='width:300px;height:300px;object-fit:contain;'>
                <span class='lead'style='display:block;padding-top:10px;'>$resarr[3]<br><span class='text-success'> Price - $resarr[4]</span>
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

        <?php require_once("footer.php")?>

<!-- Recently Viewed Products JavaScript -->
<script src="js/recently-viewed.js"></script>
    </body>
</html>