<?php include "head.txt";?>
        <title>Set Information</title>
    </head>
    <body>
        <?php
        include "menu.php";

        //Hämta eventuella parametrer
        $SetID = $_GET["SetID"];

        //Om man inte har valt någon bit
        if(!$SetID) {
            die("<div class='tablediv'><p>Oops! Did you forget to choose a set?</p></div>");
        }

        $connection = mysqli_connect("mysql.itn.liu.se","lego","","lego");
        if (!$connection) { 
            die("<div class='tablediv'><p>MySQL connection error</p></div>");
        }
        //Förhindra SQL injection
        $SetID = mysqli_real_escape_string($connection,$SetID);

        //Hämta alla parts
        $sql_set_parts = "SELECT inventory.SetID, inventory.ItemID, inventory.ItemtypeID, inventory.ColorID, inventory.Quantity, parts.PartID, parts.Partname, images.ColorID, images.ItemID, images.has_gif, images.has_jpg
        			      FROM   inventory, parts, images
        			      WHERE  inventory.SetID = '$SetID' AND inventory.ItemtypeID = 'P' AND images.ItemtypeID = 'P' AND inventory.ItemID = parts.PartID AND images.ItemID = inventory.ItemID AND inventory.ColorID = images.ColorID
                          ORDER BY inventory.Quantity DESC";
        //Hämta alla minifigs
        $sql_set_figs = "SELECT inventory.SetID, inventory.ItemID, inventory.ItemtypeID, inventory.Quantity, minifigs.MinifigID, minifigs.Minifigname, images.ItemID, images.has_gif, images.has_jpg
                         FROM   inventory, minifigs, images
                         WHERE  inventory.SetID = '$SetID' AND inventory.ItemtypeID = 'M' AND images.ItemtypeID = 'M' AND inventory.ItemID = minifigs.MinifigID AND images.ItemID = inventory.ItemID
                         ORDER BY inventory.Quantity DESC";
        //Hämta information för det set man valt (banner)
        $sql_banner ="SELECT images.has_gif, images.has_jpg, images.ItemID, images.ItemtypeID, sets.SetID, sets.Setname, sets.Year
                      FROM   images, sets
                      WHERE  images.ItemtypeID = 'S' AND images.ItemID = '$SetID' AND sets.SetID = '$SetID'";

        $items_tot_set = mysqli_query($connection, $sql_set_parts);
        $items_tot_figs= mysqli_query($connection, $sql_set_figs);

        $num_figs = mysqli_num_rows($items_tot_figs);
        $num_parts= mysqli_num_rows($items_tot_set);
        $items_tot = $num_figs + $num_parts;

        $prefix = "http://weber.itn.liu.se/~stegu76/img.bricklink.com/";
        
        //Printa banner
        $banner_picture = mysqli_query($connection, $sql_banner);
        $row_banner = mysqli_fetch_array($banner_picture);
        $gif_banner = $row_banner['has_gif'];
        $jpg_banner = $row_banner['has_jpg'];
        $ItemID_banner = $row_banner['ItemID'];
        $ItemtypeID_banner = $row_banner['ItemtypeID'];

        $Setname = $row_banner['Setname'];
        $Year = $row_banner['Year'];
        $filename_banner = "$ItemtypeID_banner/$ColorID_banner/$ItemID_banner";

        if ($jpg_banner == 1) {
            $banner_image_link = "$filename_banner.jpg";}
        else if ($gif_banner == 1) {
            $banner_image_link = "$filename_banner.gif";}

        print ("<div class='tablediv'>");
        print ("<div class='partinfo'>");
        print ("<img src=\"$prefix$banner_image_link\" alt=\"$ItemID\">");
        print ("<p>$Setname - $Year</p>");
        print ("</div>");
        print ("<p>This set contains $num_parts parts and $num_figs minifigures!</p>");

        //Printa tabell
        print ("<table class='legotable'>\n<tr><th>Image</th><th>Quantity</th><th>ID</th><th>Part name</th></tr>");
        
        //Loop för att printa parts
        for($i = 0; $i < $num_parts && $row = mysqli_fetch_array($items_tot_set); ++$i) {
            $Partname = $row['Partname'];
            $Quantity = $row['Quantity'];
            $ItemtypeID = $row['ItemtypeID'];
            $ItemID = $row['ItemID'];
            $ColorID = $row['ColorID'];
            $filename = "$ItemtypeID/$ColorID/$ItemID";
            $gif = $row['has_gif'];
            $jpg = $row['has_jpg'];

            if ($gif == 1) {
                $image_link = "$filename.gif";}
            else if ($jpg == 1) {
                $image_link = "$filename.jpg";}  

            print ("<tr>");
            if ($gif == 1 || $jpg == 1) {
                print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src=\"$prefix$image_link\" alt=\"$ItemID\"></a></td>");
            } else {
                print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src='icons/noimage.svg' alt='No_image_available'></a></td>");
            }
            print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Quantity</a></td>");
            print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$filename</a></td>");
            print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Partname</a></td>");
            print ("</tr>");
        }
        //Loop för att printa figurer
        for($i = 0; $i < $num_figs && $row = mysqli_fetch_array($items_tot_figs); ++$i) {
            $Partname = $row['Minifigname'];
            $Quantity = $row['Quantity'];
            $ItemtypeID = $row['ItemtypeID'];
            $ItemID = $row['ItemID'];
            $filename = "$ItemtypeID/$ItemID";
            $gif = $row['has_gif'];
            $jpg = $row['has_jpg'];        

            if ($gif == 1) {
                $image_link = "$filename.gif";}
            else if ($jpg == 1) {
                $image_link = "$filename.jpg";}

            print ("<tr>");
            if ($gif == 1 || $jpg == 1) {
                print("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src=\"$prefix$image_link\" alt=\"$ItemID\"></a></td>");
            }
            else {
                print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'><img src='https://static.independent.co.uk/static-assets/close-video-preroll.svg' alt='No_image_available'></a></td>");
            }

            print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Quantity</a></td>");
            print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$filename</a></td>");
            print ("<td><a target='_blank' class='historylink' href='partinfo.php?ItemID=$ItemID&ColorID=$ColorID'>$Partname</a></td>");
            print ("</tr>");
        }
        
        print ("</table></div>");
    ?>