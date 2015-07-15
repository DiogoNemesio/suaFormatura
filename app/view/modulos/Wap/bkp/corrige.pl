#!/usr/bin/perl

@num = `cat Teotonio.txt`;


for $n (@num) {
	chop($n);
	$t	= length($n);

	if ($t == 10) {
		print $n."\n";
	}

}
