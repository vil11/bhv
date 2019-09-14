<?php

/**
 * Get list of all files & directories inside specified directory.
 * Function returns empty array if specified directory is empty.
 *
 * @param string $dirPath
 * @return array
 */
function getDirContentList($dirPath)
{
    $dirs = getDirDirsList($dirPath);
    $files = getDirFilesList($dirPath);

    return array_merge($dirs, $files);
}

/**
 * Get list of all files inside specified directory.
 * Function returns empty array if specified directory contains no files.
 *
 * @param string $dirPath
 * @return array
 * @throws Exception if input path is invalid
 */
function getDirFilesList($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new Exception("Path to [$dirPath] is invalid.");
    }

    $files = [];
    foreach (scandir($dirPath) as $contentElement) {
        if ($contentElement != '.' && $contentElement != '..') {
            if (is_file($dirPath . DS . $contentElement)) {
                $files[] = $contentElement;
            }
        }
    }

    return $files;
}

/**
 * Get list of all files' paths inside specified directory.
 * Function returns empty array if specified directory contains no files.
 *
 * @param string $dirPath
 * @return array
 */
function getDirFilesPathsList($dirPath)
{
    $filesPaths = [];
    foreach (getDirFilesList($dirPath) as $fileName) {
        $filesPaths[] = $dirPath . DS . $fileName;
    }

    return $filesPaths;
}

/**
 * Get list of all directories inside specified directory.
 * Function returns empty array if specified directory contains no directories.
 *
 * @param string $dirPath
 * @return array
 * @throws Exception if input path is invalid
 */
function getDirDirsList($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new Exception("Path to [$dirPath] is invalid.");
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
 * Get list of all files (of specified extension only) inside specified directory.
 * Function returns empty array if specified directory contains no files of specified extension.
 *
 * @param string $dirPath
 * @param string $ext
 * @return array
 */
function getDirFilesListByExt($dirPath, $ext)
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
 * Get list of all files' paths (of specified extension only) inside specified directory.
 * Function returns empty array if specified directory contains no files of specified extension.
 *
 * @param string $dirPath
 * @param string $ext
 * @return array
 */
function getDirFilesPathsListByExt($dirPath, $ext)
{
    $filesPaths = [];
    foreach (getDirFilesPathsList($dirPath) as $filePath) {
        if (getExt($filePath) === $ext) {
            $filesPaths[] = $filePath;
        }
    }

    return $filesPaths;
}

/**
 * Create directory on Hard Disk Drive by specified path.
 *
 * @param string $dirPath
 * @throws Exception if directory is not created
 */
function createDir($dirPath)
{
    if (!is_dir($dirPath)) {
        if (!mkdir($dirPath, 0777)) {
            throw new Exception("Directory [$dirPath] is not created.");
        }
    }
}

/**
 * Remove empty directory on Hard Disk Drive by specified path.
 *
 * @param string $dirPath
 */
function removeEmptyDir($dirPath)
{
    if (count(getDirContentList($dirPath)) === 0) {
        rmdir($dirPath);
    }
}
