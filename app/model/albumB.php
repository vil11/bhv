<?php

class albumB extends album
{
    /**
     * @param string $artistPath
     * @param string $artistTitle
     * @param string $albumTitle
     * @throws Exception
     */
    public function __construct(string $artistPath, string $artistTitle, string $albumTitle)
    {
        parent::__construct($artistPath, $artistTitle, $albumTitle);
        $this->setData();
    }


    /** @throws Exception if dir is absent by specified path */
    protected function setPath()
    {
        $this->path = $this->artistPath . DS . $this->title;
        parent::setPath();
    }
}
