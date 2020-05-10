<?php

const ENCODING = [
    'utf' => 'UTF-8',
    'win' => 'Windows-1251',

    'iso-1' => 'ISO-8859-1',
    'iso-2' => 'ISO-8859-2',
    'koi-u' => 'KOI8-U',
    'koi-r' => 'KOI8-R'
];


/**
 * Make file name more unified to be saved on different OSs easily:
 *  # replace derived Latin characters by English analogues
 *  # remove tabs
 *  # remove restricted in Windows OS symbols
 *  # fix wrappings
 *  # remove doubles:
 *      - in wrapping
 *      - in spacing
 *  # trim
 *
 * @param string $fileName
 * @return string
 *
 * @tested 1.3.4
 */
function smartPrepareFileName(string $fileName): string
{
    $replacements = [
        // vocabulary
        'Á' => 'A',
        'á' => 'a',
        'à' => 'a',
        'ã' => 'a',
        'Ć' => 'C',
        'ć' => 'c',
        'č' => 'c',
        'ð' => 'd',
        'É' => 'E',
        'é' => 'e',
        'ë' => 'e',
        'ï' => 'i',
        'î' => 'i',
        'í' => 'i',
        'ñ' => 'n',
        'Ö' => 'O',
        'ö' => 'o',
        'ô' => 'o',
        'Ō' => 'O',
        'ō' => 'o',
        'Ó' => 'o',
        'ó' => 'o',
        'ş' => 's',
        'Š' => 'S',
        'š' => 's',
        'ß' => 'ss',
        'ü' => 'u',
        'ū' => 'u',
        'ú' => 'u',
        'ž' => 'z',

        // tabs
        "\n" => '',
        "\r" => '',
        "\t" => '',

        // windows
        '/' => '',
        '|' => '',
        "\\" => '',
        '?' => '',
        '*' => '',
        ':' => '',
        '>' => '',
        '<' => '',

        // wrappers
        '[ ' => '[',
        '( ' => '(',
        '{ ' => '{',
        ' ]' => ']',
        ' )' => ')',
        ' }' => '}',
        ' !' => '!',
        '"' => "'",
    ];
    $restricteds = array_keys($replacements);
    $replacements = array_values($replacements);
    $fileName = str_replace($restricteds, $replacements, $fileName);

    $fileName = removeDoubles(['[', ']', '(', ')', '{', '}', ' '], $fileName);

    return trim($fileName);
}

/**
 * Remove duplicates of requested substrings inside file name.
 *
 * @param string[] $uniques
 * @param string $fileName
 * @return string
 *
 * @tested 1.3.3
 */
function removeDoubles(array $uniques, string $fileName): string
{
    foreach ($uniques as $unique) {
        while (strpos($fileName, $unique . $unique)) {
            $fileName = str_replace($unique . $unique, $unique, $fileName);
        }
    }

    return $fileName;
}

/**
 * Replace backslash with slash in specified path (file, URL, ...).
 *
 * @param string $path
 * @return string
 *
 * @tested 1.2.3
 */
function bendSeparatorsRight($path): string
{
    return str_replace("\\", '/', $path);
}

/**
 * Switch encoding from Windows-1251 to UTF-8:
 *  - is relevant for Cyrillic & Latin characters
 *
 * @param string $string
 * @return string
 *
 * @tested 1.2.3
 */
//function fixEncodingWhileRead(string $string): string
//{
//    return mb_convert_encoding($string, ENCODING['win'], ENCODING['utf']);
//}

/**
 * [IN PROGRESS] Switch encoding from UTF-8 to Windows-1251:
 *   - is relevant for Cyrillic & Latin characters
 *
 * @param string $string
 * @return string
 */
//function fixEncodingWhileWrite(string $string): string
//{
//    return mb_convert_encoding($string, ENCODING['utf'], ENCODING['win']);
//}

/**
 * Get path (file, URL, ...) section by its position counting backwards.
 *  - returns the last section by default.
 *
 * @param string $path
 * @param int $sectionPositionBackwards
 * @return string
 *
 * @tested 1.2.5
 */
function getPathSectionBackwards(string $path, int $sectionPositionBackwards = 1): string
{
    $path = parsePath($path);
    return $path[count($path) - $sectionPositionBackwards];
}

/**
 * Break path (file, URL, ...) to sections. Adjust path not to end with divider, in advance.
 *
 * @param string $path
 * @param string $divider
 * @return array
 *
 * @tested 1.2.5
 */
function parsePath(string $path, string $divider = '/'): array
{
    $path = bendSeparatorsRight($path);
    if ($path{strlen($path) - strlen($divider)} === $divider) {
        $path = substr($path, 0, -strlen($divider));
    }

    return explode($divider, $path);
}

/**
 * Check if the string is tagged with the prefix.
 *
 * @param string $string
 * @param string $prefix
 * @return bool
 *
 * @tested 1.3.6
 */
function isMarkedWithPrefix(string $string, string $prefix): bool
{
    return (substr($string, 0, strlen($prefix)) === $prefix);
}
