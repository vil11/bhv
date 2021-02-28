<?php

class artistB extends artist
{
    // lazy
    /** @var array */
    protected $songs;


    /**
     * @param string $artistTitle
     * @throws Exception
     */
    public function __construct(string $artistTitle)
    {
        parent::__construct($artistTitle);
    }


    /** @throws Exception if dir is absent by specified path */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv') . $this->title;
        parent::setPath();
    }

    /**
     * @throws Exception
     */
    protected function setSongs()
    {
        if (!$this->albums) $this->setAlbums();
        $songs = [];

        foreach ($this->getAlbums() as $album) {
            /** @var albumInterface $album */
            $songs = array_merge($songs, $album->getSongs());
        }
        if ($this->getFreeSongs() !== null) $songs = array_merge($songs, $this->getFreeSongs());

        $this->songs = $songs;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getSongs(): array
    {
        if (!$this->songs) $this->setSongs();
        return $this->songs;
    }


    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     */
    public function updateMetadata(bool $autoRenamingIfSuccess)
    {
//        $this->provideAccess();

        if ($this->ifAnyAlbumsTagged()) {
            $this->updateMetadataForTaggedAlbums($autoRenamingIfSuccess);
        } else {
            $this->updateMetadataForSongs($this->getSongs());
        }
        echo 'updated!';

        if ($autoRenamingIfSuccess) {
            $this->renameUpdated();
            echo ' renamed!';
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function ifAnyAlbumsTagged(): bool
    {
        $ifAnyAlbumsTagged = false;
        foreach ($this->getAlbumsListing() as $albumFolderName) {
            if ($this->isMarkedToBeUpdated($albumFolderName)) {
                $ifAnyAlbumsTagged = true;
                break;
            }
        }

        return $ifAnyAlbumsTagged;
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     */
    private function updateMetadataForTaggedAlbums(bool $autoRenamingIfSuccess)
    {
        foreach ($this->albumsListing as $albumFolderName) {
            if (!$this->isMarkedToBeUpdated($albumFolderName)) continue;

            $album = new albumB($this->getPath(), $this->getTitle(), $albumFolderName);
            $this->updateMetadataForSongs($album->getSongs());

            if ($autoRenamingIfSuccess) {
                $album->renameUpdated();
            }
        }
    }

    /**
     * @param array $songs
     * @throws Exception
     */
    private function updateMetadataForSongs(array $songs)
    {
        if (empty($songs)) {
            throw new Exception(prepareIssueCard('Songs are absent. Seems to be a useless call.'));
        }

        foreach ($songs as $song) {
            $song->updateMetadata();
            say('.', 'grey');
        }
    }
}
