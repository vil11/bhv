<?php

/**
 * Check if URL exists or not.
 *
 * @param string $url
 * @return bool
 */
function urlExists($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ($httpCode >= 200 && $httpCode < 300);
}

/**
 * Get protocol by URL.
 *
 * @param $url
 * @return string
 */
function getProtocol($url)
{
    return explode('//', $url)[0] . '//';
}

/**
 * Get site name from URL.
 *
 * @param string $url
 * @return string
 */
function getSiteName($url)
{
    $siteName = str_replace(getProtocol($url), '', $url);
    $siteName = str_replace('www.', '', $siteName);
    $siteName = explode('.', $siteName)[0];

    return $siteName;
}

/**
 * Get domain name from URL.
 *
 * @param string $url
 * @return string
 */
function getDomain($url)
{
    $protocol = getProtocol($url);
    $domain = str_replace($protocol, '', $url);
    $domain = explode('/', $domain)[0];
    $domain = $protocol . $domain;

    return $domain;
}

/**
 * Get URL section by its position counting from the end.
 * Returns the last section by default.
 *
 * @param string $url
 * @param int $sectionBackwardPosition
 * @return string
 */
function getUrlSectionBackwards($url, $sectionBackwardPosition = 1)
{
    if ($url{strlen($url) - 1} === '/') {
        $url = substr($url, 0, -1);
    }
    $url = explode('/', $url);
    $section = $url[count($url) - $sectionBackwardPosition];

    return $section;
}
