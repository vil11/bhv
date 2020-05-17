<?php

class qeeNameFormatTest extends dataIntegrityTest
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
     * @test
     *
     * Artist name contains no:
     *  - uppercase
     *  - restricted symbols (see settings to edit)
     *  - additional info tags (may present for Albums & Songs only)
     *
     * @dataProvider dataArtists
     * @param string $qeeName
     * @param array $artistNames
     * @throws Exception
     */
    public function artistNameConsistent(string $qeeName, array $artistNames)
    {
        $this->assertNotEmpty($artistNames);
        foreach ($artistNames as $artistName) {
            $artist = new artistQ($artistName, $qeeName);
            $this->unit = ucfirst(get_class($artist));
            $this->path = $artist->getPath();

            $artistName = (!$this->acceptanceMode) ? $artist->getTitle() : $this->adjustName($artist->getTitle());

            $this->verifyUppercaseAbsent($artistName);
            $this->verifyWrapAbsent($artistName);
            $this->verifyRestrictedSymbolAbsent($artistName, settings::getInstance()->get('restricted_marks'));
            $this->verifyRestrictedSymbolAbsent($artistName, $this->prepareTagsDelimiters());
        }
    }
}
