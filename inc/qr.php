<?php
 
$filename = "qr.png";
$handle = fopen($filename, "rb");
$contents = fread($handle, filesize($filename));
fclose($handle);
unlink($filename);
header("content-type: image/png");
 
echo $contents;
 
 
?>
