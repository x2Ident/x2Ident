<?php
/*
* x2Ident (web interface)
* @version: release 1.0.0
* @see https://github.com/x2Ident/x2Ident
*/

$filename = "qr.png";
$handle = fopen($filename, "rb");
$contents = fread($handle, filesize($filename));
fclose($handle);
unlink($filename);
header("content-type: image/png");
 
echo $contents;
 
 
?>
