<?php

class metadataTest extends tests_abstract
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
}
