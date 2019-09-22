<?php

/**
 * Update Catalog to log the latest Beehive state:
 *  - run manually by calling the function directly from "index.php"
 *
 * @throws Exception
 */
function updateCatalog()
{
    echo "\nCatalog updating:\n";

    $bhv = new bhv();
    $result = $bhv->updateCatalog();

    $result = ($result) ? 'SUCCESS' : 'FAIL!!!';
    echo "\n\n[$result] finished.\n\n";
}

/**
 * Update Metadata of all Artist Songs or for concrete Artist Albums only:
 *  - run manually by calling the function directly from "index.php"
 *  - start Artist folder name & Album folder name with an underscore* in order to tag it for updating
 *  - if no Albums are tagged for updating, all Artist Songs will be updated
 *  - you can enable automatic renaming of tagged folders on operation success, by default this option is disabled
 *
 * @param bool $autoRenamingIfSuccess
 * @throws Exception
 */
function updateMetadata(bool $autoRenamingIfSuccess = false)
{
    echo "\nMetadata updating:\n";

    $bhv = new bhv();
    $result = $bhv->updateMetadata($autoRenamingIfSuccess);

    $result = ($result) ? 'SUCCESS' : 'FAIL!!!';
    echo "\n\n[$result] finished.\n\n";
}

/**
 * Perform the QA session in order to be sure that BHV is deliverable.
 *
 * @throws Exception if QA execution script isn't performed validly
 * @throws Exception if testing results report reading is failed
 */
//function performQA()
//{
//    echo "\nBhv testing:\n";
//
//    $cmd = sprintf('cd %s/../qa/', PROJECT_PATH);
//    $res = system(fixDirSeparatorsToTheRight($cmd));
//
//    $logFilePath = PROJECT_PATH . settings::getInstance()->get('paths/qa_report');
//    $cmd = sprintf('phpunit --configuration %s/../qa/phpunit.xml --log-tap %s', PROJECT_PATH, $logFilePath);
//    $cmd = system(fixDirSeparatorsToTheRight($cmd));
//    if ($cmd !== '') {
//        $e = $cmd;
//        throw new Exception($e);
//    }
//
//    $logFileContent = file_get_contents($logFilePath);
//    if (!$logFileContent) {
//        $e = 'testing results report reading is failed';
//        throw new Exception($e);
//    }
//    preg_match_all('|(message:).+|', $logFileContent, $fails);
//    $fails = $fails[0];
//
//    $destinationTickPath = settings::getInstance()->get('paths/qa_tick_destination');
//    if ($fails) {
//        $resultContent = '';
//        foreach ($fails as $fail) {
//            $resultContent .= substr($fail, 9);
//        }
//
//        $destinationReportPath = settings::getInstance()->get('paths/qa_result_destination');
//        $resultFilePath = PROJECT_PATH . settings::getInstance()->get('paths/qa_result');
//
//        if (!file_put_contents($resultFilePath, $resultContent)) exit('writing results to report file failed!');
//        if (!copy($resultFilePath, $destinationReportPath)) exit('copying report to desktop failed');
//
//        if (!copy(PROJECT_PATH . '../qa/reports/tick_failed.png', $destinationTickPath)) {
//            exit('copying tick_failed to desktop failed!');
//        }
//    }
//
//    echo "\n\nfinished.\n\n";
//}
