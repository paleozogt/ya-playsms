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
         `/usr/bin/logger $2 $1`;
     }
     exit;
}

# choose which device to bind to...
#
$device= $ARGV[0];

# set up launcher for a custom passkey agent
# in the background so that when the phone
# asks to bond/pair, it will get an answer
$passkeyagent="/usr/bin/passkey-agent";
$passkeyhelper="/usr/share/playsms/bin/pin-agent-simple";
$passkeyhelper= "$passkeyagent $passkeyhelper $device &";


# run the passkeyhelper in case 
# we get a bonding attempt by the phone
# while we're setting up
#
system($passkeyhelper);


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

# slurp up the whole rfcomm config file
#
open(RFCOMM, $rfcommfile);
$rfcommconf= join('', <RFCOMM>);
close(RFCOMM);

# if this entry doesn't already exist, then add it
# TODO: what if there's a rfcommport conflict?
#
if ($rfcommconf!~ m/$rfcommentry/g) {
    open(RFCOMM, ">>", $rfcommfile) or die "can't write $rfcommfile";
    print RFCOMM $rfcommentry;
    close(RFCOMM);
}

# restart bluetooth service so that the rfcomm
# changes will get picked up
#
system("/etc/init.d/bluetooth restart") == 0 or die "can't restart bluetooth";

# wait for the bluetooth restart to get going
sleep(1);

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

# restart kannel so that its smsc changes
# will be picked up
#
system("/etc/init.d/kannel restart") == 0 or die "can't restart kannel";


# run the passkeyhelper
system($passkeyhelper);

print "OK\n";
