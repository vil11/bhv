<?php

class bhvAcceptanceMetadataTest extends bhvMetadataTest
{
    public $acceptanceMode = true;


    /**
     * @return array
     * @throws Exception
     */
    public function dataArtists(): array
    {
        $bhv = new bhv();
        $data = wrap($bhv->getNewArtistsListing());
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
    public function songMetadataConsistent(string $artistName)
    {
        parent::songMetadataConsistent($artistName);
    }
}
