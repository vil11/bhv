<?php

class artist extends unit
{
    // technical
    protected $_type = 'dir';

    // predefined
    protected $albumsListing = [];

    // lazy
    protected $albums;
    protected $freeSongs;
    protected $songs;


    /**
     * @param string $title
     * @throws Exception
     */
    public function __construct($title)
    {
        parent::__construct($title);
        $this->setAlbumsListing();
    }


    /**
     * @throws Exception
     */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv') . DS . $this->title;
        parent::setPath();
    }

    /**
     * @throws Exception
     */
    protected function setAlbumsListing()
    {
        $this->albumsListing = getDirDirsList($this->path);
    }

    /**
     * @return string[]
     */
    public function getAlbumsListing()
    {
        return $this->albumsListing;
    }

    /**
     * @throws Exception
     */
    protected function setAlbums()
    {
        foreach (getDirDirsList($this->path) as $albumFolderName) {
            $this->albums[$albumFolderName] = new album($this->title, $albumFolderName);
        }
    }

    /**
     * @throws Exception
     * @return album[]
     */
    public function getAlbums()
    {
        if (!$this->albums) $this->setAlbums();

        return $this->albums;
    }

    /**
     * @throws Exception
     */
    protected function setFreeSongs()
    {
        foreach (getDirFilesListByExt($this->path, settings::getInstance()->get('extensions/music')) as $songFileName) {
            $this->freeSongs[] = new song($this->title, null, $songFileName);
        }
    }

    /**
     * @throws Exception
     * @return song[]
     */
    public function getFreeSongs()
    {
        if (!$this->freeSongs) $this->setFreeSongs();

        return $this->freeSongs;
    }

    /**
     * @throws Exception
     */
    protected function setSongs()
    {
        if (!$this->albums) $this->setAlbums();
        $songs = [];

        foreach ($this->getAlbums() as $album) {
            /** @var album $album */
            $songs = array_merge($songs, $album->getSongs());
        }
        if ($this->getFreeSongs() !== null) $songs = array_merge($songs, $this->getFreeSongs());

        $this->songs = $songs;
    }

    /**
     * @throws Exception
     * @return song[]
     */
    public function getSongs()
    {
        if (!$this->songs) $this->setSongs();

        return $this->songs;
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     * @return bool
     */
    public function updateMetadata($autoRenamingIfSuccess)
    {
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
        echo "updated!";

        if ($autoRenamingIfSuccess) {
            if (!$this->renameUpdated()) {
                return false;
            }
            echo " renamed!";
        }

        return true;
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     * @return bool
     */
    private function updateMetadataForTaggedAlbums($autoRenamingIfSuccess)
    {
        foreach ($this->albumsListing as $albumFolderName) {
            if (!$this->isMarkedToBeUpdated($albumFolderName)) continue;

            $album = new album($this->getTitle(), $albumFolderName);
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
     * @param song[] $songs
     * @throws Exception
     * @return bool
     */
    private function updateMetadataForSongs($songs)
    {
        if (empty($songs)) {
            throw new Exception(prepareIssueCard('Songs are absent. Seems to be a useless call.'));
        }

        foreach ($songs as $song) {
            if (!$song->updateMetadata()) {
                return false;
            }
            echo ".";
        }

        return true;
    }
}
