<?php

interface artistInterface extends unitInterface
{
    public function getAlbumsListing(): array;
    public function getAlbums(): array;
    public function getFreeSongs(): ?array;
}
