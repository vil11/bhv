<?php

interface albumInterface extends unitInterface
{
    /** @return string */
    public function getArtistPath();

    /** @return string */
    public function getArtistTitle();

    /** @return array */
    public function getSongs();
}
