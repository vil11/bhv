<?php

interface albumInterface extends unitInterface
{
    public function getArtistPath(): string;
    public function getArtistTitle(): string;
    public function getSongs(): array;
}
