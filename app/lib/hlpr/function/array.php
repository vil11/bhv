<?php

/**
 * [IN PROGRESS] Get duplicated array elements:
 *  - works with array elements of the highest level only
 *  - returns empty array if all input array elements are unique
 *
 * @param array $array
 * @return array
 */
//function getNotUniqueArrayValues(array $array)
//{
//    return array_unique(array_diff_assoc($array, array_unique($array)));
//}

/**
 * Put every array element into an array (format of data provider).
 *
 * @param array $array
 * @return array
 *
 * @tested 1.2.7
 */
function wrap(array $array): array
{
    $wrapped = [];
    foreach ($array as $key => $value) {
        if (is_int($key)) {
            $wrapped[] = [$value];
        } else {
            $wrapped[] = [$key, $value];
        }
    }

    return $wrapped;
}
