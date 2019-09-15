<?php

/**
 * [IN PROGRESS] Check if URL exists or not.
 *
 * @param string $url
 * @return bool
 */
//function urlExists($url)
//{
//    $ch = curl_init($url);
//    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
//    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_exec($ch);
//    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//    curl_close($ch);
//
//    return ($httpCode >= 200 && $httpCode < 300);
//}

/**
 * [IN PROGRESS] Get protocol by URL.
 *
 * @param string $url
 * @return string array
 */
//function getProtocol($url)
//{
//    return explode('//', $url)[0] . '//';
//}

/**
 * [IN PROGRESS] Get site name from URL.
 *
 * @param string $url
 * @return string
 */
//function getSiteName($url)
//{
//    $siteName = str_replace(getProtocol($url), '', $url);
//    $siteName = str_replace('www.', '', $siteName);
//    $siteName = explode('.', $siteName)[0];
//
//    return $siteName;
//}

/**
 * [IN PROGRESS] Get domain name from URL.
 *
 * @param string $url
 * @return string
 */
//function getDomain($url)
//{
//    $protocol = getProtocol($url);
//    $domain = str_replace($protocol, '', $url);
//    $domain = explode('/', $domain)[0];
//    $domain = $protocol . $domain;
//
//    return $domain;
//}
