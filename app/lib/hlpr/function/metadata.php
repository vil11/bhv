<?php

function iptcMakeTag($rec, $data, $value)
{
    $length = strlen($value);
    $retVal = chr(0x1C) . chr($rec) . chr($data);

    if ($length < 0x8000) {
        $retVal .= chr($length >> 8) . chr($length & 0xFF);
    } else {
        $retVal .= chr(0x80) .
            chr(0x04) .
            chr(($length >> 24) & 0xFF) .
            chr(($length >> 16) & 0xFF) .
            chr(($length >> 8) & 0xFF) .
            chr($length & 0xFF);
    }

    return $retVal . $value;
}

function setJpgMetaData($filepath)
{
    $iptc = [
        '2#025' => $filepath,
    ];

    $data = '';
    foreach ($iptc as $tag => $string) {
        $tag = substr($tag, 2);
        $data .= iptcMakeTag(2, $tag, $string);
    }
    $content = iptcembed($data, $filepath);

    $fp = fopen($filepath, 'wb');
    fwrite($fp, $content);
    fclose($fp);
}
