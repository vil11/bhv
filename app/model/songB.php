<?php

class songB extends song
{
    // lazy
    protected ?array $actualMetadata = null;
    protected ?string $actualThumbnail = null;
    protected array $expectedMetadata;
    protected string $expectedThumbnail;


    /**
     * @param string $songTitle
     * @param string $artistPath
     * @param string $artistTitle
     * @param array|null $albumData
     * @throws Exception
     */
    public function __construct(string $songTitle, string $artistPath, string $artistTitle, ?array $albumData = null)
    {
        parent::__construct($songTitle, $artistPath, $artistTitle, $albumData);
        $this->setData();
    }

    /** @throws Exception if file is absent by specified path */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv') . $this->artistTitle;
        if ($this->albumData) {
            $this->path .= DS . basename($this->albumData['path']);
        }
        $this->path .= DS . $this->title;
        parent::setPath();
    }

    /** @throws Exception */
    protected function setData()
    {
        $delimiters = settings::getInstance()->get('delimiters');

        $fileName = basename($this->title, '.' . settings::getInstance()->get('extensions/music'));
        if ($this->albumData) {
            $this->verifyFileName($this->title, "|^\d{2}\.(\ \S+)*|");

            $this->data['released'] = $this->albumData['released'];
            $this->data['type'] = $this->albumData['type'];
            $this->data['position'] = explode($delimiters['song_position'], $fileName)[0];


            $artist = explode($delimiters['song_artist'], $fileName);
            if (count($artist) !== 1) {
                throw new Exception(prepareIssueCard('UNKNOWN CASE', $this->getPath()));
            }


            $position = $this->data['position'] . $delimiters['song_position'];
            $positionAndTitle = explode($delimiters['section'] . $delimiters['tag_open'], $fileName)[0];
            $this->data['title'] = explode($position, $positionAndTitle)[1];

            $tags = null;
            if ($fileName !== $positionAndTitle) {
                $before = $this->data['position'] . $delimiters['song_position'] . $this->data['title'] . $delimiters['section'];
                $this->setTags(str_replace($before, '', $fileName));
            }
        } else {
//            $this->verifyFileName("|...|");

            $this->data['title'] = explode($delimiters['section'] . $delimiters['tag_open'], $fileName)[0];

            $tags = null;
            if ($this->data['title'] !== $fileName) {
                $tags = explode($this->data['title'] . $delimiters['section'], $fileName)[1];
                $this->setTags($tags);
            }
        }

        if (!$this->data) {
            throw new Exception(prepareIssueCard('Song data is absent.', $this->getPath()));
        }
    }


    /** @throws Exception */
    private function setActualMetadata()
    {
        $tagObj = new getID3();
        $tagObj->openfile($this->getPath());
        $tagObj->analyze($this->getPath());

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
            }
        }

        ksort($this->actualMetadata);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getActualMetadata(): array
    {
        if (!$this->actualMetadata) $this->setActualMetadata();
        return $this->actualMetadata;
    }

    public function getActualThumbnail()
    {
        if (!$this->actualThumbnail) $this->setActualMetadata();
        return $this->actualThumbnail;
    }

    private function setMetadata(array $tagsData, string $readTag, ?string $writeTag = null)
    {
        if ($writeTag === null) $writeTag = $readTag;

        if (array_key_exists($readTag, $tagsData)) {
//            $this->actualMetadata[$writeTag][] = $this->decode($tagsData[$readTag][0]);
            $this->actualMetadata[$writeTag][] = $tagsData[$readTag][0];
        }
    }

    public function getExpectedMetadata()
    {
        $this->expectedMetadata['publisher'][] = settings::getInstance()->get('tags/publisher');

        $this->expectedMetadata['title'][] = $this->data['title'] . $this->prepareTagsString($this->data);
        $this->expectedMetadata['artist'][] = $this->unmark($this->artistTitle);

        if ($this->albumData) {
            $this->expectedMetadata['year'][] = $this->data['released'];
            $this->expectedMetadata['album'][] = $this->prepareAlbumTitleTag();
            $this->expectedMetadata['track'][] = $this->data['position'];
        }

        ksort($this->expectedMetadata);

        return $this->expectedMetadata;
    }

    private function prepareAlbumTitleTag(): string
    {
        $delimiters = settings::getInstance()->get('delimiters');
        return $delimiters['tag_open'] . $this->albumData['type'] . $delimiters['tag_close'] . $delimiters['section']
            . $this->albumData['title'] . $this->prepareTagsString($this->albumData);
    }

    /** @throws Exception */
    public function getExpectedThumbnail()
    {
        $thumbnailPath = $this->albumData['path'] . DS . settings::getInstance()->get('paths/album_thumbnail');
        if (!isFileValid($thumbnailPath)) {
            $err = prepareIssueCard('Thumbnail is absent or invalid.', $this->albumData['path']);
            throw new Exception($err);
        }

        $fd = fopen($thumbnailPath, 'rb');
        $this->expectedThumbnail = fread($fd, filesize($thumbnailPath));
        fclose($fd);

        return $this->expectedThumbnail;
    }

    /**
     * @param string[]|null $formats
     * @param string|null $encoding
     * @throws Exception
     */
    public function updateMetadata(?string $encoding = null, ?array $formats = null)
    {
        $encoding = (!is_null($encoding)) ? $encoding : 'UTF-8';
        $formatsDefault = ['id3v1', 'id3v2.3'];
//        $formatsDefault = ['id3v2.4'];
        $formats = isset($formats) ? $formats : $formatsDefault;

        $tagWriter = $this->declareWriter($encoding, $formats);
//        $tagWriter = $this->forceReset($tagWriter);
        $tagData = $this->prepareData();

//        $this->analyze($this->getPath(), $encoding);
        $this->writeTags($this->path, $tagData, $tagWriter);
//        $this->analyze($this->getPath(), $encoding);
    }

    private function declareWriter(string $encoding, array $formats): getid3_writetags
    {
        $tagWriter = new getid3_writetags;
        $tagWriter->filename = $this->getPath();
        $tagWriter->tagformats = $formats;
        $tagWriter->overwrite_tags = true;
        $tagWriter->tag_encoding = $encoding;
        $tagWriter->remove_other_tags = true;

        return $tagWriter;
    }

    private function prepareData(): array
    {
        $tagData = $this->getExpectedMetadata();
        if ($this->albumData) {
            $tagData['attached_picture'][] = [
                'picturetypeid' => 2,
                'description' => 'cover',
                'mime' => 'image/jpeg',
                'data' => $this->getExpectedThumbnail()
            ];
        }

        return $tagData;
    }

    private function forceReset(getid3_writetags $tagWriter): getid3_writetags
    {
//        $tagWriter->DeleteTags(['id3v1', 'id3v2']);
        $tagWriter->DeleteTags(['id3v1']);
        $tagWriter->DeleteTags(['id3v2']);

        return $tagWriter;
    }

    /**
     * @param string $path
     * @param array $tags
     * @param getid3_writetags $writer
     * @return getid3_writetags
     * @throws Exception
     */
    private function writeTags(string $path, array $tags, getid3_writetags $writer): getid3_writetags
    {
        $writer->tag_data = $tags;
        $writer->WriteTags();

        if ($errors = $writer->errors) {
            $err = 'Writing metadata failed.' . " Details: " . implode("; ", $errors);
            $err = prepareIssueCard($err, $path);
            throw new Exception($err);
        }

        return $writer;
    }

    private function analyze(string $path, string $encoding): array
    {
        $id3 = new getID3();
        $id3->encoding = $encoding;
        $info = $id3->analyze($path);

        return $info;
    }
}
