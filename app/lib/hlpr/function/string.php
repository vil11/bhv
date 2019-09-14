<?php

// ISO encoding = 'ISO-8859-1';

/**
 * Prepare file name for saving:
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
 */
function smartPrepareFileName($fileName)
{
    $restrictedCharacters = [
        "Á" => 'A',
        "á" => 'a',
        "à" => 'a',
        "ã" => 'a',
        "Ć" => 'C',
        "ć" => 'c',
        "č" => 'c',
        "ð" => 'd',
        "É" => 'E',
        "é" => 'e',
        "ë" => 'e',
        "ï" => 'i',
        "î" => 'i',
        "í" => 'i',
        "ñ" => 'n',
        "Ö" => 'O',
        "ö" => 'o',
        "ô" => 'o',
        "Ō" => 'O',
        "ō" => 'o',
        "Ó" => 'o',
        "ó" => 'o',
        "ş" => 's',
        "Š" => 'S',
        "š" => 's',
        "ß" => 'ss',
        "ü" => 'u',
        "ū" => 'u',
        "ú" => 'u',
        "ž" => 'z',
        "\n" => ' ',
        "\r" => ' ',
        "\t" => ' ',
        "/" => ' ',
        "|" => ' ',
        "\\" => ' ',
        "+" => ' ',
        "?" => ' ',
        "*" => ' ',
        ":" => ' ',
        ">" => ' ',
        "<" => ' ',
        "[ " => '[',
        " ]" => ']',
        "( " => '(',
        " )" => ')',
        " !" => '!',
        '"' => "'",
    ];
    foreach ($restrictedCharacters as $restricted => $replacing) {
        $fileName = str_replace($restricted, $replacing, $fileName);
    }

    $wrappers = [
        '[',
        ']',
        '(',
        ')',
        ' ',
    ];
    foreach ($wrappers as $wrapper) {
        while (strpos($fileName, $wrapper . $wrapper)) {
            $fileName = str_replace($wrapper . $wrapper, $wrapper, $fileName);
        }
    }

    $fileName = trim(fixDirSeparatorsToTheRight($fileName));

    return $fileName;
}

/**
 * Check if string contains no upper case characters.
 *
 * @param string $string
 * @return bool
 */
function containsNoUpperCase($string)
{
    return strtolower($string) == $string;
}

/**
 * Replace backslash with slash in specified path.
 *
 * @param string $path
 * @return string
 */
function fixDirSeparatorsToTheRight($path)
{
    return str_replace("\\", "/", $path);
}

/**
 * Replace slash with backslash in specified path.
 *
 * @param string $path
 * @return string
 */
function fixDirSeparatorsToTheLeft($path)
{
    return str_replace("/", "\\", $path);
}

/**
 * Fix encoding while reading.
 * TODO: specify type of reading.
 * TODO: remove encodings names to config.
 * Is relevant for Cyrillic & Latin characters.
 *
 * @param string $string
 * @return string
 */
function fixEncodingWhileReading($string)
{
    return changeEncoding($string, 'Windows-1251', 'UTF-8');
}

/**
 * Fix encoding while writing.
 * TODO: specify type of writing.
 * TODO: remove encodings names to config.
 * Is relevant for Cyrillic & Latin characters.
 *
 * @param string $string
 * @return string
 */
function fixEncodingWhileWriting($string)
{
    return changeEncoding($string, 'UTF-8', 'Windows-1251');
}

/**
 * Change string encoding.
 * TODO: investigate "$currentEncoding == $_utfEncoding" case.
 * TODO: remove encodings names to config.
 * 'UTF-8'
 * 'Windows-1251'
 * 'ISO-8859-1'
 * 'ISO-8859-2'
 * 'KOI8-U'
 * 'KOI8-R'
 *
 * @param string $string
 * @param string $inputEncoding
 * @param string $outputEncoding
 * @return string
 */
function changeEncoding($string, $inputEncoding, $outputEncoding)
{
    $currentEncoding = iconv_get_encoding('input_encoding');
    if ($currentEncoding != 'UTF-8') {
        $string = iconv($inputEncoding, $outputEncoding, $string);
    }

    return $string;
}
