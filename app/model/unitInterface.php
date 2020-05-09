<?php

interface unitInterface
{
    /** @return string */
    public function getTitle();

    /** @return string */
    public function getPath();

    /** @return array|null */
    public function getData();
}
