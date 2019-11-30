<?php

/**
 * Put every array element into an array (format of data provider).
 *
 * @param array $array
 * @return array
 *
 * @tested 1.3.0
 */
function wrap(array $array): array
{
    $wrapped = [];
    foreach ($array as $key => $value) {
        if (is_int($key)) {
            $wrapped[$value] = [$value];
        } else {
//            $wrapped[$key] = [$key, $value];
//            $wrapped[$key] = [$value];
            $wrapped[$value] = [$key, $value];
        }
    }

    return $wrapped;
}
