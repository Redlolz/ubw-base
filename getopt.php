#!/bin/php
<?php

function posix_getopt($argc, $argv, $optstring, &$optarg, &$optind)
{
	static $optpos = 0;

	# Check if there are any arguments at all
	if ($optind >= $argc || !$argv[$optind])
		return -1;

	if ($optind > $argc)
		return -1;

	# Test for '-' character
	if ($argv[$optind][0] != '-') {
		if ($optstring[0] == '-') {
			$optarg = $argv[$optind];
			$optind++;
			return 1;
		}
		return -1;
	}

	# Test for character after '-' character
	if (strlen($argv[$optind]) < 2)
		return -1;

	# Test for '--' to signify end of options
	if ($argv[$optind][1] == '-') {
		$optind++;
		return -1;
	}

	if (!$optpos)
		$optpos++;

	# Check if argument given is legal
	$optstringpos = strpos($optstring, $argv[$optind][$optpos]);
	if ($optstringpos === false) {
		if (strlen($argv[$optind]) == $optpos+1) {
			$optind++;
			$optpos = 0;
		} else {
			$optpos++;
		}
		return '?';
	}

	# Check if value is needed for the argument
	if ($optstringpos < strlen($optstring)-1 && $optstring[$optstringpos+1] == ':') {
		if (strlen($argv[$optind]) <= $optpos+1 && $argc > $optind+1 && $argv[$optind+1][0] != '-') {
			$c = $argv[$optind][$optpos];
			$optarg = $argv[$optind+1];
			$optind += 2;
			$optpos = 0;
			return $c;
		} else if (strlen($argv[$optind]) <= $optpos+1) {
			printf("%s: option requires an argument: %s\n", $argv[0], $argv[$optind][$optpos]);
			$optind++;
			$optpos = 0;
			return '?';
		} else {
			$optarg = substr($argv[$optind], $optpos+1);
		}
		$c = $argv[$optind][$optpos];
		$optind++;
		$optpos = 0;
	} else {
		$c = $argv[$optind][$optpos];
		if (strlen($argv[$optind]) == $optpos+1) {
			$optind++;
			$optpos = 0;
		} else {
			$optpos++;
		}
	}
	return $c;
}

