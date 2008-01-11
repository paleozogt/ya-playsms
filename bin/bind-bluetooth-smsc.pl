#!/usr/bin/perl

# if no arguments just print out the
# available bluetooth devices and exit
#
if ($ARGV[0] eq "") {
     # scan for all bluetooth devices
     #
     $output= `hcitool scan`;

     $i= 0;
     while ($output=~ m/(..:..:..:..:..:..)\s*(.*)/g) {
	     @devices[$i++]= $1;
	     print "$2 $1\n";
     }
     exit;
}

# choose which device to bind to...
#
$device= $ARGV[0];

# get the first serial port on the device
#
$serialport= "SP";
$output= `sdptool search $serialport $device`;
$output=~ m/Channel:\s*(\d+)/;
$channel= $1;


# bind the serial port to an rfcomm path
#
$rfcommfile="/etc/bluetooth/rfcomm.conf";
$rfcommport=0;
$rfcommentry= "rfcomm$rfcommport {\n" .
          "\tbind yes;\n" .
          "\tdevice $device;\n" .
          "\tchannel $channel;\n" .
          "\tcomment \"Serial Port\";\n" .
     "}\n";
open(RFCOMM, ">>", $rfcommfile) or die "can't write $rfcommfile";
print RFCOMM $rfcommentry;
close(RFCOMM);

# restart bluetooth service
#
system("/etc/init.d/bluetooth restart") == 0 or die "can't restart bluetooth";


# create kannel smsc conf using the bound rfcomm path
#
$smscid = "modem$rfcommport";
$smscfile= "/etc/kannel/smsc.conf";
$smsclogfile= "/var/log/kannel/smsc.log";
$smscentry=
	"group = smsc\n" .
	"smsc = at\n" .
	"smsc-id = $smscid\n" . 
	"modemtype = auto\n" . 
	"device = /dev/rfcomm$rfcommport\n" .
	"connect-allow-ip = 127.0.0.1\n" .
	"keepalive=10\n" .
	"max-error-count=3\n" .
	"log-file=\"$smsclogfile\"\n" .
	"log-level = 0\n";
open(SMSC, ">", $smscfile) or die "can't write $smscfile";
print SMSC $smscentry;
close(RFCOMM);

# restart kannel
#
system("/etc/init.d/kannel restart") == 0 or die "can't restart kannel";


print "OK\n";
