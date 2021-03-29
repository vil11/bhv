<?php

require_once 'app/boot/bootstrap.php';

/**
 * Beehive app features. Run manually by calling functions directly from "index.php".
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
     * Update Catalog to log the latest state of Beehive shadow.
     *
     * @throws Exception
     */
    public function updateCatalog()
    {
        say("\n\nCatalog updating:");
        $result = $this->bhv->resetCatalog();
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
        say("\n\nMetadata updating:");
        $this->bhv->updateMetadata($autoRenamingIfSuccess);
        $this->finish(true);
    }

    /**
     * Perform the QC session in order to be sure that the Beehive is deliverable in expected condition.
     *
     * @param array|null $phpUnitArgs
     * @throws Exception if session execution script isn't performed validly
     * @throws Exception if testing results report reading is failed
     */
    public function performQC(?array $phpUnitArgs)
    {
        say("\n\nQuality Control session:\n\n");

        $cmd = 'cd ' . bendSeparatorsRight(PATH_QA);
        $cmd = system($cmd);

        $cmd = bendSeparatorsRight(PATH_VENDOR . 'bin' . DS . 'phpunit') . ' --configuration ' . bendSeparatorsRight(PATH_QA) . 'phpunit.xml';
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
     * Download Albums (from mzka.clb) by "view Album" pages' urls.
     *  - (i) mzka.clb provides limited qty of downloads per day
     *
     * @param string[] $urls
     * @throws Exception
     */
    public function downloadAlbums(array $urls)
    {
        say("\n\nDownloading Albums:\n\n");
        foreach ($urls as $url) {
            if ($url === '') {
                say("\n");
                continue;
            }

            $album = new albumR($url);
            $result = $album->downloadSongs();
            if (!$result) {
                $this->finish($result);
            }
        }
        $this->finish(true);
    }

    /** @param bool $result */
    private function finish(bool $result)
    {
        say("\n\n[");
        if ($result) {
            say('SUCCESS!', 'green');
        } else {
            say('FAIL!!!', 'red');
        }
        say("]\n");
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
