<html>
<head>
<title><?=$web_title?></title>
<meta name=\"author\" content=\"http://playsms.sourceforge.net\">

<?
// put php global settings into javascript
$script= "" . 
"<script type=\"text/javascript\"><!--\n" .
"   var SMS_SINGLE_MAXCHARS= $SMS_SINGLE_MAXCHARS;\n" . 
"   var SMS_SINGLE_MULTIPART_MAXCHARS= $SMS_SINGLEMULTIPART_MAXCHARS;\n" . 
"   var SMS_MULTIPART_MAX= $SMS_MULTIPART_MAX;\n" .
"   var SMS_MAXCHARS= $SMS_MAXCHARS;\n" . 
"--></script>";

echo $script;
?>

<script type="text/javascript" src="./inc/jscss/calendar/calendar.js"></script>
<script type="text/javascript" src="./inc/jscss/calendar/calendar-en.js"></script>
<script type="text/javascript" src="./inc/jscss/calendar/calendar-start.js"></script>
<script type="text/javascript" src="./inc/jscss/selectbox.js"></script>
<script type="text/javascript" src="./inc/jscss/common.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="./inc/jscss/calendar/calendar-blue.css" title="win2k-1">
<link rel="stylesheet" type="text/css" href="./inc/jscss/common.css">

</head>

<body>
