<?php

/**
 * Put every array element into an array (format of PHPUnit data provider).
 * If element is array, handle its name to the Test as well.
 *
 * @param array $array
 * @return array
 *
 * @tested 1.3.2
 */
function wrap(array $array): array
{
    $wrapped = [];
    foreach ($array as $key => $value) {
        if (!is_array($value)) {
            $wrapped[$value] = [$value];
        } else {
            $wrapped[$key] = [$key, $value];
        }
    }

    return $wrapped;
}
