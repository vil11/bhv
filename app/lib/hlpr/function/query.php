<?php

use Laminas\Dom\Query as Query;


/**
 * Get text content by Dom element xpath.
 *
 * @param Query $dom
 * @param string $xpath
 * @return string
 * @throws Exception
 *      - if constructed element is invalid by provided xpath
 *      - if unexpected q-ty of elements are found by provided xpath
 *
 * @tested 1.3.3
 */
function getTextByXpath(Query $dom, string $xpath): string
{
    $div = $dom->queryXpath($xpath);

    if (!$div->valid()) {
        $err = err('Div was not found or is not valid by provided "%s" xpath.', $xpath);
        throw new Exception(prepareIssueCard($err));
    }
    if (count($div) !== 1) {
        $err = err('Element was not found or is not unique by provided "%s" xpath.', $xpath);
        throw new Exception(prepareIssueCard($err));
    }

    return trim($div->current()->textContent);
}

/**
 * Get list of text contents by Dom element xpath.
 *
 * @param Query $dom
 * @param string $xpath
 * @param int $elementsQtyMin
 * @param int $elementsQtyMax
 * @return string[]
 * @throws Exception
 *      - if constructed element is invalid by provided xpath
 *      - if unexpected q-ty of elements are found by provided xpath
 *
 * @tested 1.3.5
 */
function getTextsByXpath(Query $dom, string $xpath, int $elementsQtyMin = 1, int $elementsQtyMax = 100500): array
{
    $div = $dom->queryXpath($xpath);
    if (!$div->valid()) {
        $err = err('Div was not constructed validly by provided "%s" xpath.', $xpath);
        throw new Exception(prepareIssueCard($err));
    }

    $texts = [];
    for ($div->rewind(); $div->valid(); $div->next()) {
        $texts[] = trim($div->current()->textContent);
    }

    $c = count($div);
    if ($elementsQtyMin > $elementsQtyMax) {
        throw new Exception();
    }
    if ($c < $elementsQtyMin) {
        $err = err('Element was not found or is not unique by provided "%s" xpath.', $xpath);
        throw new Exception(prepareIssueCard($err));
    }
    if ($c > $elementsQtyMax) {
        $err = err('Element was not found or is not unique by provided "%s" xpath.', $xpath);
        throw new Exception(prepareIssueCard($err));
    }

    return $texts;
}

/**
 * Get attribute by its name & corresponding Dom element xpath.
 *
 * @param Query $dom
 * @param string $xpath
 * @param string $attribute
 * @return string|null
 * @throws Exception if provided div is invalid
 *
 * @tested 1.3.3
 */
function getAttributeByXpath(Query $dom, string $xpath, string $attribute): ?string
{
    $div = $dom->queryXpath($xpath);

    if (!$div->valid()) {
        $err = err('Div was not found or is not valid by provided "%s" xpath.', $xpath);
        throw new Exception(prepareIssueCard($err));
    }

    return trim($div->current()->getAttribute($attribute));
}
