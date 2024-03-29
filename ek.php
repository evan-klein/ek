<?php

namespace evan_klein\ek;

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


function extract($input, string $var_name){
	if( !\is_array($input) ){
		return $input;
	}

	if( !\isset($input[$var_name]) ){
		throw new \Exception("Index '$var_name' is undefined", 400);
	}

	return $input[$var_name];
}


function strToBool($input, string $var_name): bool {
	$input_lowercase = \strtolower($input);

	$map = [
		'true' => true,
		't' => true,
		'yes' => true,
		'y' => true,
		'1' => true,
		'false' => false,
		'f' => false,
		'no' => false,
		'n' => false,
		'0' => false
	];

	if( !\isset($map[$input_lowercase]) ){
		$possible_vals = \implode(
			', ',
			\array_keys($map)
		);
		throw new \Exception("Invalid value of '$input' for '$var_name'. Possible values: $possible_vals", 400);
	}
	else return $map[$input_lowercase];
}


function addNBSP($input): string {
	return $input=='' ? '&nbsp;':$input;
}


function removeNBSP($input): string {
	return $input=='&nbsp;' ? '':$input;
}


function replaceWeirdChars(string $input): string {
	return \str_replace(
		['“', '”', '‘', '’', '…', '—'],
		['"', '"', "'", "'", '...', '--'],
		$input
	);
}


function replaceSmartQuotes(string $input): string {
	return \str_replace(
		['“', '”', '‘', '’'],
		['"', '"', "'", "'"],
		$input
	);
}


function replaceLongDashes(string $input): string {
	return \str_replace(
		['—'],
		['--'],
		$input
	);
}


function replaceEllipses(string $input): string {
	return \str_replace(
		['…'],
		['...'],
		$input
	);
}


function replaceNTimes($find, $replace, $input, int $limit){
	return \preg_replace(
		'/' . \preg_quote($find, '/') . '/',
		$replace,
		$input,
		$limit
	);
}


function replaceOnce($find, $replace, $input){
	return replaceNTimes($find, $replace, $input, 1);
}


function htmlSafe($input, string $input_encoding='UTF-8'): string {
	return \htmlspecialchars(
		$input,
		ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE | ENT_DISALLOWED,
		$input_encoding
	);
}


function xmlSafe($input, string $input_encoding='UTF-8'): string {
	return \htmlspecialchars(
		$input,
		ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE | ENT_DISALLOWED,
		$input_encoding
	);
}


// This function takes a string or an array, converts it to UTF-8, and returns the result. Requires PHP v8.0+ because of the union type used for the return type
function convertToUTF8($input, string $input_encoding='CP1252'): string|array {
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


function parseJSON(string $json, bool $associative=true, int $depth=512, $flags=JSON_THROW_ON_ERROR){
	return \json_decode($json, $associative, $depth, $flags);
}


function convertToJSON($input, $flags=JSON_THROW_ON_ERROR, int $depth=512): string {
	return \json_encode($input, $flags, $depth);
}


/*

  __ ___ _  ___       __  __
 (_   | |_)  |  |\ | /__ (_
 __)  | | \ _|_ | \| \_| __)

*/

// This function generates a random string
function randStr(int $len=16, string $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'): string {
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
function randPw(int $len=16): string {
	return randStr(
		$len,
		'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'
	);
}


/*
This function hashes the password provided using PHP's default hashing algorithm

Note:
- The hash returned will include the algorithm, cost, and salt used, as part of the hash, so even if PHP's default hashing algorithm changes in the future, \password_verify() and \evan_klein\ek\pwMatches() should still work

- PHP's default hashing algorithm is currently bcrypt
	- bcrypt truncates passwords to a maximum length of 72 characters
	- bcrypt hashes are always 60 characters long

- If PHP's default hashing algorithm does change, the length of the hashes returned by \password_hash() and \evan_klein\ek\hashPw() may change too. When using these two functions, it is recommended that you be prepared to work with hashes that are up to 255 characters long
*/
function hashPw(string $pw): string {
	return \password_hash($pw, PASSWORD_DEFAULT);
}


function pwMatches(string $pw, string $hash): bool {
	return \password_verify($pw, $hash);
}


/*

             ___  _      ___ ___  _
 \  / /\  |   |  | \  /\  |   |  / \ |\ |
  \/ /--\ |_ _|_ |_/ /--\ |  _|_ \_/ | \|

*/

function isValidEmail(string $input): bool {
	return \filter_var($input, FILTER_VALIDATE_EMAIL);
}


/*

    ___ ___ _
 |_| |   | |_)
 | | |   | |

*/

function redirect(string $url, int $status_code=301){
	\header("Location: $url", true, $status_code);
	exit();
}


function isHTTPS(): bool {
	return (
		(
			\isset($_SERVER['HTTPS'])
			&&
			!\empty($_SERVER['HTTPS'])
			&&
			$_SERVER['HTTPS']!=='off'
		)
		||
		(
			\isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
			&&
			$_SERVER['HTTP_X_FORWARDED_PROTO']=='https'
		)
	);
}


function forceHTTPS(){
	if( !isHTTPS() ) redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
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

// This function converts a Unicode domain name to an IDNA ASCII-compatible format, which is useful when working with domain names that contain non-ASCII characters
function domainToASCII(string $domain): string {
	$domain_ascii = \idn_to_ascii($domain);
	if($domain_ascii===false) throw new \Exception("idn_to_ascii($domain) failed", 400);
	return $domain_ascii;
}


// This function returns an array of name servers for the domain specified
function getNameServers(string $domain): array {
	// TODO
	// Throw an exception if $domain is not a valid domain name

	// Convert domain name to an IDNA ASCII-compatible format
	$domain = domainToASCII($domain);

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


function domainHasRecords(string $domain, string $type): bool {
	$domain = domainToASCII($domain);
	return \checkdnsrr($domain, $type);
}


function domainHasMXRecords(string $domain): bool {
	return domainHasRecords($domain, 'MX');
}


function throwIfDomainHasNoMXRecords(string $domain){
	if( !domainHasMXRecords($domain) ) throw new \Exception("Domain '$domain' does not have any MX records", 404);
}


/*

  __     __ ___ _
 (_ \_/ (_   | |_ |\/|
 __) |  __)  | |_ |  |

*/

// This function returns true if the shell command specified exists, or false otherwise. Do NOT use with user input
function shellCommandExists(string $cmd): bool {
	return is_string(`which $cmd`);
}


// This function returns the number of CPUs the system has, or 0 if it's unable to figure it out
function getNumCPUs(): int {
	if( shellCommandExists('nproc') ){
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
function getTotalDiskSpace(string $path='/'): int {
	return \floor(
		disk_total_space($path)/1048576
	);
}


// This function returns the amount of free disk space, in megabytes, on the filesystem or disk partition specified
function getFreeDiskSpace(string $path='/'): int {
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


function getStdin(bool $trim=false, bool $lowercase=false, bool $uppercase=false): string {
	$stdin = \fgets(STDIN);

	if($stdin===false){
		throw new \Exception('An error occurred while attempting to read from stdin', 500);
	}

	// Processing
	if($trim) $stdin = \trim($stdin);
	if($lowercase) $stdin = \strtolower($stdin);
	if($uppercase) $stdin = \strtoupper($stdin);

	return $stdin;
}

?>