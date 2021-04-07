<?php

abstract class artist extends unit implements artistInterface
{
    // technical
    protected $_type = 'dir';

    // lazy
    protected array $albumsListing = [];
    protected array $albums;
    protected ?array $freeSongs;


    /** @throws Exception */
    protected function setAlbumsListing()
    {
        $this->albumsListing = getDirDirsList($this->path);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAlbumsListing(): array
    {
        if (!$this->albumsListing) $this->setAlbumsListing();
        return $this->albumsListing;
    }

    /** @throws Exception */
    protected function setAlbums()
    {
        /** @var albumInterface $class */
        $class = str_replace('artist', 'album', get_class($this));

        foreach (getDirDirsList($this->path) as $albumFolderName) {
            $this->albums[$albumFolderName] = new $class($this->path, $this->title, $albumFolderName);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getAlbums(): array
    {
        if (!$this->albums) $this->setAlbums();
        return $this->albums;
    }

    /**
     * @throws Exception
     */
    protected function setFreeSongs()
    {
        /** @var songInterface $class */
        $class = str_replace('artist', 'song', get_class($this));

        $select = getDirFilesListByExt($this->getPath(), settings::getInstance()->get('extensions/music'));
        foreach ($select as $songFileName) {
            $this->freeSongs[] = new $class($songFileName, $this->path, $this->title, null);
        }
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function getFreeSongs(): ?array
    {
        if (!$this->freeSongs) $this->setFreeSongs();
        return $this->freeSongs;
    }
}
