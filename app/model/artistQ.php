<?php

class artistQ extends artist
{
    // predefined
    protected string $qeeName;


    /**
     * @param string $artistTitle
     * @param string $qeeName
     * @throws Exception
     */
    public function __construct(string $artistTitle, string $qeeName)
    {
        $this->qeeName = $qeeName;
        parent::__construct($artistTitle);
    }


    /** @throws Exception if dir is absent by specified path */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/qee') . $this->qeeName . DS . $this->title;
        parent::setPath();
    }
}
