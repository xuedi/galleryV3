<?php
function saveFileIndex($data) {
    file_put_contents("cache/fileIndex.array", serialize($data));
    echo "updated fileIndex<br>";
}
function updateFileIndex($data) {
    $retVal = array();
    foreach($data as $item => $key) {
        if(is_array($key['img'])) {
            foreach($key['img'] as $image => $imageData) {

                $thumb = "cache/t_".$imageData['id']."-".thumbSize.".jpg";
                if(file_exists($thumb)) $thumbExist = true;
                else $thumbExist = false;

                $image = "cache/t_".$imageData['id']."-".imageSize.".jpg";
                if(file_exists($image)) $imageExist = true;
                else $imageExist = false;

                $retVal[] = array(
                    "id" => $imageData['id'],
                    "name" => $imageData['name'],
                    "path" => $imageData['path'],
                    "thumbExist" => $thumbExist,
                    "imageExist" => $imageExist,
                    "size" => filesize($imageData['path'])
                    );
            }
        }
        if(is_array($key['sub'])) {
            $tmp = updateFileIndex($key['sub']);
            if(is_array($tmp)) $retVal = array_merge($retVal, $tmp);
        }
    }
    return $retVal;
}
function countMissing() {
    $cnt = 0;
    $data = unserialize(file_get_contents("cache/fileIndex.array"));
    foreach($data as $key => $value) {
        if(!$value['thumbExist'] || !$value['imageExist'] ) $cnt++;
    }
    // refresh index
    return $cnt;
}
function genThumnails() {
    $cnt = 0;
    $data = unserialize(file_get_contents("cache/fileIndex.array"));
    shuffle($data);
    foreach($data as $key => $value) {
        if($cnt<20 && (!$value['thumbExist']||!$value['imageExist']) ) {
            $cnt++;

            $dest = absolutePath."cache/t_".$value['id']."-".thumbSize.".jpg";
            $source = absolutePath.$value['path'];
            if(!file_exists($dest)&&file_exists($source)) 
                make_thumb_chopped($source, $dest, thumbSize);

            $dest = absolutePath."cache/t_".$value['id']."-".imageSize.".jpg";
            $source = absolutePath.$value['path'];
            if(!file_exists($dest)&&file_exists($source)) 
                make_thumb_chopped($source, $dest, imageSize);
        }
    }
    // refresh index
    saveFileIndex(updateFileIndex(genInit("galleries",true)));
}
function genJson($data, $level=0) {
    foreach($data as $item => $key) {
        $file = "cache/".$key['id'].".json";
        file_put_contents($file, json_encode(array("name"=>$key['name'], "img"=>$key['img']), JSON_PRETTY_PRINT) );
        if(is_array($key['sub'])) genJson($key['sub'], $level+1);
    }
    return $retVal;
}
function genMenu($data, $level=0) {
    $retVal = "";
    foreach($data as $item => $key) {
        $spacer = '';
        for($i=0;$i<$level;$i++) {
            if($i==($level-1)) $spacer .= "<img src='static/images/sub.png' />";
            else $spacer .= "<img src='static/images/sub_blind.png' />";
        }
        $retVal .= "              <li class='menue_item'>\n";
        $retVal .= "                <a class='menue_link' id='".$key['id']."'>$spacer".$key['name']." </a><div class='menue_item_cnt'>(".$key['cnt'].")</div>\n";
        $retVal .= "              </li>\n";
        if(is_array($key['sub'])) $retVal .= genMenu($key['sub'], $level+1);
    }
    return $retVal;
}
function genInit($path, $withImages=false) {
    $data=0;
    $list = array();
    $retVal = array();

    // fetch inicial raw data
    $directory = @opendir($path);
    while ($element = @readdir($directory)) {
        if($element != "." && $element != "..") {
            if(is_dir($path."/".$element)) {
                $data=1;
                $list[] = $element;
            }
        }
    }
    @closedir($directory);
    if($data==0) return 0;

    // sort data
    sort($list);
    
    // add subfolders
    foreach($list as $element) {
        $tmpImages = getImages($path."/".$element, 0);
        $cnt = count($tmpImages);
        if(!$withImages) $tmpImages = 0;
        $retVal[] = array( 
            "id" => md5($element), 
            "name" => $element, 
            "cnt" => $cnt,
            "img" => $tmpImages,
            "sub" => genInit($path."/".$element, $withImages)
            );
    }
    return $retVal;
}
function getImages($path) {
    $images = array();
    $directory = @opendir($path);
    while ($element = @readdir($directory)) {
        if (($element <> ".") && ($element <> "..")) {
            $file = $path."/".$element;
            if(is_file($file)) {
                if(exif_imagetype($file)==IMAGETYPE_JPEG) {
                    $images[] = array( 
                        "id" => md5($file),
                        "name" => $element,
                        "path" => $file
                        );
                }
            }
        }
    }
    @closedir($directory);
    return $images;
}
function make_thumb_chopped($src, $dest, $desired_width) {

    // read the source image
    $source_image = imagecreatefromjpeg($src);
    
    // fetch height width
    $width = imagesx($source_image);
    $height = imagesy($source_image);
    
    // its a square :-P
    $new_w = $desired_width;
    $new_h = $desired_width;

    // calculate ratios
    $w_ratio = ($new_w / $width);
    $h_ratio = ($new_h / $height);

    // calculate positions and chopping points
    if ($width > $height ) {//landscape
        $crop_w = round($width * $h_ratio);
        $crop_h = $new_h;
        $src_x = ceil( ( $width - $height ) / 2 );
        $src_y = 0;
    } elseif ($width < $height ) {//portrait
        $crop_h = round($height * $w_ratio);
        $crop_w = $new_w;
        $src_x = 0;
        $src_y = ceil( ( $height - $width ) / 2 );
    } else {//square
        $crop_w = $new_w;
        $crop_h = $new_h;
        $src_x = 0;
        $src_y = 0;	
    }
    
    // create the gd image
    $virtual_image = imagecreatetruecolor($new_w,$new_h);
    
    // resize & chopp the thumnail
    imagecopyresampled($virtual_image, $source_image, 0 , 0 , $src_x, $src_y, $crop_w, $crop_h, $width, $height);

    // write the image into a real picure
    imagejpeg($virtual_image, $dest, 100);
}
function make_thumb($src, $dest, $desired_width) {

	/* read the source image */
	$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	/* find the "desired height" of this thumbnail, relative to the desired width  */
        if($width>$height) {
            $desired_height = floor($height * ($desired_width / $width));
        }
        if($width<$height) {
            $desired_height = floor($desired_width/1.5);
            $desired_width = floor($width * ($desired_height / $height));
        }
	
	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);
	
	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
	
	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image, $dest, 90);
}
?>