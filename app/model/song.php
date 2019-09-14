<?php

class song extends unit
{
    // technical
    protected $_type = 'file';

    // predefined
    protected $artistTitle;
    protected $albumData;
    protected $data;

    // lazy
    protected $actualMetadata;
    protected $actualThumbnail;
    protected $expectedMetadata;
    protected $expectedThumbnail;


    /**
     * @param string $artistTitle
     * @param array $albumData
     * @param string $title
     * @throws Exception
     */
    public function __construct($artistTitle, $albumData, $title)
    {
        $this->artistTitle = $artistTitle;
        $this->albumData = $albumData;

        parent::__construct($title);

        $this->setData();
    }

    /**
     * @return string
     */
    public function getArtistTitle()
    {
        return $this->artistTitle;
    }

    public function getAlbumData()
    {
        return $this->albumData;
    }

    /**
     * @throws Exception
     */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv') . DS . $this->artistTitle;
        if ($this->albumData) {
            $this->path .= DS . basename($this->albumData['path']);
        }
        $this->path .= DS . $this->title;
        parent::setPath();
    }

    /**
     * @throws Exception
     */
    protected function setData()
    {
        $delimiters = settings::getInstance()->get('delimiters');

        $fileName = basename($this->title, settings::getInstance()->get('extensions/music'));
        if ($this->albumData) {
            $this->verifyFileName("|^\d{2}\.(\ \S+)*|");

            $this->data['released'] = $this->albumData['released'];
            $this->data['type'] = $this->albumData['type'];
            $this->data['position'] = explode($delimiters['song_position'], $fileName)[0];

            $position = $this->data['position'] . $delimiters['song_position'];
            $positionAndTitle = explode($delimiters['section'] . $delimiters['tag_open'], $fileName)[0];
            $this->data['title'] = explode($position, $positionAndTitle)[1];

            $tags = null;
            if ($fileName !== $positionAndTitle) {
                $tags = explode($this->data['title'] . $delimiters['section'], $fileName)[1];
                $this->setTags($tags);
            }
        } else {

            $this->data['title'] = explode($delimiters['section'] . $delimiters['tag_open'], $fileName)[0];

            $tags = null;
            if ($fileName !== $this->data['title']) {
                $tags = explode($this->data['title'] . $delimiters['section'], $fileName)[1];
                $this->setTags($tags);
            }
        }
    }

    /**
     * @throws Exception
     */
    protected function setActualMetadataAndThumbnail()
    {
        $tagObj = new getID3();
        $tagObj->openfile($this->path);
        $tagObj->analyze($this->path);

        $tags = $tagObj->info['tags']['id3v2'];
        $this->setMetadata($tags, 'publisher');
        $this->setMetadata($tags, 'title');
        $this->setMetadata($tags, 'artist');
        if ($this->albumData) {
            $this->setMetadata($tags, 'year');
            $this->setMetadata($tags, 'album');
            $this->setMetadata($tags, 'track_number', 'track');

            $apic = $tagObj->info['id3v2'];
            if (array_key_exists('APIC', $apic)) {
                $this->actualThumbnail = $apic['APIC'][0]['data'];
            } else {
                $aaa = 1;
            }
        }
    }

    public function getActualMetadata()
    {
        if (!$this->actualMetadata) $this->setActualMetadataAndThumbnail();
        return $this->actualMetadata;
    }

    public function getActualThumbnail()
    {
        if (!$this->actualThumbnail) $this->setActualMetadataAndThumbnail();
        return $this->actualThumbnail;
    }

    private function setMetadata(array $tagsData, $readTag, $writeTag = null)
    {
        if ($writeTag === null) $writeTag = $readTag;

        if (array_key_exists($readTag, $tagsData)) {
            $this->actualMetadata[$writeTag][] = $this->decode($tagsData[$readTag][0]);
        }
    }

    protected function setExpectedMetadata()
    {
        $this->expectedMetadata['publisher'][] = settings::getInstance()->get('tags/publisher');

        $this->expectedMetadata['title'][] = $this->data['title'] . $this->prepareTagsString($this->data);
        $this->expectedMetadata['artist'][] = $this->adjustName($this->artistTitle);

        if ($this->albumData) {
            $this->expectedMetadata['year'][] = $this->data['released'];
            $this->expectedMetadata['album'][] = $this->prepareAlbumTitleTag();
            $this->expectedMetadata['track'][] = $this->data['position'];
        }
    }

    /**
     * @return string
     */
    private function prepareAlbumTitleTag()
    {
        $delimiters = settings::getInstance()->get('delimiters');
        return $delimiters['tag_open'] . $this->albumData['type'] . $delimiters['tag_close'] . $delimiters['section']
            . $this->albumData['title'] . $this->prepareTagsString($this->albumData);
    }

    public function getExpectedMetadata()
    {
        if (!$this->expectedMetadata) $this->setExpectedMetadata();
        return $this->expectedMetadata;
    }

    public function setExpectedThumbnail()
    {
        $thumbnailPath = $this->albumData['path'] . DS . 'cover' . settings::getInstance()->get('extensions/thumbnail');
        $fd = fopen($thumbnailPath, 'rb');
        $this->expectedThumbnail = fread($fd, filesize($thumbnailPath));
        fclose($fd);
    }

    public function getExpectedThumbnail()
    {
        if (!$this->expectedThumbnail) $this->setExpectedThumbnail();
        return $this->expectedThumbnail;
    }

    /**
     * @throws Exception
     * @return bool
     */
    public function updateMetadata()
    {
        // object declaration
        $tagObj = new getid3_writetags;
        $tagObj->filename = $this->path;
        $tagObj->tagformats = array('id3v1', 'id3v2.3');
        $tagObj->overwrite_tags = true;
        $encodingType = 'UTF-8';
        $tagObj->tag_encoding = $encodingType;
        $tagObj->remove_other_tags = true;

        // tags preparing
        $tagData = $this->prepareUpdatedMetadataForWriting();
        if ($this->albumData) {
            $tagData['attached_picture'][] = array(
                'picturetypeid' => 2,
                'description' => 'cover',
                'mime' => 'image/jpeg',
                'data' => $this->getExpectedThumbnail()
            );
        }

        // writing
        $tagObj->tag_data = $tagData;
        $result = $tagObj->WriteTags();

        // finalizing
        $id3 = new getID3();
        $id3->analyze($this->path);
        $id3->encoding = $encodingType;

        return $result;
    }

    /**
     * @throws Exception
     * @return array
     */
    private function prepareUpdatedMetadataForWriting()
    {
        $metadata = $this->getExpectedMetadata();
        foreach ($metadata as $k => &$m) {
            if (count($m) !== 1)  {
                throw new Exception('');
            }

            if (is_string($m[0])) {
                $m[0] = $this->encode($m[0]);
            }
        }
        return $metadata;
    }
}
