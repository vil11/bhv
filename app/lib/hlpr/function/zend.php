<?php

/**
 * Get page dom by its URL.
 * The dom is built by Zend lib.
 *
 * @param string $url
 * @return Zend_Dom_Query
 */
function getPageDom($url)
{
    $handle = curl_init();
    $options = [
        CURLOPT_URL             => $url,
        CURLOPT_HEADER          => false,
        CURLOPT_CONNECTTIMEOUT  => 4,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_USERAGENT       => 'PROJECT_TITLE',
    ];
    curl_setopt_array($handle, $options);

    $html = curl_exec($handle);
    curl_close($handle);

    return new Zend_Dom_Query($html);
}

/**
 * [IN PROGRESS]
 * Validate if page exists & if its dom contains a list element
 *
 * @param string $url
 * @param string $listDivXpath
 * @param array $skipDivXpaths
 * @return bool
 */
function isDivReady($url, $listDivXpath, array $skipDivXpaths)
{
    if (!urlExists($url)) {
        return false;
    }
    if (getExt(getUrlBackPart($url, 1)) === 'gif') {
        return true;
    }

    $dom = getPageDom($url);

    $content = $dom->queryXpath($listDivXpath);
    $contentIsInvalid = !$content->valid() || $content->count() === 0;
    if ($contentIsInvalid) {
        return false;
    }

    foreach ($skipDivXpaths as $xpath) {
        $skipDiv = $dom->queryXpath($xpath);
        if ($skipDiv->valid() && $contentIsInvalid) {
            return false;
        }
    }

    return true;
}

/**
 * Get text content by dom element xpath.
 *
 * @param Zend_Dom_Query $dom
 * @param string $divXpath
 * @return string
 * @throws Exception
 */
function getTextByXpath($dom, $divXpath)
{
    $div = $dom->queryXpath($divXpath);
    if (!$div->valid()) {
        throw new Exception();
    }

    return $div->current()->textContent;
}

/**
 * Get attribute by its name & correspondent dom element xpath.
 *
 * @param Zend_Dom_Query $dom
 * @param string $divXpath
 * @param string $attribute
 * @return string|null
 * @throws Exception
 */
function getAttributeByXpath($dom, $divXpath, $attribute)
{
    $div = $dom->queryXpath($divXpath);
    if (!$div->valid()) {
        throw new Exception();
    }

    return $div->current()->getAttribute($attribute);
}
