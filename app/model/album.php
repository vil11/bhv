<?php

abstract class album extends unit implements albumInterface
{
    // technical
    protected $_type = 'dir';

    // predefined
    protected string $artistPath;
    protected string $artistTitle;

    // lazy
    protected array $songs;


    /**
     * @param string $artistPath
     * @param string $artistTitle
     * @param string $albumTitle
     * @throws Exception
     */
    public function __construct(string $artistPath, string $artistTitle, string $albumTitle)
    {
        $this->artistPath = $artistPath;
        $this->artistTitle = $artistTitle;
        parent::__construct($albumTitle);
    }


    public function getArtistPath(): string
    {
        return $this->artistPath;
    }

    public function getArtistTitle(): string
    {
        return $this->artistTitle;
    }

    /** @throws Exception */
    protected function setSongs()
    {
        /** @var songInterface $class */
        $class = str_replace('album', 'song', get_class($this));

        $ext = settings::getInstance()->get('extensions/music');
        $selection = getDirFilesListByExt($this->getPath(), $ext);
        if (empty($selection)) {
            $err = err('Album contains no Songs (%s). Where are they?', $ext);
            throw new Exception(prepareIssueCard($err, $this->getPath()));
        }
        foreach ($selection as $songFileName) {
            if (!$this->data) $this->setData();
            $albumData = array_merge(['path' => $this->getPath()], $this->data);

            $this->songs[] = new $class($songFileName, $this->artistPath, $this->artistTitle, $albumData);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSongs(): array
    {
        if (!isset($this->songs)) $this->setSongs();
        return $this->songs;
    }


    /** @throws Exception */
    protected function setData()
    {
        $updatePrefixMark = settings::getInstance()->get('tags/update_metadata');
        $this->verifyFileName($this->title, "|^(" . $updatePrefixMark . ")?\d{4}(\ \[\S+)*\](\ \S+)*|");

        $delimiters = settings::getInstance()->get('delimiters');
        $this->data['released'] = substr($this->unmark($this->title), 0, 4);
        $this->data['type'] = explode($delimiters['tag_open'], explode($delimiters['tag_close'], $this->title)[0])[1];

        $titleAndTags = explode($delimiters['tag_close'] . $delimiters['section'], $this->title)[1];
        $this->data['title'] = explode($delimiters['section'] . $delimiters['tag_open'], $titleAndTags)[0];

        if ($titleAndTags !== $this->data['title']) {
            $this->setTags(explode($this->data['title'] . $delimiters['section'], $this->title)[1]);
        }

        if (!$this->data) {
            throw new Exception(prepareIssueCard('UNKNOWN CASE', $this->getPath()));
        }
    }
}
