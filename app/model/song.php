<?php

abstract class song extends unit implements songInterface
{
    // technical
    protected $_type = 'file';

    // predefined
    protected string $artistPath;
    protected string $artistTitle;
    protected ?array $albumData;


    /**
     * @param string $songTitle
     * @param string $artistPath
     * @param string $artistTitle
     * @param array|null $albumData
     * @throws Exception
     */
    public function __construct(string $songTitle, string $artistPath, string $artistTitle, ?array $albumData = null)
    {
        $this->artistPath = $artistPath;
        $this->artistTitle = $artistTitle;
        $this->albumData = $albumData;
        parent::__construct($songTitle);
    }


    public function getArtistPath(): string
    {
        return $this->artistPath;
    }

    public function getArtistTitle(): string
    {
        return $this->artistTitle;
    }

    public function getAlbumData(): ?array
    {
        return $this->albumData;
    }
}
