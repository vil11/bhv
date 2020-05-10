<?php

class albumQ extends album
{
    /** @throws Exception if dir is absent by specified path */
    protected function setPath()
    {
        $this->path = $this->artistPath . DS . $this->title;
        parent::setPath();
    }
}
