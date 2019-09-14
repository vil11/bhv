<?php

/**
 * Get duplicated array elements:
 *  - works with array elements of the highest level only
 *  - returns empty array if all input array elements are unique
 *
 * @param array $array
 * @return array
 */
function getNotUniqueArrayValues(array $array)
{
    return array_unique(array_diff_assoc($array, array_unique($array)));
}

/**
 * Convert single-level array into format of phpunit data provider
 *
 * @param array $array
 * @return array
 */
function prepareDataProvider(array $array)
{
    $dataProvider = [];
    foreach ($array as $value) {
        $dataProvider[] = [$value];
    }

    return $dataProvider;
}
