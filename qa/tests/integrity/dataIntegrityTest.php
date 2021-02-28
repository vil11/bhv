<?php

abstract class dataIntegrityTest extends PHPUnit\Framework\TestCase
{
    public $acceptanceMode = false;

    protected $unit;
    protected $path;


    /** @return array */
    protected function prepareTagsDelimiters(): array
    {
        return [
            settings::getInstance()->get('delimiters/tag_open'),
            settings::getInstance()->get('delimiters/tag_close'),
            settings::getInstance()->get('delimiters/tag_name'),
            settings::getInstance()->get('delimiters/tag_info'),
        ];
    }

    /**
     * @param string $string
     * @return string
     */
    protected function adjustName(string $string): string
    {
        $updatePrefixMark = settings::getInstance()->get('tags/update_metadata');
        $string = substr($string, strlen($updatePrefixMark));

        return $string;
    }


    /** @param string $title */
    protected function verifyUppercaseAbsent(string $title)
    {
        $err = err('Remove uppercase from "%s" %s name.', $title, $this->unit);
        $err = prepareIssueCard($err, $this->path);

        $this->assertSame(mb_strtolower($title), $title, $err);
    }

    /** @param string $title */
    protected function verifyWrapAbsent(string $title)
    {
        $err = err('Remove wrapper from "%s" %s name.', $title, $this->unit);
        $err = prepareIssueCard($err, $this->path);

        $this->assertSame(trim($title), $title, $err);
    }

    /**
     * @param string $title
     * @param array $restricted
     */
    protected function verifyRestrictedSymbolAbsent(string $title, array $restricted)
    {
        foreach ($restricted as $mark) {
            $err = err('Remove "%s" from %s name.', $mark, $this->unit);
            $err = prepareIssueCard($err, $this->path);

            $this->assertNotContains($mark, $title, $err);
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    protected function verifyDataPresent(string $key, string $value)
    {
        $err = err('Specify "%s" in %s name.', $key, $this->unit);
        $err = prepareIssueCard($err, $this->path);

        $this->assertNotEmpty($value, $err);
    }

    /** @param string $type */
    protected function verifyRecordTypeValid(string $type)
    {
        $allowed = settings::getInstance()->get('record_types');

        $err = err('Record type is invalid in %s name.', $this->unit);
        $err = prepareIssueCard($err, $this->path);

        $this->assertTrue(in_array($type, $allowed), $err);
    }

    /**
     * @param array $data
     * @return string
     */
    protected function verifyTagsValid(array $data): string
    {
        $delimiters = settings::getInstance()->get('delimiters');
        $allowed = settings::getInstance()->get('info_tags');

        $tags = $delimiters['section'];
        foreach ($data as $k => $v) {
            $v = implode($delimiters['tag_info'], $v);

            $err = err('"%s" tag type is invalid in %s name.', $k, $this->unit);
            $err = prepareIssueCard($err, $this->path);
            if ($k === 'info') {
                $this->assertTrue(key_exists($k, $allowed), $err);
                $tags .= $delimiters['tag_open'] . $v . $delimiters['tag_close'];
            } else {
                $this->assertTrue(in_array($k, $allowed), $err);
                $tags .= $delimiters['tag_open'] . $k . $delimiters['tag_name'] . $v . $delimiters['tag_close'];
            }

//            if ($k === $allowed['cover']) {
//                $err = prepareIssueCard('Impossible to make cover to several authors.', $this->path);
//                $this->assertSame(1, count($data[$allowed['cover']]), $err);
//            }
        }

        return $tags;
    }

    /**
     * @param string $title
     * @param array $data
     * @param string $tags
     */
    protected function verifyAlbumFormatValid(string $title, array $data, string $tags)
    {
        $delimiters = settings::getInstance()->get('delimiters');
        $format =
            $data['released']
            . $delimiters['section']
            . $delimiters['tag_open'] . $data['type'] . $delimiters['tag_close']
            . $delimiters['section']
            . $data['title'] . $tags;

        $err = err('Invalid %s name format.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertSame($format, $title, $err);
    }

    /**
     * @param string $title
     * @param array $data
     * @param string $tags
     */
    protected function verifyAlbumSongFormatValid(string $title, array $data, string $tags)
    {
        $format =
            $data['position']
            . settings::getInstance()->get('delimiters/song_position')
            . $data['title'] . $tags
            . '.' . settings::getInstance()->get('extensions/music');

        $err = err('Invalid %s name format.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertSame($format, $title, $err);
    }

    /**
     * @param string $title
     * @param array $data
     * @param string $tags
     */
    protected function verifyArtistSongFormatValid(string $title, array $data, string $tags)
    {
        $format =
            $data['title'] . $tags
            . '.' . settings::getInstance()->get('extensions/music');

        $err = err('Invalid %s name format.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertSame($format, $title, $err);
    }

    /**
     * @param array $libA
     * @param array $libB
     * @param string $err
     */
    protected function verifyDuplicatingsAbsent(array $libA, array $libB, string $err)
    {
        $duplicate = array_intersect($libA, $libB);
        $duplicate = implode(", ", $duplicate);

        $err = err($err, $duplicate);
        $err = prepareIssueCard($err);
        $this->assertEmpty($duplicate, $err);
    }

    /** @param array $names */
    protected function verifyPrefixDuplicatingsAbsent(array $names)
    {
        foreach ($names as $name) {
            $prefix = substr($name, 0, 4);
            if ($prefix === 'the ') {
                $err = err('"%s" %s is duplicated with prefix', str_replace($prefix, '', $name), $this->unit);
                $err = err($err . ' "%s". ', $prefix);
                $err = err($err . 'Please compare it to "%s" %s.', $name, $this->unit);
                $err = prepareIssueCard($err);
                $this->assertFalse(in_array(substr($name, 4), $names), $err);
            }
        }
    }

    /**
     * @param array $expectedMandatoryFiles
     * @param array $expectedPossibleFiles
     * @throws Exception
     */
    protected function verifyExpectedFilesPresent(array $expectedMandatoryFiles = [], array $expectedPossibleFiles = [])
    {
        $actual = [];
        foreach (getDirFilesList($this->path) as $file) {
            $actual[] = bendSeparatorsRight($this->path . DS . $file);
        }

        $diff = array_diff($actual, $expectedMandatoryFiles);

        if (!empty($diff) && !empty($expectedPossibleFiles)) {
            $diff = array_diff($diff, $expectedPossibleFiles);
        }

        $err = err('Files list is unexpected in %s root folder.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertSame([], $diff, $err);
    }

    /**
     * @param artistInterface $artist
     * @throws Exception
     */
    protected function verifyAlbumsNotAbsent(artistInterface $artist)
    {
        $albumsQtyMin = (int)settings::getInstance()->get('limits/artist_albums_qty_min');
        $albumsQty = count($artist->getAlbums());

        $err = err('"%s" %s contains no Albums. Where are they?', $artist->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual($albumsQtyMin, $albumsQty, $err);
    }

    /**
     * @param albumInterface $album
     * @throws Exception
     */
    protected function verifySongsNotAbsent(albumInterface $album)
    {
        $songsQtyLimit = (int)settings::getInstance()->get('limits/album_songs_qty_min');
        $songsQty = count($album->getSongs());

        $err = err('"%s" %s contains no Songs. Where are they?', $album->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual($songsQtyLimit, $songsQty, $err);
    }

    /**
     * @param albumInterface $album
     * @throws Exception
     */
    protected function verifySongsOrdered(albumInterface $album)
    {
        $songs = $album->getSongs();

        $err = err('"%s" %s contains Songs in invalid order. Reorder them.', $album->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertSame(count($songs), (integer)end($songs)->getData()['position'], $err);
        /** @var songInterface $song */
        foreach ($songs as $position => $song) {
            $this->assertSame($position + 1, (integer)$song->getData()['position'], $err);
        }
    }

    /**
     * @param albumInterface $album
     * @throws Exception
     */
    protected function verifyAlbumHasNoFolders(albumInterface $album)
    {
        $dirs = getDirDirsList($this->path);

        $err = err('"%s" %s contains folder.', $album->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertEmpty($dirs, $err);
    }

    protected function verifyAlbumThumbnail()
    {
        $min = (int)settings::getInstance()->get('limits/imgs_size_min');
        $max = (int)settings::getInstance()->get('limits/imgs_size_max');
        $thumbPath = $this->path . DS . settings::getInstance()->get('paths/album_thumbnail');
        try {
            $height = exif_read_data($thumbPath)['COMPUTED']['Height'];
            $width = exif_read_data($thumbPath)['COMPUTED']['Width'];
        } catch (Exception $e) {
            $err = '%s Thumbnail is broken or absent. Make sure extension is allowed or try to replace it.';
            $err = err($err, $this->unit);
            $err = prepareIssueCard($err, $this->path);
            echo $err, "\n\n", $e->getMessage();
        }

        $err = err('%s Thumbnail is too small.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual($min, $height, $err);

        $err = err('%s Thumbnail is too big.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertLessThanOrEqual($max, $height, $err);

        $err = err('%s Thumbnail is not square.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertSame($height, $width, $err);
    }

    /**
     * @param songB $song
     * @throws Exception
     */
    protected function verifySongMetadata(songB $song)
    {
        $err = prepareIssueCard('Update Song metadata.', $this->path);
        $this->assertSame($song->getExpectedMetadata(), $song->getActualMetadata(), $err);

        if ($song->getAlbumData()) {
            $err = prepareIssueCard('Update Song picture metadata.', $this->path);
            $this->assertSame($song->getExpectedThumbnail(), $song->getActualThumbnail(), $err);
        }
    }

    /** @param string $entry */
    protected function verifyCatalogEntryPresent(string $entry)
    {
        $filepath = bendSeparatorsRight($this->path . DS . $entry);

        $err = err('"%s" is absent or empty. Where is it?', $filepath);
        $err = prepareIssueCard($err, $filepath);
        $this->assertTrue(isFileValid($filepath), $err);
    }

    /**
     * @param array $catalog
     * @param artistInterface $artist
     */
    protected function verifyArtistFilesCatalogued(array $catalog, artistInterface $artist)
    {
        $i = new RecursiveDirectoryIterator($artist->getPath());
        foreach (new RecursiveIteratorIterator($i) as $path => $file) {
            if ($file->getFileName() === '.' || $file->getFileName() === '..') continue;

            $f = bendSeparatorsRight($path);
            $f = $artist->getTitle() . str_replace($artist->getPath(), '', $f);

            $err = err('"%s" is not catalogued. Review this file & update Catalog accordingly.', $f);
            $err = prepareIssueCard($err, $path);
            $this->assertTrue(in_array($f, $catalog), $err);
        }
    }

    protected function verifyQeeContainsArtistsInLimits(array $artistNames, int $limit)
    {
        $err = err('%s contains no Artists. Where are they?', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertNotEmpty($artistNames, $err);

        $err = err('%s contains too many Artists.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual(count($artistNames), $limit, $err);
    }

    /**
     * @param int $limit
     */
    protected function verifyFolderSize(int $limit)
    {
        $actualSize = getFolderSize($this->path);

        $err = err('%s is too large.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual($actualSize, $limit, $err);
    }
}
