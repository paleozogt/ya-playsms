#!/usr/bin/php
<?php
error_log("async-call " . print_r($_SERVER['argv'], true));

// hit the url from the command line
$url= $_SERVER['argv'][1];
file_get_contents($url);
?>