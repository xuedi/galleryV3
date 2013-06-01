<?php
/*
    Copyright 2013 xuedi

    This file is part of 'GalleryV3'.

    GalleryV3 is free software: you can redistribute it and/or modify it under the terms
    of the GNU General Public License as published by the Free Software Foundation, either
    version 3 of the License, or (at your option) any later version.

    GalleryV3 is distributed in the hope that it will be useful, but WITHOUT ANY
    WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
    A PARTICULAR PURPOSE. See the GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with GalleryV3. If not, see http://www.gnu.org/licenses/.
*/
require_once("include/config.php");
require_once("include/functions.php");
?>

<html>
<head>
	<title>galleryV3 - admin</title>
	<script type="text/javascript" src="static/js/jquery-1.8.1.min.js"></script>
	<link rel="stylesheet" href="static/css/style.css" type="text/css" />
	<link rel="stylesheet" href="static/css/slimbox2.css" type="text/css" />
</head>
<body>
	<div id="base">
		<div class="menu_container menu_admin">
		<h1 class="admin">gallery - admin</h1>
		<div id="menue_elemnts">
			<li class='menue_item admin'>Show</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?show=all'>all</a>
				<div class='menue_item_admin'>shows entire picture list</div>
			</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?show=missingThumbs'>missing thumbs</a>
				<div class='menue_item_admin'>missing: (<?php echo countMissing(); ?>)</div>
			</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?show=size'>sorted by size</a>
				<div class='menue_item_admin'>biggest first</div>
			</li>
			<li class='menue_item admin'>Actions</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?action=genMenue'>genMenue</a>
				<div class='menue_item_admin'>updates the menue</div>
			</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?action=genJson'>genJson</a>
				<div class='menue_item_admin'>generates the gallery ajax requests</div>
			</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?action=genThumnails'>genThumnails</a>
				<div class='menue_item_admin'>can also be run as cronjop</div>
			</li>
			<li class='menue_item'>
				<img src='static/images/sub.png' />
				<a class='menue_link' href='admin.php?action=updateFileIndex'>updateFileIndex</a>
				<div class='menue_item_admin'>is needed for thumb creation</div>
			</li>
		</div>
		</div>
		<div id="gallery">
			<h2 id="gallery_name">Name</h2>
			<div>
<?php

switch($_REQUEST["action"]) {
    case "genMenue":
        file_put_contents("cache/menu.html", genMenu(genInit("galleries")) );
        break;
    case "genJson":
        genJson(genInit("galleries",true));
        break;
    case "genThumnails":
        genThumnails();
        break;
    case "updateFileIndex":
        saveFileIndex(updateFileIndex(genInit("galleries",true)));
        break;
    default:
	    $data = unserialize(file_get_contents("cache/fileIndex.array"));
    	$showall=false;

    	if($_REQUEST["show"]=="all") 
    		$showall=true;

		if($_REQUEST["show"]=="size") { 
			usort($data, function($a, $b) { // needs php 5.3+
			    return $b['size'] - $a['size'];
			});
			$showall=true;
		}

	    echo "<table border=1>";
	    echo "  <tr>";
	    echo "    <td>cnt</td>";
	    echo "    <td>Name</td>";
	    echo "    <td>".thumbSize."</td>";
	    echo "    <td>".imageSize."</td>";
	    echo "    <td>Size</td>";
	    echo "    <td>Hash</td>";
	    echo "  </tr>";
	    $cnt = 0;
	    foreach($data as $key => $value) {
	    	
	    	// sort missing
	    	if($_REQUEST["show"]=="missingThumbs") {
	    		if(!$value['thumbExist']||!$value['imageExist'])
	    			$missing = true;
	    	} else $missing = false;

	    	// default list
	    	if($showall||$missing) {
		    	$cnt++;

		    	if($value['thumbExist']) $thumbExist = "yes";
		    	else $thumbExist = "no";

		    	if($value['imageExist']) $imageExist = "yes";
		    	else $imageExist = "no";

			    echo "  <tr>";
			    echo "    <td>".$cnt."</td>";
			    echo "    <td>".$value['name']."</td>";
			    echo "    <td>".$thumbExist."</td>";
			    echo "    <td>".$imageExist."</td>";
			    echo "    <td>".$value['size']."</td>";
			    echo "    <td>".$value['id']."</td>";
			    echo "  </tr>";
			}
	    }
	    echo "</table>";
}
?>
            </div>
        </div>
    </div>
</body>
