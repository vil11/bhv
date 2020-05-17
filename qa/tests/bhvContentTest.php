<?php

class bhvContentTest extends dataIntegrityTest
{
    /** @var bhv */
    protected $bhv;


    /**
     * @return array
     * @throws Exception
     */
    public function dataArtists(): array
    {
        $bhv = new bhv();
        $data = wrap($bhv->getArtistsListing());
        unset($bhv);

        return $data;
    }

    protected function setUp()
    {
       $this->bhv = new bhv();
    }


    /**
     * @test
     *
     * Artist name is unique:
     *  - among all containers (BHV pool, all Qees)
     *  - if Artist name starts from "the ", verify that the same Artist name without "the " is absent among all containers
     *  - TODO: add POLLEN state to pool
     */
    public function artistsAreNotDuplicated()
    {
        $qees = new qee();
        $this->unit = 'artistB';

        $qeeArtists = [];
        foreach ($qees->getArtistsListing() as $qeeName => $qee) {
            $this->verifyDuplicatingsAbsent($qeeArtists, $qee, '"%s" is duplicated somewhere among Qees.');
            $qeeArtists = array_merge($qeeArtists, $qee);
        }

        $this->verifyDuplicatingsAbsent($this->bhv->getArtistsListing(), $qeeArtists, '"%s" is already in BHV. Remove it from Qee.');
        $artists = array_merge($this->bhv->getArtistsListing(), $qeeArtists);

        $this->verifyPrefixDuplicatingsAbsent($artists);
    }

    /**
     * @test
     *
     * Beehive contains no files except: Catalog (required), icon (required).
     *
     * @throws Exception
     */
    public function bhvContentConsistent()
    {
        $this->unit = strtoupper(get_class($this->bhv));
        $this->path = $this->bhv->getPath();

        $files = [
            bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/bhv_catalog')),
            bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/bhv_icon')),
        ];
        $this->verifyOnlyExpectedFilesPresent($files);
    }

    /**
     * @test
     *
     * Every Catalog entry is present in BHV.
     *
     * @throws Exception
     */
    public function bhvCatalogConsistent()
    {
        $this->path = $this->bhv->getPath();

        $catalog = $this->bhv->getCatalog();
        foreach ($catalog as $entry) {
            $this->verifyCatalogEntryPresent($entry);
        }
    }

    /**
     * @ test
     *
     * Artist has all its files catalogued.
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function bhvArtistFilesCatalogued(string $artistName)
    {
        $artist = new artistB($artistName);
        $this->path = $artist->getPath();

        $catalog = $this->bhv->getCatalog();
        $this->verifyArtistFilesCatalogued($catalog, $artist);
    }

    /**
     * @test
     *
     * Artist contains at least 1 Album.
     * Artist contains no files except: free Songs (optional), Index (required).
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function artistContentConsistent(string $artistName)
    {
        $artist = new artistB($artistName);
        $this->unit = ucfirst(get_class($artist));
        $this->path = $artist->getPath();

        $this->verifyAlbumsNotAbsent($artist);

        $files = array_merge(
            $this->prepareSongsPaths($artist->getFreeSongs()),
            [bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/artist_index'))]
        );
        $this->verifyOnlyExpectedFilesPresent($files);
    }

//    /**
//     * @test
//     */
//    public function artistIndexConsistent()
//    {
//        $this->markTestSkipped('TBD');
//    }

    /**
     * @test
     *
     * Album contains at least 1 Song.
     * Album contains all Songs in consistent order.
     *
     * Album contains no files except: Songs (required), Cover (required).
     * Album contains no folders.
     *
     * Cover has its width equals to height & meets the config value*.
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function albumContentConsistent(string $artistName)
    {
        $artist = new artistB($artistName);
        /** @var albumInterface $album */
        foreach ($artist->getAlbums() as $album) {
            $this->unit = ucfirst(get_class($album));
            $this->path = $album->getPath();

            $this->verifySongsNotAbsent($album);
            $this->verifySongsOrdered($album);

            $files = array_merge(
                $this->prepareSongsPaths($artist->getSongs()),
                [bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/album_thumbnail'))]
            );
            $this->verifyOnlyExpectedFilesPresent($files);
            $this->verifyAlbumHasNoFolders($album);

            $this->verifyAlbumThumbnail();
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
