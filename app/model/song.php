<?php

abstract class song extends unit implements songInterface
{
    // technical
    protected $_type = 'file';

    // predefined
    /** @var string */
    protected $artistPath;
    /** @var string */
    protected $artistTitle;
    /** @var ?array */
    protected $albumData;


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


    /** @return string */
    public function getArtistPath(): string
    {
        return $this->artistPath;
    }

    /** @return string */
    public function getArtistTitle(): string
    {
        return $this->artistTitle;
    }

    /** @return array|null */
    public function getAlbumData(): ?array
    {
        return $this->albumData;
    }
}
