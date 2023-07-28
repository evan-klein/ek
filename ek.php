<?php

namespace ek;

/*
  __ ___ _  ___       __  __
 (_   | |_)  |  |\ | /__ (_
 __)  | | \ _|_ | \| \_| __)
*/

// This function generates a random string
function randStr($len=16, $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'){
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


// This function generates a random string, like randStr(), except that it excludes characters that look alike (i.e., I, i, L, l, 1, O, o, 0)
function randPw($len=16){
	return randStr(
		$len,
		'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789'
	);
}


/*
  _        __
 | \ |\ | (_ 
 |_/ | \| __)
*/

// This function returns an array of name servers for the domain specified
function getNameServers($domain){
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

?>