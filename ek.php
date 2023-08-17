<?php

namespace ek;

/*

  _   _   _   _  _  __  __ ___       __
 |_) |_) / \ /  |_ (_  (_   |  |\ | /__
 |   | \ \_/ \_ |_ __) __) _|_ | \| \_|

*/

function get($key, $default_val=''): string {
	return $_GET[$key] ?? $default_val;
}


function post($key, $default_val=''): string {
	return $_POST[$key] ?? $default_val;
}


function request($key, $default_val=''): string {
	return $_REQUEST[$key] ?? $default_val;
}


function cookie($key, $default_val=''): string {
	return $_COOKIE[$key] ?? $default_val;
}


function addNBSP($input): string {
	return $input=='' ? '&nbsp;':$input;
}


function removeNBSP($input): string {
	return $input=='&nbsp;' ? '':$input;
}


function htmlSafe($input, $input_encoding='UTF-8'): string {
	return \htmlspecialchars(
		$input,
		ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,
		$input_encoding
	);
}


function xmlSafe($input, $input_encoding='UTF-8'): string {
	return \htmlspecialchars(
		$input,
		ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE | ENT_DISALLOWED,
		$input_encoding
	);
}


// This function takes a string or an array, converts it to UTF-8, and returns the result. Requires PHP v8.0+ because of the union type used for the return type
function convertToUTF8($input, $input_encoding='CP1252'): string|array {
	// Attempt to convert the input to UTF-8
	$result = \mb_convert_encoding(
		$input,
		'UTF-8',
		$input_encoding
	);

	// Throw an exception if it failed
	if(
		\is_bool($result)
		&&
		!$result
	) throw new \Exception('Failed to convert to UTF-8', 500);

	// Otherwise, return the result
	return $result;
}


/*

  __ ___ _  ___       __  __
 (_   | |_)  |  |\ | /__ (_
 __)  | | \ _|_ | \| \_| __)

*/

// This function generates a random string
function randStr($len=16, $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'): string {
	$chars_len = \strlen($chars);

	$rand_str = '';

	while($len>0){
		$rand_str.=\substr(
			$chars,
			\random_int(0, $chars_len-1),
			1
		);
		$len--;
	}

	return $rand_str;
}


/*

  _       __  __        _   _   _   __
 |_) /\  (_  (_ \    / / \ |_) | \ (_
 |  /--\ __) __) \/\/  \_/ | \ |_/ __)

*/

// This function generates a random string, like randStr(), except that it excludes characters that look alike (i.e., I, i, L, l, 1, O, o, 0)
function randPw($len=16): string {
	return randStr(
		$len,
		'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'
	);
}


/*

    ___ ___ _
 |_| |   | |_)
 | | |   | |

*/

function redirect($url){
	\header("Location: $url");
	exit();
}


function isHTTPS(): bool {
	return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on';
}


function forceHTTPS(){
	// TODO
}


function sendJSONHeader(){
	\header('content-type: application/json; charset=utf-8');
}


function sendXMLHeader(){
	\header('content-type: text/xml');
}


/*

  _        __
 | \ |\ | (_ 
 |_/ | \| __)

*/

// This function returns an array of name servers for the domain specified
function getNameServers($domain): array {
	// TODO
	// Throw an exception if $domain is not a valid domain name

	// Perform a whois lookup
	$whois_lookup_results = \shell_exec('whois ' . \escapeshellarg($domain));
	// Throw an exception if it fails
	if(!$whois_lookup_results) throw new \Exception("whois lookup for domain \"$domain\" failed", 500);

	// Extract an array of name servers from the results
	\preg_match_all(
		"/Name Server: ([A-Z|a-z|0-9|\.|-]+)/",
		$whois_lookup_results,
		$matches
	);

	// Process the extracted name servers
	$name_servers = $matches[1] ?? [];
	$name_servers = \array_map('\strtolower', $name_servers);
	$name_servers = \array_unique($name_servers);

	return $name_servers;
}


/*

  __     __ ___ _
 (_ \_/ (_   | |_ |\/|
 __) |  __)  | |_ |  |

*/

// This function returns true if the shell command specified exists, or false otherwise. Do NOT use with user input
function shellCommandExists($cmd): bool {
	return is_string(`which $cmd`);
}


// This function returns the number of CPUs the system has, or 0 if it's unable to figure it out
function getNumCPUs(): int {
	if( \ek\shellCommandExists('nproc') ){
		return (int) `nproc`;
	}
	else if( \file_exists('/proc/cpuinfo') ){
		\preg_match_all(
			'/^processor/m',
			\file_get_contents('/proc/cpuinfo'),
			$matches
		);

		return \count($matches[0]);
	}
	else return 0;
}


// This function returns an associative array containing system memory usage metrics
function getMemInfo(): array {
	// Default values
	$mem_total = -1;
	$mem_free = -1;
	$mem_available = -1;
	$swap_total = -1;
	$swap_free = -1;

	// Linux
	if( \file_exists('/proc/meminfo') ){
		// Read the meminfo file
		$meminfo = \file_get_contents('/proc/meminfo');

		// Extract data
		\preg_match('/MemTotal:\s+(\d+)/', $meminfo, $match1);
		\preg_match('/MemFree:\s+(\d+)/', $meminfo, $match2);
		\preg_match('/MemAvailable:\s+(\d+)/', $meminfo, $match3);
		\preg_match('/SwapTotal:\s+(\d+)/', $meminfo, $match4);
		\preg_match('/SwapFree:\s+(\d+)/', $meminfo, $match5);

		// Convert from KB to MB
		if( isset($match1[1]) ) $mem_total = \round($match1[1]/1024);
		if( isset($match2[1]) ) $mem_free = \round($match2[1]/1024);
		if( isset($match3[1]) ) $mem_available = \round($match3[1]/1024);
		if( isset($match4[1]) ) $swap_total = \round($match4[1]/1024);
		if( isset($match5[1]) ) $swap_free = \round($match5[1]/1024);
	}
	// TODO
	// macOS
	// TODO
	// Windows

	// Return the result
	return [
		'mem_total'=>(int) $mem_total,
		'mem_free'=>(int) $mem_free,
		'mem_free_ratio'=>\round(($mem_free/$mem_total), 2),
		'mem_available'=>(int) $mem_available,
		'mem_available_ratio'=>\round(($mem_available/$mem_total), 2),
		'swap_total'=>(int) $swap_total,
		'swap_free'=>(int) $swap_free,
		'swap_free_ratio'=>\round(($swap_free/$swap_total), 2)
	];
}


// This function returns the amount of total disk space, in megabytes, on the filesystem or disk partition specified
function getTotalDiskSpace($path='/'): int {
	return \floor(
		disk_total_space($path)/1048576
	);
}


// This function returns the amount of free disk space, in megabytes, on the filesystem or disk partition specified
function getFreeDiskSpace($path='/'): int {
	return \floor(
		disk_free_space($path)/1048576
	);
}


/*

  _    ___
 /  |   |
 \_ |_ _|_

*/

/*
This function returns an associative array of all of the command-line arguments passed to the PHP script in the following formats:
--key=value
--another_key='another value'
--yet_another_key="yet another value"
*/
function getCommandLineArgs(): array {
	$results = [];

	foreach($GLOBALS['argv'] as $arg){
		if(
			\preg_match('/^--(\w+)=(.+)$/', $arg, $matches)
		){
			$key = $matches[1];
			$value = $matches[2];
			$results[$key] = $value;
		}
	}

	return $results;
}

?>