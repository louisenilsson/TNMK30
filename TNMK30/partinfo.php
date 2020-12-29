<?php include "head.txt";?>
        <title>Part Information</title>
    </head>
    <body>
        <?php
        include "menu.php";

        //Hämta eventuella parametrer
        $ItemID = $_GET["ItemID"];
        $ColorID = $_GET["ColorID"];

        //Om man inte har valt någon bit
        if( (!$ItemID && !$ColorID) || (!$ItemID)) {
            die("<div class='tablediv'><p>Oops! Did you forget to choose an item?</p></div>");
        }
        $connection = mysqli_connect("mysql.itn.liu.se","lego","","lego");
        if (!$connection) { 
            die('MySQL connection error');
        }
        //Förhindra SQL injection
        $ItemID = mysqli_real_escape_string($connection,$ItemID);
        $ColorID = mysqli_real_escape_string($connection,$ColorID);

        //Hämta tillhörande set
        $sql_history = "SELECT collection.SetID, collection.Quantity, inventory.SetID, inventory.ItemID, inventory.ItemtypeID, inventory.ColorID, sets.SetID, sets.Year, sets.Setname
                        FROM collection, inventory, sets
                        WHERE inventory.ItemID = '$ItemID' AND inventory.ColorID = '$ColorID' AND inventory.SetID = sets.SetID AND sets.SetID = collection.SetID
        				GROUP BY Year, sets.SetID";
        //Hämta information för den biten man valt (banner)
        if (!$ColorID) {
        $sql_banner = " SELECT inventory.ItemID, inventory.ItemtypeID, images.ItemID, images.ItemtypeID, images.has_gif, images.has_jpg, minifigs.MinifigID, minifigs.Minifigname
                        FROM   inventory, images, minifigs
                        WHERE  inventory.ItemID = '$ItemID' AND inventory.ItemID = images.ItemID AND inventory.ItemtypeID = images.ItemtypeID AND inventory.ItemID = minifigs.MinifigID";
        }
        else {
        $sql_banner = " SELECT inventory.ItemID, inventory.ItemtypeID, inventory.ColorID, colors.ColorID, colors.Colorname, images.ColorID, images.has_gif, images.has_jpg, images.ItemID, images.ItemtypeID, parts.PartID, parts.Partname
                        FROM   inventory, images, parts, colors
                        WHERE  inventory.ItemtypeID = images.ItemtypeID AND inventory.ColorID = '$ColorID' AND inventory.ItemID = '$ItemID' AND inventory.ItemID = images.ItemID AND parts.PartID = inventory.ItemID AND inventory.ColorID = images.ColorID AND inventory.ColorID = colors.ColorID";
        }
        //Ta ut SQL information
        $item_history = mysqli_query($connection, $sql_history);
        $item_banner = mysqli_query($connection, $sql_banner);

        $items = mysqli_num_rows($item_history);

        //Printa banner
        $row_banner = mysqli_fetch_array($item_banner);
        $ItemID_banner = $row_banner['ItemID'];
        $ItemtypeID_banner = $row_banner['ItemtypeID'];
        $Partname_banner = $row_banner['Minifigname'];
        $gif_banner = $row_banner['has_gif'];
        $jpg_banner = $row_banner['has_jpg'];

        $prefix = "http://weber.itn.liu.se/~stegu76/img.bricklink.com/";
        //Om man har valt part eller minifig
        if ($ItemtypeID_banner == 'M') {
             $Partname_banner = $row_banner['Minifigname'];
             $filename_banner = "$ItemtypeID_banner/$ItemID_banner";
        } else {
            $Partname_banner = $row_banner['Partname'];
            $ColorID_banner = $row_banner['ColorID'];
            $Colorname_banner = $row_banner['Colorname'];
            $filename_banner = "$ItemtypeID_banner/$ColorID_banner/$ItemID_banner";
        }

        if ($gif_banner == 1) {
            $image_link_banner = "$filename_banner.gif";}
        else if ($jpg_banner == 1) {
            $image_link_banner = "$filename_banner.jpg";}

        print ("<div class='tablediv'>");
        print ("<div class='partinfo'>");
        print ("<img src=\"$prefix$image_link_banner\" alt=\"$ItemID\">");
        print ("<p>$Colorname_banner $Partname_banner - $filename_banner</p>");
        print ("</div>");

        //Se till så vi printar rätt ord
        if ( $ItemtypeID_banner == 'P') {
            print ("<p>This part can be found in $items different sets!</p>");
        }
        else {
            print ("<p>This minifigure can be found in $items different sets!</p>");
        }
        //Printa tabell
        print ("<table class='legotable'>\n<tr><th>Image</th><th>Quantity</th><th>ID</th><th>Release Year</th><th>Set Name</th></tr>");
        //Printa tillhörande set
        for($i = 0; $i < $items; ++$i) {
            $row = mysqli_fetch_array($item_history);
            $SetID = $row['SetID'];

            //Hämta bilder i en snabb fråga
            $sql_pics =  "SELECT images.has_gif, images.has_jpg, images.ItemID, images.ItemtypeID
                          FROM   images
                          WHERE  images.ItemtypeID = 'S' AND images.ItemID = '$SetID'";
            $content_pics = mysqli_query($connection, $sql_pics);
            $row_pics = mysqli_fetch_array($content_pics);

            $Quantity = $row['Quantity'];
            $Setname = $row['Setname'];
            $Year = $row['Year'];

            $gif = $row_pics['has_gif'];
            $jpg = $row_pics['has_jpg'];

            $filename = "S/$SetID";

            if ($gif == 1 || $largegif == 1) {
                $image_link = "$filename.gif";}
            else if ($jpg == 1 || $largegif == 1) {
                $image_link = "$filename.jpg";}

            print ("<tr>");
            if ($gif == 1 || $jpg ==1) {
                print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src=\"$prefix$image_link\" alt=\"$ItemID\"></a></td>");
            } else {
                print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src='icons/noimage.svg' alt='No_image_available'></a></td>");
            }   
            print ("<td><a target='_blank' class='setinfo' href='setinfo.php?SetID=$SetID'>$Quantity</a></td>");  
            print ("<td><a target='_blank' class='setinfo' href='setinfo.php?SetID=$SetID'>$filename</a></td>");
            print ("<td><a target='_blank' class='setinfo' href='setinfo.php?SetID=$SetID'>$Year</a></td>"); 
            print ("<td><a target='_blank' class='setinfo' href='setinfo.php?SetID=$SetID'>$Setname</a></td>");
            print ("</tr>");
        }

        print ("</table></div>");
        ?>
    </body>