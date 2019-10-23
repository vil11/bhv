<?php

abstract class tests_abstract extends PHPUnit\Framework\TestCase
{
    protected $unit;
    protected $path;


    protected function findOvercontent(array $allowed)
    {
        foreach ($allowed as $fileExt => $limit) {
            if (count(getDirFilesListByExt($this->path, $fileExt)) > $limit) return $fileExt;
        }

        foreach (getDirFilesList($this->path) as $fileName) {
            $fileExt = pathinfo($this->path . DS . $fileName, PATHINFO_EXTENSION);
            if (!array_key_exists("." . $fileExt, $allowed)) return "." . $fileExt;
        }

        return '';
    }

    /**
     * @return array
     */
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
     * @param string $title
     */
    protected function verifyUppercaseAbsent(string $title)
    {
        $err = err('Remove uppercase from "%s" %s name.', $title, $this->unit);
        $err = prepareIssueCard($err, $this->path);

        $this->assertEquals(mb_strtolower($title), $title, $err);
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

    /**
     * @param string $type
     */
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
//                $this->assertEquals(1, count($data[$allowed['cover']]), $err);
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
        $this->assertEquals($format, $title, $err);
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
        $this->assertEquals($format, $title, $err);
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
        $this->assertEquals($format, $title, $err);
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

    /**
     * @param array $names
     */
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
     * @param array $files
     * @throws Exception
     */
    protected function verifyOnlyExpectedFilesPresent(array $files)
    {
        $actual = [];
        foreach (getDirFilesList($this->path) as $file) {
            $actual[] = bendSeparatorsRight($this->path . DS . $file);
        }

        $err = err('Files list is unexpected in %s root folder.', $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertEquals(ksort($files), ksort($actual), $err);
    }

    /**
     * @param artist $artist
     * @throws Exception
     */
    protected function verifyAlbumsNotAbsent(artist $artist)
    {
        $albumsQtyLimit = (int)settings::getInstance()->get('limits/artist_albums_qty_min');
        $albumsQty = count($artist->getAlbums());

        $err = err('"%s" %s contains no Albums. Where are they?', $artist->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual($albumsQtyLimit, $albumsQty, $err);
    }

    /**
     * @param album $album
     * @throws Exception
     */
    protected function verifySongsNotAbsent(album $album)
    {
        $songsQtyLimit = (int)settings::getInstance()->get('limits/album_songs_qty_min');
        $songsQty = count($album->getSongs());

        $err = err('"%s" %s contains no Songs. Where are they?', $album->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertGreaterThanOrEqual($songsQtyLimit, $songsQty, $err);
    }

    /**
     * @param album $album
     * @throws Exception
     */
    protected function verifySongsOrdered(album $album)
    {
        $songs = $album->getSongs();

        $err = err('"%s" %s contains Songs in invalid order. Reorder them.', $album->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertEquals(count($songs), (integer)end($songs)->getData()['position'], $err);
        foreach ($songs as $position => $song) {
            $this->assertEquals($position + 1, (integer)$song->getData()['position'], $err);
        }
    }

    /**
     * @param album $album
     * @throws Exception
     */
    protected function verifyAlbumHasNoFolders(album $album)
    {
        $dirs = getDirDirsList($this->path);

        $err = err('"%s" %s contains folder.', $album->getTitle(), $this->unit);
        $err = prepareIssueCard($err, $this->path);
        $this->assertEmpty($dirs, $err);
    }
}
