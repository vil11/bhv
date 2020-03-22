<?php

/**
 * Get page html by its URL.
 *
 * @param string $url
 * @return string
 * @throws Exception if response code is unexpected
 *
 * @tested 1.3.3
 */
function getHtml(string $url): string
{
    $ch = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_CONNECTTIMEOUT => 148,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
        CURLOPT_FOLLOWLOCATION => true,
    ];
    curl_setopt_array($ch, $options);

    $html = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode > 300 || $httpCode < 200)
    {
        $err = err('"%s" HTTP response code is unexpected for "%s" URL.', $httpCode, $url);
        throw new Exception(prepareIssueCard($err));
    };
    curl_close($ch);

    return $html;
}

/**
 * Get protocol by URL.
 *
 * @param string $url
 * @return string
 *
 * @tested 1.3.3
 */
function getProtocol(string $url): string
{
    return explode('//', $url)[0] . '//';
}

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
 * Get domain name from URL.
 *
 * @param string $url
 * @return string
 *
 * @tested 1.3.3
 */
function getDomain(string $url): string
{
    $protocol = getProtocol($url);
    $domain = str_replace($protocol, '', $url);
    $domain = parsePath($domain)[0];

    return $protocol . $domain;
}

/**
 * [IN PROGRESS] Validate if page exists & if its Dom contains a list of needed elements
 * before starting hard processes of detailed parsing & downloading.
 *
 * @param string $url
 * @param string $listDivXpath
 * @param array $skipDivXpaths
 * @return bool
 */
//function isDivReady(string $url, string $listDivXpath, array $skipDivXpaths): bool
//{
//    if (!urlExists($url)) {
//        return false;
//    }
//    if (getExt(getUrlBackPart($url, 1)) === 'gif') {
//        return true;
//    }
//
//    $dom = getPageDom($url);
//
//    $content = $dom->queryXpath($listDivXpath);
//    $contentIsInvalid = !$content->valid() || $content->count() === 0;
//    if ($contentIsInvalid) {
//        return false;
//    }
//
//    foreach ($skipDivXpaths as $xpath) {
//        $skipDiv = $dom->queryXpath($xpath);
//        if ($skipDiv->valid() && $contentIsInvalid) {
//            return false;
//        }
//    }
//
//    return true;
//}
