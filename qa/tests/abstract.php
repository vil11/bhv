<?php

abstract class tests_abstract extends PHPUnit\Framework\TestCase
{
    protected $unit;
    protected $path;


    protected function findOvercontent(string $dirPath, array $dataAllowed)
    {
        foreach ($dataAllowed as $fileExt => $limit) {
            if (count(getDirFilesListByExt($dirPath, $fileExt)) > $limit) return $fileExt;
        }

        foreach (getDirFilesList($dirPath) as $fileName) {
            $fileExt = pathinfo($dirPath . DS . $fileName, PATHINFO_EXTENSION);
            if (!array_key_exists("." . $fileExt, $dataAllowed)) return "." . $fileExt;
        }

        return '';
    }

    protected function isAlbumSongsOrderConsistent(album $album): bool
    {
        $songs = $album->getSongs();

        if (count($songs) !== (integer)end($songs)->getData()['position']) {
            return false;
        }

        foreach ($songs as $position => $song) {
            if ($position + 1 !== (integer)$song->getData()['position']) {
                return false;
            }
        }

        return true;
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
        $err = err('Remove uppercase from %s %s name.', $title, $this->unit);
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
            $err = err('Remove %s from %s name.', $mark, $this->unit);
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
        $err = err('Specify %s in %s name.', $key, $this->unit);
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

            $err = err('%s tag type is invalid in %s name.', $k, $this->unit);
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
}
