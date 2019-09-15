<?php

/**
 * [IN PROGRESS] Get page Dom by its URL.
 * The Dom is built by Zend lib.
 *
 * @param string $url
 * @return Zend_Dom_Query
 */
//function getPageDom1(string $url): Zend_Dom_Query
//{
//    $pageHtml = file_get_contents($url);
//    $pageDom = new Zend_Dom_Query($pageHtml);
//
//    return $pageDom;
//}

/**
 * [IN PROGRESS] Get page Dom by its URL.
 * The Dom is built by Zend lib.
 *
 * @param string $url
 * @return Zend_Dom_Query
 */
//function getPageDom2(string $url): Zend_Dom_Query
//{
//    $handle = curl_init();
//    $options = [
//        CURLOPT_URL => $url,
//        CURLOPT_HEADER => false,
//        CURLOPT_CONNECTTIMEOUT => 4,
//        CURLOPT_RETURNTRANSFER => true,
//        CURLOPT_USERAGENT => 'PROJECT_TITLE',
//    ];
//    curl_setopt_array($handle, $options);
//
//    $html = curl_exec($handle);
//    curl_close($handle);
//
//    return new Zend_Dom_Query($html);
//}

/**
 * [IN PROGRESS] Validate if page exists & if its Dom contains a list element.
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

/**
 * [IN PROGRESS]  Get text content by Dom element xpath.
 *
 * @param Zend_Dom_Query $dom
 * @param string $divXpath
 * @throws Exception
 * @return string|null
 */
//function getTextByXpath(Zend_Dom_Query $dom, string $divXpath): ?string
//{
//    $div = $dom->queryXpath($divXpath);
//    if (!$div->valid()) {
//        throw new Exception();
//    }
//
//    return $div->current()->textContent;
//}

/**
 * [IN PROGRESS] Get attribute by its name & correspondent Dom element xpath.
 *
 * @param Zend_Dom_Query $dom
 * @param string $divXpath
 * @param string $attribute
 * @throws Exception
 * @return string|null
 */
//function getAttributeByXpath(Zend_Dom_Query $dom, string $divXpath, string $attribute): ?string
//{
//    $div = $dom->queryXpath($divXpath);
//    if (!$div->valid()) {
//        throw new Exception();
//    }
//
//    return $div->current()->getAttribute($attribute);
//}
