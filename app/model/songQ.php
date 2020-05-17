<?php

class songQ extends song
{
    /** @throws Exception if file is absent by specified path */
    protected function setPath()
    {
        $this->path = $this->artistPath;
        $this->path .= DS . $this->title;
        parent::setPath();
    }
}
