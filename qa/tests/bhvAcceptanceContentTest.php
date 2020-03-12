<?php

class bhvAcceptanceContentTest extends bhvContentTest
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
    public function artistContentConsistent(string $artistName)
    {
        parent::artistContentConsistent($artistName);
    }

    /**
     * @test
     * @group acceptance
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function albumContentConsistent(string $artistName)
    {
        parent::albumContentConsistent($artistName);
    }
}
