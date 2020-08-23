<?php

class bhvAcceptanceNameFormatTest extends bhvNameFormatTest
{
    public $acceptanceMode = true;


    /**
     * @return array
     * @throws Exception
     */
    public function dataArtists(): array
    {
        $bhv = new bhv();
        $list = $bhv->getNewArtistsListing();
        if (empty($list)) {
            echo "\nArtists for Metadata updating are absent\n";
        }
        $data = wrap($list);
        unset($bhv);

        return $data;
    }


    /**
     * @test
     * @group acceptance
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function artistNameConsistent(string $artistName)
    {
        parent::artistNameConsistent($artistName);
    }

    /**
     * @test
     * @group acceptance
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function albumNameConsistent(string $artistName)
    {
        parent::albumNameConsistent($artistName);
    }

    /**
     * @test
     * @group acceptance
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function songNameConsistent(string $artistName)
    {
        parent::songNameConsistent($artistName);
    }
}
