<?php

class bhvNameFormatTest extends tests_abstract
{
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


    /**
     * @test
     *
     * Artist name contains no:
     *  - uppercase
     *  - restricted symbols (see settings to edit)
     *  - additional info tags (may present for Albums & Songs only)
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function artistNameConsistent(string $artistName)
    {
        $artist = new artist($artistName);
        $this->unit = ucfirst(get_class($artist));
        $this->path = $artist->getPath();

        $this->verifyUppercaseAbsent($artist->getTitle());
        $this->verifyWrapAbsent($artist->getTitle());
        $this->verifyRestrictedSymbolAbsent($artist->getTitle(), settings::getInstance()->get('restricted_marks'));
        $this->verifyRestrictedSymbolAbsent($artist->getTitle(), $this->prepareTagsDelimiters());
    }

    /**
     * @test
     *
     * Album name contains no:
     *  - uppercase
     *  - restricted symbols (see settings to edit)
     *
     * Album name follows the format:
     *  - year of release (required)
     *  - allowed record type (required)
     *  - title (required)
     *  - allowed additional info tags (optional)
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function albumNameConsistent(string $artistName)
    {
        $artist = new artist($artistName);
        /** @var album $album */
        foreach ($artist->getAlbums() as $album) {
            $this->unit = ucfirst(get_class($album));
            $this->path = $album->getPath();

            $this->verifyUppercaseAbsent($album->getTitle());
            $this->verifyWrapAbsent($album->getTitle());
            $this->verifyRestrictedSymbolAbsent($album->getTitle(), settings::getInstance()->get('restricted_marks'));

            $data = $album->getData();
            $this->verifyDataPresent('release year', $data['released']);
            $this->verifyDataPresent('record type', $data['type']);
            $this->verifyRecordTypeValid($data['type']);
            $this->verifyDataPresent('title', $data['title']);
            $this->verifyWrapAbsent($data['title']);
            $tags = (array_key_exists('tags', $data)) ? $this->verifyTagsValid($data['tags']) : '';
            $this->verifyAlbumFormatValid($album->getTitle(), $data, $tags);
        }
    }

    /**
     * @test
     *
     * Song name contains no:
     *  - uppercase
     *  - restricted symbols (see settings to edit)
     *
     * Song name follows the format:
     *  - inside the Album:
     *      - track position (required)
     *      - title (required)
     *      - allowed additional info tags (optional)
     *  - outside the Album:
     *      - title (required)
     *      - allowed additional info tags (optional)
     *
     * @dataProvider dataArtists
     * @param string $artistName
     * @throws Exception
     */
    public function songNameConsistent(string $artistName)
    {
        $artist = new artist($artistName);
        /** @var song $song */
        foreach ($artist->getSongs() as $song) {
            $this->unit = ucfirst(get_class($song));
            $this->path = $song->getPath();

            $this->verifyUppercaseAbsent($song->getTitle());
            $this->verifyWrapAbsent($song->getTitle());
            $this->verifyRestrictedSymbolAbsent(pathinfo($this->path)['filename'], settings::getInstance()->get('restricted_marks'));

            $data = $song->getData();
            if ($song->getAlbumData()) {
                $this->verifyDataPresent('track position', $data['position']);
                $this->verifyDataPresent('title', $data['title']);
                $this->verifyWrapAbsent($data['title']);
                $tags = (array_key_exists('tags', $data)) ? $this->verifyTagsValid($data['tags']) : '';
                $this->verifyAlbumSongFormatValid($song->getTitle(), $data, $tags);
            } else {
                $this->verifyDataPresent('title', $data['title']);
                $this->verifyWrapAbsent($data['title']);
                $tags = (array_key_exists('tags', $data)) ? $this->verifyTagsValid($data['tags']) : '';
                $this->verifyArtistSongFormatValid($song->getTitle(), $data, $tags);
            }
        }
    }
}
