<?php include "head.txt";?>
		<title>Lego Collection Database</title>
	</head>
	<body>
		<?php
		$show_search = '1'; //Bestämma om sökrutan ska synas

		include "menu.php";

		//Hämta eventuella parametrer
		$search = $_GET["search"];
		$ItemtypeID = $_GET["ItemtypeID"];

		if (isset($_GET["page"])) {
			$page = $_GET["page"];
		}
		else {
			$page = 1;
		}

		//Sökfunktioner
		$trim_search= trim($search," ");
		$add_spaces = preg_replace('/(?<=[a-z])(?=\d)|(?<=\d)(?=[a-z])/i', ' ', $trim_search);
		
		$connection	= mysqli_connect("mysql.itn.liu.se","lego","","lego");
		if (!$connection) { 
			die('MySQL connection error');
		}
		//Förhindra SQL injection
		$ItemtypeID = mysqli_real_escape_string($connection,$ItemtypeID);
		$search = mysqli_real_escape_string($connection,$search);
		$page = mysqli_real_escape_string($connection,$page);

		//Sidouppdelning
		if (($_GET['filter'] == 'Parts') || ($_GET['filter'] == 'Minifigs') || (isset($_GET['search']) == 1)) {
			$num_results = 150;
		} else {
			$num_results = 75;
		}		
		$start = ($page-1) * $num_results;

		//Visa titel/bakgrund om man är på första sidan och inte köra mer kod än nödvändigt
		if( (isset($_GET['filter']) == 0) && (isset($_GET['search']) == 0)) {
			print ("<div class='tablediv'>
						<h1 id='headline'>Collector Database</h1>
						<img id='legofigs' src='icons/legofigs.png' alt='legofigs.png'>
					</div>");
			die();
		}
		//Om man har valt filter
		if (!$search || $trim_search == '') {
			switch ($_GET['filter']){
				case "Parts":
					$sql_contents_parts = "SELECT inventory.ItemID, inventory.ColorID, collection.SetID, inventory.SetID, inventory.ItemtypeID, parts.PartID, parts.Partname, colors.ColorID, images.ColorID, images.ItemID, itemtypes.ItemtypeID, images.ItemtypeID, images.has_gif, images.has_jpg, sum(collection.Quantity * inventory.Quantity) AS amount
								FROM collection, inventory, parts, colors, images, itemtypes
								WHERE collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND inventory.ColorID = colors.ColorID AND inventory.ItemID = images.ItemID AND colors.ColorID = images.ColorID AND itemtypes.ItemtypeID = inventory.ItemtypeID AND itemtypes.ItemtypeID = images.ItemtypeID
								GROUP BY inventory.ItemID, inventory.ColorID
								ORDER BY amount DESC LIMIT $start, $num_results";

					$sql_tot_parts = "SELECT COUNT(*)
						FROM collection, inventory, parts, colors, images, itemtypes
						WHERE collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND inventory.ColorID = colors.ColorID AND inventory.ItemID = images.ItemID AND colors.ColorID = images.ColorID AND itemtypes.ItemtypeID = inventory.ItemtypeID AND itemtypes.ItemtypeID = images.ItemtypeID
						GROUP BY inventory.ItemID, inventory.ColorID";
					break;
					
		 		case "Minifigs":
					$sql_contents_figs = "SELECT inventory.ItemID, inventory.ColorID, collection.SetID, inventory.SetID, inventory.ItemtypeID, minifigs.MinifigID, minifigs.Minifigname, images.ItemID,	itemtypes.ItemtypeID, images.ItemtypeID, images.has_gif, images.has_jpg, sum(collection.Quantity * inventory.Quantity) AS amount
								FROM collection, inventory, minifigs, images, itemtypes
								WHERE collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID AND itemtypes.ItemtypeID = images.ItemtypeID AND itemtypes.ItemtypeID = inventory.ItemtypeID
								GROUP BY inventory.ItemID, inventory.ColorID
								ORDER BY amount DESC LIMIT $start, $num_results";

					$sql_tot_figs = "SELECT COUNT(*)
						FROM collection, inventory, minifigs, images, itemtypes
						WHERE  collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID AND itemtypes.ItemtypeID = images.ItemtypeID AND itemtypes.ItemtypeID = inventory.ItemtypeID
						GROUP BY inventory.ItemID, inventory.ColorID";
					break;
					
				case "Show all":
					//Alla parts
					$sql_contents_parts = "SELECT inventory.ItemID, inventory.ColorID, collection.SetID, inventory.SetID, inventory.ItemtypeID, parts.PartID, parts.Partname, colors.ColorID, images.ColorID, images.ItemID, itemtypes.ItemtypeID, images.ItemtypeID, images.has_gif, images.has_jpg, sum(collection.Quantity * inventory.Quantity) AS amount
									FROM collection, inventory, parts, colors, images, itemtypes
									WHERE collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND inventory.ColorID = colors.ColorID AND inventory.ItemID = images.ItemID AND colors.ColorID = images.ColorID AND itemtypes.ItemtypeID = inventory.ItemtypeID AND itemtypes.ItemtypeID = images.ItemtypeID
									GROUP BY inventory.ItemID, inventory.ColorID
									ORDER BY amount DESC LIMIT $start, $num_results";
					//Räkna parts
					$sql_tot_parts = "SELECT COUNT(*)
									FROM collection, inventory, parts, colors, images, itemtypes
									WHERE collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND inventory.ColorID = colors.ColorID AND inventory.ItemID = images.ItemID AND colors.ColorID = images.ColorID AND itemtypes.ItemtypeID = inventory.ItemtypeID AND itemtypes.ItemtypeID = images.ItemtypeID
									GROUP BY inventory.ItemID, inventory.ColorID";
					//Alla minifigs
					$sql_contents_figs = "SELECT inventory.ItemID, inventory.ColorID, collection.SetID, inventory.SetID, inventory.ItemtypeID, minifigs.MinifigID, minifigs.Minifigname, images.ItemID,	itemtypes.ItemtypeID, images.ItemtypeID, images.has_gif, images.has_jpg, sum(collection.Quantity * inventory.Quantity) AS amount
									FROM collection, inventory, minifigs, images, itemtypes
									WHERE  collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID AND itemtypes.ItemtypeID = images.ItemtypeID AND itemtypes.ItemtypeID = inventory.ItemtypeID
									GROUP BY inventory.ItemID, inventory.ColorID
									ORDER BY amount DESC LIMIT $start, $num_results";
					//Räkna minifigs
					$sql_tot_figs = "SELECT COUNT(*)
									FROM collection, inventory, minifigs, images, itemtypes
									WHERE  collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID AND itemtypes.ItemtypeID = images.ItemtypeID AND itemtypes.ItemtypeID = inventory.ItemtypeID
									GROUP BY inventory.ItemID, inventory.ColorID";
					break;
			}
		}
		//Om man har sökt
		else {
			$sql_contents_parts = "SELECT inventory.ItemID, inventory.ColorID, collection.SetID, inventory.SetID, inventory.ItemtypeID, parts.PartID, parts.Partname, colors.ColorID, images.ColorID, images.ItemID, itemtypes.ItemtypeID, 									  images.ItemtypeID, images.has_gif, images.has_jpg, sum(collection.Quantity * inventory.Quantity) AS amount
								   FROM collection, inventory, parts, colors, images, itemtypes
								   WHERE Partname LIKE '%{$add_spaces}%' AND collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND inventory.ColorID = colors.ColorID AND inventory.ItemID = images.ItemID AND colors.ColorID = images.ColorID AND itemtypes.ItemtypeID = inventory.ItemtypeID AND itemtypes.ItemtypeID = images.ItemtypeID
								   GROUP BY inventory.ItemID, inventory.ColorID
								   ORDER BY Partname DESC LIMIT $start, $num_results";

			$sql_tot_parts = "SELECT COUNT(*)
							  FROM collection, inventory, parts, colors, images, itemtypes
							  WHERE Partname LIKE '%{$add_spaces}%' AND collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND inventory.ColorID = colors.ColorID AND inventory.ItemID = images.ItemID AND colors.ColorID = images.ColorID AND itemtypes.ItemtypeID = inventory.ItemtypeID AND itemtypes.ItemtypeID = images.ItemtypeID
							  GROUP BY inventory.ItemID, inventory.ColorID
							  ORDER BY Partname";

			$sql_contents_figs = "SELECT inventory.ItemID, inventory.ColorID, collection.SetID, inventory.SetID, inventory.ItemtypeID, minifigs.MinifigID, minifigs.Minifigname, images.ItemID,	itemtypes.ItemtypeID, images.ItemtypeID,									 images.has_gif, images.has_jpg, sum(collection.Quantity * inventory.Quantity) AS amount
								  FROM collection, inventory, minifigs, images, itemtypes
								  WHERE Minifigname LIKE '%{$add_spaces}%' AND collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID AND itemtypes.ItemtypeID = images.ItemtypeID AND itemtypes.ItemtypeID = inventory.ItemtypeID
								  GROUP BY inventory.ItemID, inventory.ColorID
								  ORDER BY Minifigname DESC LIMIT $start, $num_results";

			$sql_tot_figs = "SELECT COUNT(*)
							 FROM collection, inventory, minifigs, images, itemtypes
							 WHERE Minifigname LIKE '%{$add_spaces}%' AND collection.SetID = inventory.SetID AND inventory.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID AND itemtypes.ItemtypeID = images.ItemtypeID AND itemtypes.ItemtypeID = inventory.ItemtypeID
							 GROUP BY inventory.ItemID, inventory.ColorID
							 ORDER BY Minifigname";
		}

		//Ta ut SQL information och lite räkning
		$contents_parts = mysqli_query($connection, $sql_contents_parts);
		$contents_figs = mysqli_query($connection, $sql_contents_figs);
		$tot_parts = mysqli_query($connection, $sql_tot_parts);
		$tot_figs = mysqli_query($connection, $sql_tot_figs);

		$part_items = mysqli_num_rows($tot_parts);
		$figs_items = mysqli_num_rows($tot_figs);

		$parts_on_page = mysqli_num_rows($contents_parts);
		$figs_on_page  = mysqli_num_rows($contents_figs);

		$items = $part_items + $figs_items;	
		
		//Funktion för att printa sidonummer
		function pagesRow($tot_pages, $howMany, $num_results) {
		print("<div class='pagesrow'>");
			if($_GET['filter'] == 'Parts') {
				$tot_pages = ceil($howMany / $num_results);
				for($i=1; $i <= $tot_pages; ++$i) {			
					echo "<a href='home.php?filter=Parts&page=$i'><div class='pagebutton' title='Page $i'>$i</div></a>";}
			}
			else if ($_GET['filter'] == 'Minifigs') {
				$tot_pages = ceil($howMany / $num_results);
				for($i=1; $i <= $tot_pages; ++$i) {			
					echo "<a href='home.php?filter=Minifigs&page=$i'><div class='pagebutton' title='Page $i'>$i</div></a>";}
			}
			else if (isset($_GET['search']) == 1) {
				$tot_pages = ceil($howMany / $num_results);
				for($i=1; $i <= $tot_pages; ++$i) {
					echo "<a href='home.php?search=$search&page=$i'><div class='pagebutton' title='Page $i'>$i</div></a>";}
			}
			else {			
				$tot_pages = ceil($howMany / $num_results);
				for($i=1; $i <= $tot_pages; ++$i) {
					echo "<a href='home.php?filter=Show+all&page=$i'><div class='pagebutton' title='Page $i'>$i</div></a>";}
			}
			print("</div>");
		}

		//Printa tabell header
	    if((isset($_GET['filter']) == 1) || (isset($_GET['search']) == 1)) {
			print ("<div class='tablediv'>");
				if($items > 0) {
					if($_GET['filter'] == 'Parts') {
						print("<p>Displaying $parts_on_page parts of $part_items.</p>");
						$howMany = $part_items;
					}
					else if ($_GET['filter'] == 'Minifigs') {
			    		print("<p>Displaying $figs_on_page minifigures of $figs_items.</p>");
			    		$howMany = $figs_items;
			    	}
			    	else if (isset($_GET['search']) == 1) {
			    		print ("<p>Found $items results!</p>");
			    		print("<p>Displaying $parts_on_page parts of $part_items.</p>");
			    		print("<p>Displaying $figs_on_page minifigures of $figs_items.</p>");
			    		$howMany = $part_items;
			    	}
			    	else {
			    		print("<p>Displaying $parts_on_page parts of $part_items.</p>");
			    		print("<p>Displaying $figs_on_page minifigures of $figs_items.</p>");
			    		$howMany = $part_items;
			    	}
					pagesRow($tot_pages, $howMany, $num_results);
					print ("<table class='legotable'><tr><th>Image</th><th>Quantity</th><th>ID</th><th>Part name</th></tr>");
				}
				else {
					print ("<p>No match found! Please check your spelling and try again.</p>");
				}
		}

		$prefix = "http://weber.itn.liu.se/~stegu76/img.bricklink.com/";

		//Printa tabell parts
		for ($i = 0; $i < $num_results && $row = mysqli_fetch_array($contents_parts); ++$i) {	
			//Bitar
			$Quantity = $row['amount'];
			$SetID = $row['SetID'];
			$ItemtypeID = $row['ItemtypeID'];
			$ItemID = $row['ItemID'];
			$ColorID = $row['ColorID'];
			$filename = "$ItemtypeID/$ColorID/$ItemID";	
			//Namn
			$Partname = $row['Partname'];	
			//Bilder
			$gif = $row['has_gif'];
			$jpg = $row['has_jpg'];

			if ($gif == 1) {
				$image_link = "$filename.gif";}
			else if ($jpg == 1) {
				$image_link = "$filename.jpg";}
			
			print ("<tr title='Click to display more information'>");
			if ($gif == 1 || $jpg ==1) {
				print("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src=\"$prefix$image_link\" alt=\"$ItemID\"></a></td>");
			} else {
				print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src='icons/noimage.svg' alt='No_image_available'></a></td>");
			}
			print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Quantity</a></td>");
			print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$filename</a></td>");	
			print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Partname</a></td>");
			print ("</tr>");
		}
		//Printa tabell minifigs
		for ($i = 0; $i < $num_results && $row = mysqli_fetch_array($contents_figs); ++$i) {
			//Bitar
			$Quantity = $row['amount'];
			$SetID = $row['SetID'];
			$ItemtypeID = $row['ItemtypeID'];
			$ItemID = $row['ItemID'];
			$filename = "$ItemtypeID/$ItemID";			
			//Namn
			$Partname = $row['Minifigname'];			
			//Bilder
			$gif = $row['has_gif'];
			$jpg = $row['has_jpg'];

			if ($gif == 1) {
				$image_link = "$filename.gif";}
			else if ($jpg == 1) {
				$image_link = "$filename.jpg";}
			
			print ("<tr title='Click for more information'>");			
			if ($gif == 1 || $jpg ==1) {
				print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src=\"$prefix$image_link\" alt=\"$ItemID\"></a></td>");
			} else {
				print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src='icons/noimage.svg' alt='No_image_available'></a></td>");
			}				
			print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Quantity</a></td>");
			print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$filename</a></td>");	
			print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Partname</a></td>");
			print ("</tr>");
		}
		//Stänga HTML taggar i de fall det behövs och printa en ytterligare pagesRow
		if ((isset($_GET['filter']) == 1) || (isset($_GET['search']) == 1)) {
			print ("</table>");
			pagesRow($tot_pages, $howMany, $num_results);
			print ("</div>");
		}
		?>
	</body>
</html>