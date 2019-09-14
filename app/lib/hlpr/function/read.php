<?php

/**
 * Check if file is valid for further using.
 *
 * @param string $filePath
 * @return bool
 */
function isFileValid($filePath)
{
    return (file_exists($filePath) && is_file($filePath) && is_readable($filePath) && filesize($filePath) > 0);
}

/**
 * Get file extension in ".ext" format.
 *
 * @param string $filePath
 * @return string
 */
function getExt($filePath)
{
    return pathinfo($filePath, PATHINFO_EXTENSION);
}

/**
 * Check if 2 not broken files are the same or not.
 *
 * @param string $firstFilePath
 * @param string $secondFilePath
 * @return bool
 */
function filesAreEqual($firstFilePath, $secondFilePath)
{
    if (getExt($firstFilePath) !== getExt($secondFilePath)) {
        return false;
    }
    if (file_get_contents($firstFilePath) !== file_get_contents($secondFilePath)) {
        return false;
    }

    if (fileIsImage($firstFilePath) && !imgsAreEqual($firstFilePath, $secondFilePath)) {
        return false;
    }

    return true;
}

/**
 * Check if file is image or not.
 *
 * @param string $filePath
 * @return bool
 */
function fileIsImage($filePath)
{
    return getimagesize($filePath) ? true : false;
}

/**
 * Check if 2 images are the same or not.
 * Images can be called by path on Hard Disk Drive or by URL.
 * At least 1 of files must be called by path on Hard Disk Drive.
 *
 * @param string $firstImgPath
 * @param string $secondImgPath
 * @return bool
 * @throws Exception if it is impossible to render image file properties
 */
function imgsAreEqual($firstImgPath, $secondImgPath)
{
    if (!($firstImgProperties = getimagesize($firstImgPath))) {
        throw new Exception("Path to [$firstImgPath] is invalid.");
    }
    if (!($secondImgProperties = getimagesize($secondImgPath))) {
        throw new Exception("Path to [$secondImgPath] is invalid.");
    }

    if ($firstImgProperties !== $secondImgProperties) {
        return false;
    }
    foreach ($firstImgProperties as $key => $value) {
        if ($firstImgProperties[$key] !== $secondImgProperties[$key]) {
            return false;
        }
    }

    return true;
}

/**
 * Download file by URL.
 * TODO: specify supported file formats.
 *
 * @param string $fileUrl
 * @param string $saveFilePath
 */
function downloadFile($fileUrl, $saveFilePath)
{
    file_put_contents($saveFilePath, fopen($fileUrl, 'r'));
}

/**
 * Download file by URL.
 * TODO: specify supported file formats.
 *
 * @param string $fileUrl
 * @param string $saveFilePath
 */
function downloadFile2($fileUrl, $saveFilePath)
{
    $ch = curl_init($fileUrl);
    $fp = fopen($saveFilePath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);
}

/**
 * Wait while file is loading.
 *
 * @param string $fileUrl
 */
function waitForLoading($fileUrl)
{
    sleep(2);
    $delay = getFileSize($fileUrl) / 30000000;
    sleep($delay);
}

/**
 * Check if file is ready to be downloaded:
 *  - file size < correspondent limit (default file size limit is 1gb);
 *  - Hard Disk Drive free space > correspondent limit (default available space limit is 10gb).
 *
 * @param string $fileUrl
 * @param string $saveFilePath
 * @return bool
 */
function isFileReadyToDownload($fileUrl, $saveFilePath)
{
    if (getFileSize($fileUrl) > 1073741824) {
        return false;
    }
    if (disk_free_space($saveFilePath) < 10737418240) {
        return false;
    }

    return true;
}

/**
 * Get file size by its path on Hard Disk Drive or per URL.
 *
 * @param string $filePath
 * @return int
 */
function getFileSize($filePath)
{
    if (substr($filePath, 0, 4) != 'http') {
        $fileSize = @filesize($filePath);
    } else {
        $fileData = array_change_key_case(get_headers($filePath, 1), CASE_LOWER);
        if (!is_array($fileData['content-length'])) {
            $fileSize = $fileData['content-length'];
        } else {
            $fileSize = $fileData['content-length'][1];
        }
    }

    return (int)$fileSize;
}

/**
 * Parse list from txt file.
 * Function returns empty array if specified file is empty.
 * TODO: investigate possibility not to return mix variables types.
 *
 * @param string $filePath
 * @return array
 */
function parseList($filePath)
{
    $content = explode("\r\n", file_get_contents($filePath));
    foreach ($content as $key => $record) {
        if (trim($record) == '') {
            unset($content[$key]);
        }
    }

    return array_values($content);
}

/**
 * Parse CSV file & convert pulled data to array of arrays.
 * Function returns empty array if input file is empty.
 *
 * @param string $filePath
 * @param string $cellDelimiter
 * @return array
 * @throws Exception if input path is invalid
 */
function parseCsvTable($filePath, $cellDelimiter = ',')
{
    if (!isFileValid($filePath)) {
        throw new Exception("Path to [$filePath] is invalid.");
    }

    $header = null;
    $data = [];
    $handle = fopen($filePath, 'r');
    while ($row = fgetcsv($handle, 1000, $cellDelimiter)) {
        if (!$header) {
            $header = $row;
        } else {
            $data[] = array_combine($header, $row);
        }
    }
    fclose($handle);

    return $data;
}

/**
 * Get full record by 1 of its values.
 *
 * @param string $filePath
 * @param string $value
 * @return null|string
 */
function getCsvRecord($filePath, $value)
{
    foreach (parseCsvTable($filePath) as $record) {
        if (in_array($value, $record)) {
            return $record;
        }
    }
}

/**
 * Check if log record is already present in appropriate log file or not.
 *
 * @param string $logRecord
 * @param string $logFilePath
 * @return bool
 */
function isLogged($logRecord, $logFilePath)
{
    return in_array($logRecord, parseList($logFilePath));
}

/**
 * Add log record to appropriate log file.
 *
 * @param string $logRecord
 * @param string $logFilePath
 */
function addLog($logRecord, $logFilePath)
{
    $file = fopen($logFilePath, 'a');
    fwrite($file, $logRecord . "\r\n");
    fclose($file);
}

/**
 * Clear logs file from duplications.
 *
 * @param string $logFilePath
 */
function clearLogsFromDuplications($logFilePath)
{
    $listBeforeClearing = parseList($logFilePath);
    $duplications = getNotUniqueArrayValues($listBeforeClearing);
    $listAfterClearing = array_merge($duplications, array_diff($listBeforeClearing, $duplications));
    file_put_contents($logFilePath, implode("\r\n", $listAfterClearing));
}
