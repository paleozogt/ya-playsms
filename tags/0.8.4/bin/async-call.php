#!/usr/bin/php
<?php
// hit the url from the command line
$url= $_SERVER['argv'][1];
file_get_contents($url);
?>