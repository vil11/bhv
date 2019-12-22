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
 * [IN PROGRESS] Prepare file name for saving:
 *  # replace foreign alphabet characters by english (latin) analogue;
 *  # remove tabs;
 *  # remove restricted in Windows OS symbols;
 *  # remove invalid wrapping;
 *  # remove double spaces;
 *  # fix directory separators;
 *  # trim.
 *
 * @param string $fileName
 * @return string
 *
 */
//function smartPrepareFileName(string $fileName): string
//{
//    $restrictedCharacters = [
//        "Á" => 'A',
//        "á" => 'a',
//        "à" => 'a',
//        "ã" => 'a',
//        "Ć" => 'C',
//        "ć" => 'c',
//        "č" => 'c',
//        "ð" => 'd',
//        "É" => 'E',
//        "é" => 'e',
//        "ë" => 'e',
//        "ï" => 'i',
//        "î" => 'i',
//        "í" => 'i',
//        "ñ" => 'n',
//        "Ö" => 'O',
//        "ö" => 'o',
//        "ô" => 'o',
//        "Ō" => 'O',
//        "ō" => 'o',
//        "Ó" => 'o',
//        "ó" => 'o',
//        "ş" => 's',
//        "Š" => 'S',
//        "š" => 's',
//        "ß" => 'ss',
//        "ü" => 'u',
//        "ū" => 'u',
//        "ú" => 'u',
//        "ž" => 'z',
//        "\n" => ' ',
//        "\r" => ' ',
//        "\t" => ' ',
//        "/" => ' ',
//        "|" => ' ',
//        "\\" => ' ',
//        "+" => ' ',
//        "?" => ' ',
//        "*" => ' ',
//        ":" => ' ',
//        ">" => ' ',
//        "<" => ' ',
//        "[ " => '[',
//        " ]" => ']',
//        "( " => '(',
//        " )" => ')',
//        " !" => '!',
//        '"' => "'",
//    ];
//    foreach ($restrictedCharacters as $restricted => $replacing) {
//        $fileName = str_replace($restricted, $replacing, $fileName);
//    }
//
//    $wrappers = [
//        '[',
//        ']',
//        '(',
//        ')',
//        ' ',
//    ];
//    foreach ($wrappers as $wrapper) {
//        while (strpos($fileName, $wrapper . $wrapper)) {
//            $fileName = str_replace($wrapper . $wrapper, $wrapper, $fileName);
//        }
//    }
//
//    $fileName = trim(bendSeparatorsRight($fileName));
//
//    return $fileName;
//}

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
 * [IN PROGRESS] Replace slash with backslash in specified path (file, URL, ...).
 *
 * @param string $path
 * @return string
 */
//function bendSeparatorsLeft($path)
//{
//    return str_replace("/", "\\", $path);
//}

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
