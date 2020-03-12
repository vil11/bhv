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
     * Perform the QC session in order to be sure that BHV is deliverable in expected condition.
     *
     * @param array|null $phpUnitArgs
     * @throws Exception if session execution script isn't performed validly
     * @throws Exception if testing results report reading is failed
     */
    public function performQC(?array $phpUnitArgs)
    {
        say("\n\nQuality Control session:\n\n");

        $phpunitPath = dirname(PATH_APP) . DS . 'vendor/bin/phpunit';

        $cmd = 'cd ' . bendSeparatorsRight(PATH_QA);
        $cmd = system($cmd);

        $cmd = bendSeparatorsRight($phpunitPath) . ' --configuration ' . bendSeparatorsRight(PATH_QA) . 'phpunit.xml';
        if ($phpUnitArgs) {
            foreach ($phpUnitArgs as $arg => $value) {
                $cmd .= ' --' . $arg . ' ' . $value;
            }
        }

        $cmd = system($cmd);

        $result = (strpos($cmd, 'OK (') === 0) ? true : false;
        $this->finish($result);
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
