<?php

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
