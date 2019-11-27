<?php

class bhvContentTest extends tests_abstract
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
     *  - among all containers (BHV pool, all Queues)
     *  - if Artist name starts from "the ", verify that the same Artist name without "the " is absent among all containers
     *  - TODO: add POLLEN state to pool
     */
    public function artistsAreNotDuplicated()
    {
        $queues = new queue();
        $this->unit = 'Artist';

        $queueArtists = [];
        foreach ($queues->getArtistsListing() as $queueName => $queue) {
            $this->verifyDuplicatingsAbsent($queueArtists, $queue, '"%s" is duplicated somewhere among Queues.');
            $queueArtists = array_merge($queueArtists, $queue);
        }

        $this->verifyDuplicatingsAbsent($this->bhv->getArtistsListing(), $queueArtists, '"s" is already in BHV. Remove it from Queue.');
        $artists = array_merge($this->bhv->getArtistsListing(), $queueArtists);

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
     * Every Catalog entry is still present in BHV.
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
     * @test
     *
     * Artist contains at least 1 Album.
     * Artist contains no files except: free Songs (optional), Index (required).
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function artistContentConsistent($artistName)
    {
        $artist = new artist($artistName);
        $this->unit = ucfirst(get_class($artist));
        $this->path = $artist->getPath();

        $this->verifyAlbumsNotAbsent($artist);

        $files = array_merge(
            $this->prepareSongsPaths($artist->getFreeSongs()),
            [bendSeparatorsRight($this->path . DS . settings::getInstance()->get('paths/artist_index'))]
        );
        $this->verifyOnlyExpectedFilesPresent($files);
    }

    /**
     * @test
     */
    public function artistIndexConsistent()
    {
        $this->markTestSkipped('TBD');
    }

    /**
     * @test
     *
     * Album contains at least 1 Song.
     * Album contains all Songs in consistent order.
     * Album contains no files except: Songs (required), Cover (required).
     * Album contains no folders.
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function albumContentConsistent($artistName)
    {
        $artist = new artist($artistName);
        /** @var album $album */
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
        }
    }

    /**
     * @test
     *
     * Cover has its width equals to height & meets the config value*
     * Look for metatags tags of jpg as well.
     */
    public function albumThumbnailConsistent()
    {
        $this->markTestSkipped('TBD');
    }

    /**
     * @test
     *
     * Song has correct ID3 meta tags:
     *  - inside the Album:
     *      - "title" = correspondent Song title + its additional info
     *      - "artist" = correspondent Artist title
     *      - "album" = correspondent Album title + its additional info
     *      - "year" = the year of correspondent Album release
     *      - "track" = correspondent Song track position in correspondent Album
     *      - "publisher" meets the config value
     *      - "img" = correspondent Album Cover
     *  - outside the Album:
     *      - "title" = correspondent Song title + its additional info
     *      - "artist" = correspondent Artist title + its additional info
     *      - "publisher" meets the config value
     * Song has its other meta tags blank.
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function songMetadataConsistent($artistName)
    {
        $artist = new artist($artistName);
        /** @var song $song */
        foreach ($artist->getSongs() as $song) {
            $this->unit = ucfirst(get_class($song));
            $this->path = $song->getPath();

            $this->verifySongMetadata($song);
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
