<?php

/**
 * [IN PROGRESS] Get list of all files & directories inside specified directory:
 *  - returns empty array if specified directory is empty.
 *
 * @param string $dirPath
 * @return array
 */
//function getDirContentList($dirPath)
//{
//    $dirs = getDirDirsList($dirPath);
//    $files = getDirFilesList($dirPath);
//
//    return array_merge($dirs, $files);
//}

/**
 * Get list of all files inside specified directory.
 * Function returns empty array if specified directory contains no files.
 *
 * @param string $dirPath
 * @throws Exception if dir is absent by specified path
 * @return string[]
 *
 * @tested 1.2.3
 */
function getDirFilesList(string $dirPath)
{
    if (!is_dir($dirPath)) {
        throw new Exception(prepareIssueCard("Dir is absent.", $dirPath));
    }

    $files = [];
    foreach (scandir($dirPath) as $contentElement) {
        if ($contentElement !== '.' && $contentElement !== '..') {
            if (is_file($dirPath . DS . $contentElement)) {
                $files[] = $contentElement;
            }
        }
    }

    return $files;
}

/**
 * [IN PROGRESS] Get list of all files' paths inside specified directory.
 * Function returns empty array if specified directory contains no files.
 *
 * @param string $dirPath
 * @return array
 */
//function getDirFilesPathsList($dirPath)
//{
//    $filesPaths = [];
//    foreach (getDirFilesList($dirPath) as $fileName) {
//        $filesPaths[] = $dirPath . DS . $fileName;
//    }
//
//    return $filesPaths;
//}

/**
 * Get list of all directories inside specified directory:
 *  - return empty array if specified directory contains no child directories
 *
 * @param string $dirPath
 * @throws Exception if dir is absent by specified path
 * @return string[]
 *
 * @tested 1.2.3
 */
function getDirDirsList(string $dirPath): array
{
    if (!is_dir($dirPath)) {
        throw new Exception(prepareIssueCard("Dir is absent.", $dirPath));
    }

    $dirs = [];
    foreach (scandir($dirPath) as $contentElement) {
        if ($contentElement != '.' && $contentElement != '..') {
            if (is_dir($dirPath . DS . $contentElement)) {
                $dirs[] = $contentElement;
            }
        }
    }

    return $dirs;
}

/**
 * Get list of all files (of specified extension only) inside specified directory:
 *  - use "ext" format (without "." prefix)
 *  - return empty array if specified directory contains no files of specified extension
 *
 * @param string $dirPath
 * @param string $ext
 * @throws Exception
 * @return string[]
 *
 * @tested 1.2.3
 */
function getDirFilesListByExt(string $dirPath, string $ext): array
{
    $files = [];
    foreach (getDirFilesList($dirPath) as $fileName) {
        $fileExt = getExt($dirPath . DS . $fileName);
        if ($fileExt === $ext) {
            $files[] = $fileName;
        }
    }

    return $files;
}

/**
 * [IN PROGRESS] Get list of all files' paths (of specified extension only) inside specified directory.
 * Function returns empty array if specified directory contains no files of specified extension.
 *
 * @param string $dirPath
 * @param string $ext
 * @return array
 */
//function getDirFilesPathsListByExt($dirPath, $ext)
//{
//    $filesPaths = [];
//    foreach (getDirFilesPathsList($dirPath) as $filePath) {
//        if (getExt($filePath) === $ext) {
//            $filesPaths[] = $filePath;
//        }
//    }
//
//    return $filesPaths;
//}

/**
 * Create directory if such is absent.
 *
 * @param string $dirPath
 * @throws Exception if directory was not created
 *
 * @tested 1.3.3
 */
function createDir(string $dirPath)
{
    if (!is_dir($dirPath)) {
        if (!mkdir($dirPath, 0777)) {
            throw new Exception(prepareIssueCard("Directory was not created.", $dirPath));
        }
    }
}

/**
 * [IN PROGRESS] Remove empty directory on Hard Disk Drive by specified path.
 *
 * @param string $dirPath
 */
//function removeEmptyDir($dirPath)
//{
//    if (count(getDirContentList($dirPath)) === 0) {
//        rmdir($dirPath);
//    }
//}
