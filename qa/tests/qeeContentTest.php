<?php

class qeeContentTest extends dataIntegrityTest
{
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
        $this->assertNotEmpty($artistNames);
        foreach ($artistNames as $artistName) {
            $artist = new artistQ($artistName, $qeeName);
            $this->unit = ucfirst(get_class($artist));
            $this->path = $artist->getPath();

            $this->verifyAlbumsNotAbsent($artist);

            $files = array_merge(
                $this->prepareSongsPaths($artist->getFreeSongs()),
                [bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/artist_index'))]
            );
            $this->verifyOnlyExpectedFilesPresent($files);
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
