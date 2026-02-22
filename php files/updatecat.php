<html>
<head><title>Update Category</title>
<?php
ob_start();
session_start();
require_once("extfiles.php");
?>

<?php
$categoryid=$_GET["cid"];
require_once("vars.php");
$conn=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection " . mysqli_connect_error());

			$q="select * from managecat where catid='$categoryid'";
			
			$res=mysqli_query($conn,$q) or die("Error in query " . mysqli_error($conn));
			
			$resarr=mysqli_fetch_array($res);
			
			mysqli_close($conn);
?>
</head>
<body>
<?php
require_once("adminnavbar.php");
?>




		<div class="container" align="center"style="margin-top:100px;">
			<h2>Update Category</h2>
            <br><br><br><br>
			<div class="login-form-grids animated wow slideInUp" data-wow-delay=".5s">
				<form name="form1" method="post" enctype="multipart/form-data">
					<input type="text" value="<?php print $resarr[1];?>" name="catname" placeholder="Category Name" required=" " ><br/>
					<br><br>
					<?php print "<img src='uploads/$resarr[2]' height='100'>" ?>
					
					<br/><br/><br><br>
					<b>Choose new image, if required</b>
					<br/><br>
					<input type="file" name="catpic"><br><br><br>
					<input type="submit" name="btn" value="Update Category"class="btn btn-success">
				</form>
				
				<?php
				if(isset($_POST["btn"]))
				{
					$cname=$_POST["catname"];//it will fetch name of category from html to php. Name will be either new or old
				
					$errcode=$_FILES["catpic"]["error"];
					if($errcode==0)
					{
						$cpic=time().$_FILES["catpic"]["name"];
						$tname=$_FILES["catpic"]["tmp_name"];
						move_uploaded_file($tname,"uploads/$cpic");
					}
					else
					{
						$cpic=$resarr[2];
					}
					require_once("vars.php");
			$conn=mysqli_connect(dbhost,dbuname,dbpass,dbname) or die("Error in connection " . mysqli_connect_error());

					$q="update managecat set catname='$cname', catpic='$cpic' where catid='$categoryid'";
					
					mysqli_query($conn,$q) or die("Error in query " . mysqli_error($conn));
					
					$rescount=mysqli_affected_rows($conn);
					mysqli_close($conn);
					
					if($rescount==1)
					{
						header("location:catmng.php");
					}
					else
					{
						print "Category not updated successfully";
					}
				}
				
				?>
				
			</div><br/>
			
			
			
		</div>
		<br/>
			
	</div>
<?php
require_once("footer.php");
?>
</body>
</html>