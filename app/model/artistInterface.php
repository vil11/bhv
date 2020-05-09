<?php

interface artistInterface extends unitInterface
{
    /** @return array */
    public function getAlbumsListing();

    /** @return array */
    public function getAlbums();

    /** @return array|null */
    public function getFreeSongs();
}
