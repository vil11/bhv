<?php

use Laminas\Dom\Query as Query;


/**
 * Get text content by Dom element xpath.
 *
 * @param Query $dom
 * @param string $xpath
 * @return string|null
 * @throws Exception if provided div is invalid or if invalid q-ty of elements are found
 *
 * @tested 1.3.3
 */
function getTextByXpath(Query $dom, string $xpath): ?string
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
 * Get attribute by its name & correspondent Dom element xpath.
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
