<?php 
	
	session_start();
 	include "menu.txt";

/*Connect to database*/
	
$connection = mysqli_connect("mysql.itn.liu.se","lego","", "lego");
if (!$connection) 
			{
				die('MySQL connection error');
			}
			

	$keyword = $_GET['searchbox'];
	

/*Query for database*/

$bricks = mysqli_query($connection, "SELECT DISTINCT inventory.ItemID, inventory.ColorID, colors.Colorname, parts.Partname 
FROM inventory, parts, colors WHERE inventory.Extra='N' AND inventory.ItemTypeID='P' 
AND inventory.ItemID=parts.PartID AND inventory.ColorID=colors.ColorID 
AND (Partname LIKE '%$keyword%' OR PartID='$keyword') ORDER BY parts.Partname ASC");

/*Determine how many hits*/

/*No hits*/
if(mysqli_num_rows($bricks)==0)
{
	header("Location: noresult.php");
}

/*One hit*/
else if(mysqli_num_rows($bricks)==1)
{

		$found = $keyword;
		
		header("Location: brick_found.php?foundpart=$found");			

}
/*Many hits*/ 
else
{
	
		echo "<div class='header'>
		<h2 class='speechbubble_table'>We found multiple matches for your search '$keyword'.<br> Choose one to display the sets it appears in!</h2>
		</div>";
		
		echo "<div class='middlediv'>"; 
	
		/*Pagenation*/
		$recordsperpage = 20;

		$sql = "SELECT count(inventory.ItemID) FROM inventory";

		$returnvalue = mysqli_query($connection, $sql);

		if(! $returnvalue)
		{
			die('Could not get data: ' . mysqli_error());
		}

		if(isset($_GET['page']))
		{
			$page = $_GET['page']+1;
			$offset = $recordsperpage * $page;
		}
		else
		{
			$page = 0;
			$offset = 0;
		}
		
		
		$pagerow = mysqli_num_rows($bricks);

		

		$left_rec = $pagerow - ($page * $recordsperpage);
		
		$maxpage=0;
		
		if($pagerow%$recordsperpage==0)
		{
			$maxpage = $pagerow/$recordsperpage;
		}
		else if($pagerow%$recordsperpage!=0)
		{
			$maxpage = (($pagerow-($pagerow%$recordsperpage))/$recordsperpage)+1;
		}
			


		$returnvalue = mysqli_query($connection, $sql);

		if(! $returnvalue)
		{
			die('Could not get data: ' . mysqli_error());
		}
			
			$bricks = mysqli_query($connection, "SELECT DISTINCT inventory.ItemID, inventory.ColorID, colors.Colorname, parts.Partname 
			FROM inventory, parts, colors WHERE inventory.Extra='N' AND inventory.ItemTypeID='P' 
			AND inventory.ItemID=parts.PartID AND inventory.ColorID=colors.ColorID 
			AND (Partname LIKE '%$keyword%' OR PartID='$keyword') ORDER BY parts.Partname ASC 
			LIMIT $offset, $recordsperpage");
	
	print("<table class='displaytable'>\n<tr>");
		
		while($fieldinfo = mysqli_fetch_field($bricks))
		{
			print("<th>". $fieldinfo->name . "</th>");
		}
		
	print("<th>Images</th><th>Choose</th>");	
	
	print("</tr>\n");
		
		while($row = mysqli_fetch_array($bricks))
		{
			// Determine the file name for the small 80x60 pixels image, with a preference for JPG format.
			$prefix = "http://www.itn.liu.se/~stegu76/img.bricklink.com/";
			$ItemID = $row['ItemID'];
			$ColorID = $row['ColorID'];
			// Query the database to see which files, if any, are available
			$imagesearch = mysqli_query($connection, "SELECT * FROM images WHERE ItemTypeID='P' AND ItemID='$ItemID' AND ColorID='$ColorID'");
			// By design, the query above should return exactly one row.
			$imageinfo = mysqli_fetch_array($imagesearch);
			
			
			
			print("<tr>");
			
			for($i=0; $i<mysqli_num_fields($bricks); $i++)
			{
				print("<td><a href='brick_found.php?foundpart=$ItemID'>$row[$i]</a></td>");
	
			}
			
			if($imageinfo['has_jpg']) 
			{ 
				$filename = "P/$ColorID/$ItemID.jpg"; // Use JPG if it exists
			}
			else if($imageinfo['has_gif']) 
			{ 
				$filename = "P/$ColorID/$ItemID.gif"; // Use GIF if JPG is unavailable
			} 
			else 
			{ 
				$filename = "noimage_small.png"; // If neither format is available, insert a placeholder image
			}
			print("<td><form action='brick_found.php' method='get'><button type='submit' class='choosebutton' name='foundpart' value='$ItemID'><img src=\"$prefix$filename\" alt=\"Part $ItemID\"/></button></form></td>");
	
			print("<td class='last_col'><form action='brick_found.php' method='get'><button type='submit' class='choosebutton' name='foundpart' value='$ItemID'>
			<img src='images/choose_lego.png' alt='Choose' class='choose_img'></button></form></td>");
			
			
			
			print("</tr>\n");	
		}
		
		/*Pagefooter and pagebuttons*/
		
		echo "</table>";
		echo "</div>";
		echo "<div class='pagefooter'>";
		
		
			$keyword = urlencode($keyword);
				
			if ($page == 0 && $page == ($maxpage-1))
			{
				echo "<img class='pagebutton' src='images/empty.png' alt='previous'>";
				
				echo "<img  class='pagebutton' src='images/empty.png' alt='next'>";
			}
			else if($page > 0 && $page < ($maxpage-1))
			{
				$last = $page - 2;
				
				echo "<a href=\"$_PHP_SELF?searchbox=$keyword&page=$last\">
				<img class='pagebutton' src='images/prev.png' alt='previous'></a>";
				
				echo "<a href=\"$_PHP_SELF?searchbox=$keyword&page=$page\">
				<img  class='pagebutton' src='images/next.png' alt='next'>
				</a>";
			}
			else if ($page == 0)
			{
				echo "<img class='pagebutton' src='images/empty.png' alt='previous'>";
				
				echo "<a href=\"$_PHPSELF?searchbox=$keyword&page=$page\">
				<img  class='pagebutton' src='images/next.png' alt='next'></a>";
			}
			else if ($page == ($maxpage-1))
			{
				$last = $page - 2;
				
				echo "<a href=\"$_PHPSELF?searchbox=$keyword&page=$last\">
				<img class='pagebutton' src='images/prev.png' alt='previous'></a>";
				
				echo "<img  class='pagebutton' src='images/empty.png' alt='next'>";
			}
		echo "</div>";

}

		mysqli_close($connection);
		
		
?>


	</body>
	</html>
	
