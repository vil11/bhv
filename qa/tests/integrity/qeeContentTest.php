<?php

class qeeContentTest extends dataIntegrityTest
{
    /** @var qee */
    protected $qee;

    /**
     * @return array
     * @throws Exception
     */
    public function dataArtists(): array
    {
        $qee = new qee();
        $data = wrap($qee->getArtistsListing());
        unset($qee);

        return $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function dataDemoArtists(): array
    {
        $qee = new qee();
        $data = wrap($qee->getDemoArtistsListing());
        unset($qee);

        return $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function dataProgressArtists(): array
    {
        $qee = new qee();
        $data = wrap($qee->getProgressArtistsListing());
        unset($qee);

        return $data;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function dataWaitingArtists(): array
    {
        $qee = new qee();
        $data = wrap($qee->getWaitingArtistsListing());
        unset($qee);

        return $data;
    }

    protected function setUp()
    {
        $this->qee = new qee();
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function contentConsistent()
    {
        $this->unit = ucfirst(get_class($this->qee));
        $this->path = settings::getInstance()->get('libraries/qee');

        $this->verifyExpectedFilesPresent();
    }

    /**
     * @test
     *
     * Demo Qee contains at least 1 Artist.
     * Demo Qee contains Artists in a qty meeting the config value*.
     * Demo Qee contains no files.
     *
     * @dataProvider dataDemoArtists
     * @param string $qeeName
     * @param array $artistNames
     * @throws Exception
     */
    public function demoContentConsistent(string $qeeName, array $artistNames)
    {
        $this->unit = ucfirst(get_class($this->qee));
        $this->path = $this->qee->getPaths()[$qeeName];

        $limit = settings::getInstance()->get('limits/qee_demo_artists_subqty_max');
        $this->verifyQeeContainsArtistsInLimits($artistNames, $limit);

        $this->verifyExpectedFilesPresent();
    }

    /**
     * @test
     *
     * Progress Qee contains at least 1 Artist.
     * Progress Qee contains Artists in a qty meeting the config value*.
     * Progress Qee contains no files.
     *
     * @dataProvider dataProgressArtists
     * @param string $qeeName
     * @param array $artistNames
     * @throws Exception
     */
    public function progressContentConsistent(string $qeeName, array $artistNames)
    {
        $this->unit = ucfirst(get_class($this->qee));
        $this->path = $this->qee->getPaths()[$qeeName];

        $limit = settings::getInstance()->get('limits/qee_progress_artists_subqty_max');
        $this->verifyQeeContainsArtistsInLimits($artistNames, $limit);

        $this->verifyExpectedFilesPresent();
    }

    /**
     * @test
     *
     * Waiting Qee contains at least 1 Artist.
     * Waiting Qee contains Artists in a qty meeting the config value*.
     * Waiting Qee contains no files.
     *
     * @dataProvider dataWaitingArtists
     * @param string $qeeName
     * @param array $artistNames
     * @throws Exception
     */
    public function waitingContentConsistent(string $qeeName, array $artistNames)
    {
        $this->unit = ucfirst(get_class($this->qee));
        $this->path = $this->qee->getPaths()[$qeeName];

        $limit = settings::getInstance()->get('limits/qee_waiting_artists_subqty_max');
        $this->verifyQeeContainsArtistsInLimits($artistNames, $limit);

        $this->verifyExpectedFilesPresent();

        $limit = settings::getInstance()->get('limits/qee_artist_size_max');
        foreach ($artistNames as $artistName) {
            $artist = new artistQ($artistName, $qeeName);
            $this->unit = ucfirst(get_class($artist));
            $this->path = $artist->getPath();

            // temp start: skip giant folders until solid discographies are collected
            if ($qeeName === 'to_upload_giant') {
                continue;
            }
            // temp end.

            $this->verifyFolderSize($limit);
        }
    }

    /**
     * @test
     *
     * Demo Artist contains at least 1 Album.
     * Demo Artist contains no files except: free Songs (optional), Index (required).
     *
     * @dataProvider dataDemoArtists
     * @param string $qeeName
     * @param array $artistNames
     * @throws Exception
     */
    public function demoArtistContentConsistent(string $qeeName, array $artistNames)
    {
        foreach ($artistNames as $artistName) {
            $artist = new artistQ($artistName, $qeeName);
            $this->unit = ucfirst(get_class($artist));
            $this->path = $artist->getPath();

            $this->verifyAlbumsNotAbsent($artist);

            $mandatoryFiles = array_merge(
                $this->prepareSongsPaths($artist->getFreeSongs()),
                [bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/artist_index'))]
            );
            $possibleFiles = [
                bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/artist_thumbnail')),
            ];
            $this->verifyExpectedFilesPresent($mandatoryFiles, $possibleFiles);
        }
    }

    /**
     * @param array|null $songs
     * @return array
     */
    private function prepareSongsPaths(?array $songs): array
    {
        $paths = [];
        if ($songs !== null) {
            foreach ($songs as $song) {
                $paths[] = $song->getPath();
            }
        }

        return $paths;
    }
}
