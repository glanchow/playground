<?php
/**
 * Encodes data with base64,
 * with the URL and filename safe alphabet (RFC4648),
 * and without the padding symbol (=).
 *
 * @param string $string
 * @return string
 */
function base64url_encode($string)
{
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($string));
}

/**
 * Decodes data encoded with base64.
 *
 * @param string $string
 * @return string|false
 */
function base64url_decode($string)
{
    $string = str_replace(array('-', '_'), array('+', '/'), $string);
    return base64_decode($string, true);
}

/**
 * Generate a unique id.
 */
$hex = md5('a_salt_is_a_salt' . uniqid('', true));
$pack = pack('H*', $hex);
$base64 = base64url_encode($pack);
$name = substr($base64, 0, 10);
echo $name;
echo "\n";
