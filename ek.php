<?php

namespace ek;

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

?>