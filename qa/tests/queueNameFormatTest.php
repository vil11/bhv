<?php

class queueNameFormatTest extends dataIntegrityTest
{
    /**
     * @return array
     * @throws Exception
     */
    public function dataArtists(): array
    {
        $queue = new queue();
        $data = wrap($queue->getArtistsListing());
        unset($queue);

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
     * @param string $queueName
     * @param array $artistNames
     * @throws Exception
     */
    public function artistNameConsistent(string $queueName, array $artistNames)
    {
        foreach ($artistNames as $artistName) {
            $this->unit = 'Queue Artist';
            $this->path = settings::getInstance()->get('libraries/queue') . DS . $queueName . DS . $artistName;

            $this->verifyUppercaseAbsent($artistName);
            $this->verifyWrapAbsent($artistName);
            $this->verifyRestrictedSymbolAbsent($artistName, settings::getInstance()->get('restricted_marks'));
            $this->verifyRestrictedSymbolAbsent($artistName, $this->prepareTagsDelimiters());
        }
    }
}
