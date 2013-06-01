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
    <title>Gallery2</title>
    <script type="text/javascript" src="static/js/jquery-1.8.1.min.js"></script>
    <script type="text/javascript" src="static/js/javascript.js"></script>
    <script type="text/javascript" src="static/js/slimbox2.js"></script>
    <link rel="stylesheet" href="static/css/style.css" type="text/css" />
    <link rel="stylesheet" href="static/css/slimbox2.css" type="text/css" />
</head>
<body>
    <div id="base">
        <div class="menu_container">
            <h1>galleryV3</h1>
            <div id="menue_elemnts">
                <?php include("cache/menu.html");?>
            </div>
        </div>
        <div id="gallery">
            <h2 id="gallery_name">Name</h2>
            <div id="thumbnails">ready to load</div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.menue_link').click(function() {
                $('#thumbnails').html(""); 
                $id = $(this).attr('id');
                $.getJSON("cache/"+$id+".json", null, function(data){
                    $('#gallery_name').html(data.name);
                    $.each(data.img, function() {
                        $name = this.name;
                        if($name.length>17) $name = $name.substring(0,17)+"...";
                        $img = "<img width='<?php echo thumbSize?>' height='<?php echo thumbSize?>' src='cache/t_"+this.id+"-<?php echo thumbSize?>.jpg' />";
                        $lnk = "<a title='This is just a preview to open the original photo click <a target=\"_Blank\" href=\""+this.path+"\">here</a>' class='img_link' href='cache/t_"+this.id+"-<?php echo imageSize?>.jpg' target='_Blank' rel='lightbox-gl' >";
                        $div = "<div class='thumb'>"+$lnk+$img+"<br /><p class='thumb_name'>"+$name+"</p></a></div>"
                        $('#thumbnails').append($div);
                    });

                });
            });
        });
    </script>
              
</body>
</html>