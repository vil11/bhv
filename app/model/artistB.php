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
        $this->path = settings::getInstance()->get('libraries/bhv') . DS . $this->title;
        parent::setPath();
    }


    /**
     * @throws Exception
     */
    private function setSongs()
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
     * @return bool
     * @throws Exception
     */
    public function updateMetadata(bool $autoRenamingIfSuccess): bool
    {
//        $this->provideAccess();

        $ifAnyAlbumsTagged = false;
        foreach ($this->albumsListing as $albumFolderName) {
            if ($this->isMarkedToBeUpdated($albumFolderName)) {
                $ifAnyAlbumsTagged = true;
                break;
            }
        }

        if ($ifAnyAlbumsTagged) {
            if (!$this->updateMetadataForTaggedAlbums($autoRenamingIfSuccess)) {
                return false;
            }
        } else {
            if (!$this->updateMetadataForSongs($this->getSongs())) {
                return false;
            }
        }
        echo 'updated!';

        if ($autoRenamingIfSuccess) {
            if (!$this->renameUpdated()) {
                return false;
            }
            echo ' renamed!';
        }

        return true;
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @return bool
     * @throws Exception
     */
    private function updateMetadataForTaggedAlbums(bool $autoRenamingIfSuccess): bool
    {
        foreach ($this->albumsListing as $albumFolderName) {
            if (!$this->isMarkedToBeUpdated($albumFolderName)) continue;

            $album = new albumB($this->getPath(), $this->getTitle(), $albumFolderName);
            if (!$this->updateMetadataForSongs($album->getSongs())) {
                return false;
            }

            if ($autoRenamingIfSuccess) {
                if (!$album->renameUpdated()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param array $songs
     * @return bool
     * @throws Exception
     */
    private function updateMetadataForSongs(array $songs): bool
    {
        if (empty($songs)) {
            throw new Exception(prepareIssueCard('Songs are absent. Seems to be a useless call.'));
        }

        foreach ($songs as $song) {
            if (!$song->updateMetadata()) {
                return false;
            }
            say('.', 'grey');
        }

        return true;
    }

//    private function provideAccess()
//    {
//        $cmd = 'cd ' . $this->getPath() . ' && cd .. && chmod -R 777 ' . $this->getTitle();
//        system($cmd);
//    }
}
