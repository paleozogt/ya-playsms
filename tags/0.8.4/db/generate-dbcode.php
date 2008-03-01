#!/usr/bin/php
<?php
	require_once '../web/init.php';
	require_once 'DB.php';
	require_once 'DB/DataObject/Generator.php';

	if (!@$_SERVER['argv'][1]) {
		print "usage      : generate-dbcode.php <path-to-generate-code-into> \n";
		print "for example: generate-dbcode.php ../web/DataObjects/ \n";
		die;
	}
	
	// the default dboptions paths
	// use the playsms install path,
	// so we need to replace it with
	// the one from the command-line
	$dboptions = &PEAR::getStaticProperty('DB_DataObject','options');
	$adjustedpath= $_SERVER['argv'][1];
	$dboptions[schema_location]= $adjustedpath;
	$dboptions[class_location]= $adjustedpath;
	print_r($dboptions);

	print("generating code...\n");
	$generator = new DB_DataObject_Generator;	
	$generator->start();
?>
