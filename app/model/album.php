<?php

class album extends unit
{
    // technical
    protected $_type = 'dir';

    // predefined
    /** @var string */
    protected $artistTitle;
    protected $data;

    // lazy
    /** @var song[] */
    protected $songs;


    /**
     * @param string $artistTitle
     * @param string $title
     * @throws Exception
     */
    public function __construct(string $artistTitle, string $title)
    {
        $this->artistTitle = $artistTitle;
        parent::__construct($title);
        $this->setData();
    }


    /**
     * @throws Exception if dir is absent by specified path
     */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv') . DS . $this->artistTitle . DS . $this->title;
        parent::setPath();
    }

    /**
     * @return string
     */
    public function getArtistTitle(): string
    {
        return $this->artistTitle;
    }

    /**
     * @throws Exception
     */
    private function setData()
    {
        $updatePrefixMark = settings::getInstance()->get('tags/update_metadata');
        $this->verifyFileName("|^(" . $updatePrefixMark . ")?\d{4}(\ \[\S+)*\](\ \S+)*|");

        $delimiters = settings::getInstance()->get('delimiters');
        $this->data['released'] = substr($this->adjustName($this->title), 0, 4);
        $this->data['type'] = explode($delimiters['tag_open'], explode($delimiters['tag_close'], $this->title)[0])[1];

        $titleAndTags = explode($delimiters['tag_close'] . $delimiters['section'], $this->title)[1];
        $this->data['title'] = explode($delimiters['section'] . $delimiters['tag_open'], $titleAndTags)[0];

        if ($titleAndTags !== $this->data['title']) {
            $this->setTags(explode($this->data['title'] . $delimiters['section'], $this->title)[1]);
        }

        if (!$this->data) {
            throw new Exception(prepareIssueCard('UNKNOWN CASE', $this->path));
        }
    }

    /**
     * @throws Exception
     */
    private function setSongs()
    {
        $selection = getDirFilesListByExt($this->path, settings::getInstance()->get('extensions/music'));
        foreach ($selection as $songFileName) {
            if (!$this->data) $this->setData();
            $albumData = array_merge(['path' => $this->path], $this->data);

            $this->songs[] = new song($songFileName, $this->artistTitle, $albumData);
        }
    }

    /**
     * @return song[]
     * @throws Exception
     */
    public function getSongs(): array
    {
        if (!$this->songs) $this->setSongs();

        return $this->songs;
    }
}
