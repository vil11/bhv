<?php

const COLOR_RED = "\e[00;31m";
const COLOR_GREEN = "\e[32m";
const COLOR_YELLOW = "\e[0;33m";
const COLOR_BLUE = "\e[0;34m";
const COLOR_WHITE = "\e[0;37m";
const COLOR_GREY = "\e[0;90m" ;

const COLOR_OFF = "\033[0m";


/**
 * Form error message (or its part) by filling phrase with arguments.
 *
 * @param string $phrase
 * @param string $object
 * @param string|null $subject
 * @return string
 *
 * @tested 1.2.6
 */
function err(string $phrase, string $object, string $subject = null): string
{
    if ($subject === null) {
        $err = sprintf($phrase, $object);
    } else {
        $err = sprintf($phrase, $object, $subject);
    }

    return $err;
}

/**
 * Generate formatted error (for issues reporting, exception messages, ...).
 *
 * @param string $issue
 * @param string $path
 * @return string
 *
 * @tested 1.3.1
 */
function prepareIssueCard(string $issue, string $path = ''): string
{
    $padding = str_repeat("\n", 2);
    $delimiter = '+' . str_repeat("-", 8) . '+' . str_repeat('-', 88) . "\n";
    $delimiter = shColor($delimiter, 'grey');

    $err = $padding . $delimiter;
    if ($path !== '') {
        $issue = shColor($issue, 'white');
        $path = shColor(bendSeparatorsRight($path), 'blue');

        $err .= shColor('| PATH   : ', 'grey') . "$path\n" . $delimiter;
    }
    $err .= shColor('| ISSUE  : ', 'grey') . shColor("$issue\n", 'white') . $delimiter . str_repeat($padding, 2);

    return $err;
}

/**
 * @param string $phrase
 * @param string $color
 *
 * @tested 1.3.1
 */
function say(string $phrase, $color = '')
{
    echo ($color !== '') ? shColor($phrase, $color) : $phrase;
}

/**
 * @param string $phrase
 * @param string $color
 * @return string
 *
 * @tested 1.3.1
 */
function shColor(string $phrase, string $color): string
{
    return constant(strtoupper("color_" . $color)) . $phrase . COLOR_OFF;
}
