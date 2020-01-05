<?php

require_once 'app/boot/bootstrap.php';

/**
 * Beehive app features. Run manually by calling the function directly from "index.php".
 */
class features
{
    /** @var bhv */
    private $bhv;

    public function __construct()
    {
        $this->bhv = new bhv();
    }


    /**
     * Update Catalog to log the latest Beehive state.
     *
     * @throws Exception
     */
    public function updateCatalog()
    {
        $result = $this->bhv->updateCatalog();
        $this->finish($result);
    }

    /**
     * Update Metadata of all Artist Songs or for concrete Artist Albums only:
     *  - start Artist folder name & Album folder name with an underscore* in order to tag it for updating
     *  - if no Albums are tagged for updating, all Artist Songs will be updated
     *  - you can enable automatic renaming of tagged folders on operation success, by default this option is disabled
     *
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     */
    public function updateMetadata(bool $autoRenamingIfSuccess = false)
    {
        $result = $this->bhv->updateMetadata($autoRenamingIfSuccess);
        $this->finish($result);
    }

    /**
     * Perform the QC session in order to be sure that BHV is deliverable.
     *
     * @throws Exception if session execution script isn't performed validly
     * @throws Exception if testing results report reading is failed
     */
    public function performQC()
    {
        say("\n\nQuality Control session:");

        $qaPath = bendSeparatorsRight(dirname(APP_PATH) . DS . 'qa' . DS);
        $cmd = 'cd ' . $qaPath;
        $cmd = system($cmd);

//        $logFilePath = PROJECT_PATH . settings::getInstance()->get('paths/qa_report');
//        $cmd = 'phpunit --configuration ' . $qaPath . 'phpunit.xml --log-tap ' . $logFilePath;
        $cmd = 'phpunit --configuration ' . $qaPath . 'phpunit.xml';
        $cmd = system($cmd);


//        $logFileContent = file_get_contents($logFilePath);
//        if (!$logFileContent) {
//            $e = 'testing results report reading is failed';
//            throw new Exception($e);
//        }
//        preg_match_all('|(message:).+|', $logFileContent, $fails);
//        $fails = $fails[0];
//
//        $destinationTickPath = settings::getInstance()->get('paths/qa_tick_destination');
//        if ($fails) {
//            $resultContent = '';
//            foreach ($fails as $fail) {
//                $resultContent .= substr($fail, 9);
//            }
//
//            $destinationReportPath = settings::getInstance()->get('paths/qa_result_destination');
//            $resultFilePath = PROJECT_PATH . settings::getInstance()->get('paths/qa_result');
//
//            if (!file_put_contents($resultFilePath, $resultContent)) exit('writing results to report file failed!');
//            if (!copy($resultFilePath, $destinationReportPath)) exit('copying report to desktop failed');
//
//            if (!copy(PROJECT_PATH . '../qa/reports/tick_failed.png', $destinationTickPath)) {
//                exit('copying tick_failed to desktop failed!');
//            }
//        }

        $this->finish(true);
    }

    /**
     * @param bool $result
     */
    protected function finish(bool $result)
    {
        if ($result) {
            say("\n\n[SUCCESS!]\n", 'green');
        } else {
            say("\n\n[FAIL!!!]\n", 'red');
        }
    }


//public function autoCreateArtistIndex()
//{
//    $bhv = new bhv();
//    foreach ($bhv->getArtistsListing() as $artistName) {
//        $artist = new artist($artistName);
//
//        $index = $artist->getPath() . '/x.txt';
//        $xPresent = (file_exists($index) && is_file($index) && is_readable($index));
//        if ($xPresent === false) {
//            $x = fopen($index, "w");
//            fclose($x);
//        }
//    }
//}
}
