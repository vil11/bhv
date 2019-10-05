<?php

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
 * @tested 1.2.7
 */
function prepareIssueCard(string $issue, string $path = ''): string
{
    $padding = str_repeat("\n", 2);
    $delimiter = '+' . str_repeat("-", 8) . '+' . str_repeat('-', 88) . "\n";

    $err = $padding . $delimiter;
    if ($path !== '') {
        $path = bendSeparatorsRight($path);
        $err .= "| PATH   : $path\n" . $delimiter;
    }
    $err .= "| ISSUE  : $issue\n" . $delimiter . $padding;

    return $err;
}
