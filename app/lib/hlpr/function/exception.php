<?php

/**
 * @param string $issue
 * @param string $path
 * @return string
 */
function prepareIssueCard(string $issue, string $path = '')
{
    $padding = str_repeat("\n", 2);
    $delimiter = "+" . str_repeat("-", 8) . "+" . str_repeat("-", 88) . "\n";

    $e = $padding . $delimiter;
    if ($path !== '') {
        $e .= "| PATH   : $path\n" . $delimiter;
    }
    $e .= "| ISSUE  : $issue\n" . $delimiter . $padding;

    return fixEncodingWhileReading($e);
}
