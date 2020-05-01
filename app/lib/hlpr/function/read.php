<?php

/**
 * Check if file is valid for further using.
 *
 * @param string $filePath
 * @return bool
 *
 * @tested 1.2.4
 */
function isFileValid(string $filePath): bool
{
    return (file_exists($filePath) && is_file($filePath) && is_readable($filePath) && filesize($filePath) > 0);
}

/**
 * Get file extension in "ext" format (without "." prefix).
 *
 * @param string $filePath
 * @return string
 *
 * @tested 1.2.3
 */
function getExt(string $filePath): string
{
    return pathinfo($filePath, PATHINFO_EXTENSION);
}

/**
 * [IN PROGRESS] Check if 2 not broken files are the same or not.
 *
 * @param string $firstFilePath
 * @param string $secondFilePath
 * @return bool
 */
//function filesAreEqual($firstFilePath, $secondFilePath)
//{
//    if (getExt($firstFilePath) !== getExt($secondFilePath)) {
//        return false;
//    }
//    if (file_get_contents($firstFilePath) !== file_get_contents($secondFilePath)) {
//        return false;
//    }
//
//    if (fileIsImage($firstFilePath) && !imgsAreEqual($firstFilePath, $secondFilePath)) {
//        return false;
//    }
//
//    return true;
//}

/**
 * [IN PROGRESS] Check if file is image or not.
 *
 * @param string $filePath
 * @return bool
 */
//function fileIsImage($filePath)
//{
//    return getimagesize($filePath) ? true : false;
//}

/**
 * [IN PROGRESS] Check if 2 images are the same or not.
 * Images can be called by path on Hard Disk Drive or by URL.
 * At least 1 of files must be called by path on Hard Disk Drive.
 *
 * @param string $firstImgPath
 * @param string $secondImgPath
 * @return bool
 * @throws Exception if it is impossible to render image file properties
 */
//function imgsAreEqual($firstImgPath, $secondImgPath)
//{
//    if (!($firstImgProperties = getimagesize($firstImgPath))) {
//        throw new Exception("Path to [$firstImgPath] is invalid.");
//    }
//    if (!($secondImgProperties = getimagesize($secondImgPath))) {
//        throw new Exception("Path to [$secondImgPath] is invalid.");
//    }
//
//    if ($firstImgProperties !== $secondImgProperties) {
//        return false;
//    }
//    foreach ($firstImgProperties as $key => $value) {
//        if ($firstImgProperties[$key] !== $secondImgProperties[$key]) {
//            return false;
//        }
//    }
//
//    return true;
//}

/**
 * Download file by URL.
 *
 * @param string $url
 * @param string $saveFilePath
 *
 * @tested 1.3.3
 */
function downloadFile(string $url, string $saveFilePath)
{
    $fp = fopen($saveFilePath, 'wb');

    $ch = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_CONNECTTIMEOUT => 1488,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_FILE => $fp,
    ];
    curl_setopt_array($ch, $options);

    curl_exec($ch);
    curl_close($ch);

    fclose($fp);
}

/**
 * [IN PROGRESS] Wait while file is loading.
 *
 * @param string $fileUrl
 */
//function waitForLoading($fileUrl)
//{
//    sleep(2);
//    $delay = getFileSize($fileUrl) / 30000000;
//    sleep($delay);
//}

/**
 * [IN PROGRESS] Check if file is ready to be downloaded:
 *  - file size < correspondent limit (default file size limit is 1gb);
 *  - Hard Disk Drive free space > correspondent limit (default available space limit is 10gb).
 *
 * @param string $fileUrl
 * @param string $saveFilePath
 * @return bool
 */
//function isFileReadyToDownload($fileUrl, $saveFilePath)
//{
//    if (getFileSize($fileUrl) > 1073741824) {
//        return false;
//    }
//    if (disk_free_space($saveFilePath) < 10737418240) {
//        return false;
//    }
//
//    return true;
//}

/**
 * Get file size.
 *
 * @param string $url
 * @return float
 *
 * @tested 1.3.5
 */
function getFileSize(string $url): float
{
    $ch = curl_init();
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_CONNECTTIMEOUT => 1488,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
        CURLOPT_FOLLOWLOCATION => true,
    ];
    curl_setopt_array($ch, $options);

    curl_exec($ch);
    $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    curl_close($ch);

    return $fileSize;
}

/**
 * [IN PROGRESS] Parse CSV file & convert pulled data to array of arrays.
 * Function returns empty array if input file is empty.
 *
 * @param string $filePath
 * @param string $cellDelimiter
 * @return array
 * @throws Exception if input path is invalid
 */
//function parseCsvTable($filePath, $cellDelimiter = ',')
//{
//    if (!isFileValid($filePath)) {
//        throw new Exception("Path to [$filePath] is invalid.");
//    }
//
//    $header = null;
//    $data = [];
//    $handle = fopen($filePath, 'r');
//    while ($row = fgetcsv($handle, 1000, $cellDelimiter)) {
//        if (!$header) {
//            $header = $row;
//        } else {
//            $data[] = array_combine($header, $row);
//        }
//    }
//    fclose($handle);
//
//    return $data;
//}

/**
 * [IN PROGRESS] Get full record by 1 of its values.
 *
 * @param string $filePath
 * @param string $value
 * @return null|string
 */
//function getCsvRecord($filePath, $value)
//{
//    foreach (parseCsvTable($filePath) as $record) {
//        if (in_array($value, $record)) {
//            return $record;
//        }
//    }
//}

/**
 * Parse list from txt file:
 *  - returns empty array if specified file is empty
 *
 * @param string $filePath
 * @param string $delimiter
 * @return array
 *
 * @tested 1.2.4
 */
function parseList(string $filePath, string $delimiter = "\r\n"): array
{
    $content = explode($delimiter, file_get_contents($filePath));
    foreach ($content as $key => $record) {
        if (trim($record) === '') {
            unset($content[$key]);
        }
    }

    return array_values($content);
}

/**
 * [IN PROGRESS] Check if log record is already present in appropriate log file or not.
 *
 * @param string $logRecord
 * @param string $logFilePath
 * @return bool
 */
//function isLogged($logRecord, $logFilePath)
//{
//    return in_array($logRecord, parseList($logFilePath));
//}

/**
 * Add a record to an appropriate log file.
 *
 * @param string $logRecord
 * @param string $logFilePath
 *
 * @tested 1.2.9
 */
function addLog($logRecord, $logFilePath)
{
    $file = fopen($logFilePath, 'a');
    fwrite($file, $logRecord . "\r\n");
    fclose($file);
}

/**
 * [IN PROGRESS] Clear logs file from duplications.
 *
 * @param string $logFilePath
 */
//function clearLogsFromDuplications($logFilePath)
//{
//    $listBeforeClearing = parseList($logFilePath);
//    $duplications = getNotUniqueArrayValues($listBeforeClearing);
//    $listAfterClearing = array_merge($duplications, array_diff($listBeforeClearing, $duplications));
//    file_put_contents($logFilePath, implode("\r\n", $listAfterClearing));
//}
