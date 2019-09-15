<?php

/**
 * Generate formatted error (for issues reporting, exception messages, ...).
 *
 * @param string $issue
 * @param string $path
 * @return string
 *
 * @tested 1.2.3
 */
function prepareIssueCard(string $issue, string $path = ''): string
{
    $padding = str_repeat("\n", 2);
    $delimiter = "+" . str_repeat("-", 8) . "+" . str_repeat("-", 88) . "\n";

    $e = $padding . $delimiter;
    if ($path !== '') {
        $e .= "| PATH   : $path\n" . $delimiter;
    }
    $e .= "| ISSUE  : $issue\n" . $delimiter . $padding;

    return fixEncodingWhileRead($e);
}
