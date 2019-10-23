<?php

class bhv extends unit
{
    // technical
    protected $_type = 'dir';

    // predefined
    /** @var array */
    protected $artistsListing = [];
    /** @var string */
    protected $catalogPath;

    // lazy
    /** @var array */
    protected $catalog;


    /**
     * @param string $title
     * @throws Exception
     */
    public function __construct(string $title = 'bhv')
    {
        parent::__construct($title);
        $this->setArtistsListing();
        $this->setCatalogPath();
    }


    /**
     * @throws Exception if dir is absent by specified path
     */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv');
        parent::setPath();
    }

    /**
     * @throws Exception
     */
    private function setArtistsListing()
    {
        $this->artistsListing = getDirDirsList($this->path);
    }

    /**
     * @return array
     */
    public function getArtistsListing(): array
    {
        return $this->artistsListing;
    }

    private function setCatalogPath()
    {
        $path = $this->path . DS . settings::getInstance()->get('paths/bhv_catalog');
        $path = bendSeparatorsRight($path);

        $this->catalogPath = $path;
    }

    /**
     * @return string
     */
    public function getCatalogPath()
    {
        return $this->catalogPath;
    }

    /**
     * @throws Exception
     */
    private function setCatalog()
    {
        if (!isFileValid($this->catalogPath)) {
            $this->updateCatalog();
        }

        $this->catalog = parseList($this->catalogPath);
    }

    /**
     * @throws Exception
     * @return array
     */
    public function getCatalog(): array
    {
        if (!$this->catalog) $this->setCatalog();

        return $this->catalog;
    }

    /**
     * @return bool
     */
    private function copyCatalogUnderProject(): bool
    {
        $name = getPathSectionBackwards($this->catalogPath);
        $name = bendSeparatorsRight(APP_PATH . DS . 'data' . DS . $name);

        return copy($this->catalogPath, $name);
    }

    /**
     * @throws Exception
     * @return bool
     */
    public function updateCatalog(): bool
    {
        $f = fopen($this->catalogPath, 'wt');
        if ($f === false || !is_resource($f)) {
            throw new Exception(prepareIssueCard("File open failed!", $this->catalogPath));
        }

        $i = new RecursiveDirectoryIterator($this->path);
        foreach (new RecursiveIteratorIterator($i) as $path => $file) {
            if ($file->getFileName() === '.' || $file->getFileName() === '..') continue;

            $record = str_replace($this->path . DS, '', $path);
            if (strlen($record) > 333) {
                throw new Exception(prepareIssueCard("Too long file name.", $path));
            }

            if (fwrite($f, $record . "\n") === false) {
                throw new Exception(prepareIssueCard("Writing failed!", $path));
            }
        }

        $f = fclose($f);
        if ($f === false) {
            throw new Exception(prepareIssueCard("File close failed!", $this->catalogPath));
        }

        return $this->copyCatalogUnderProject();
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     * @return bool
     */
    public function updateMetadata(bool $autoRenamingIfSuccess): bool
    {
        foreach ($this->getArtistsListing() as $artistTitle) {
            if (!$this->isMarkedToBeUpdated($artistTitle)) continue;

            $artist = new artist($artistTitle);
            echo "\n    " . substr($artist->getTitle(), 1);
            if (!$artist->updateMetadata($autoRenamingIfSuccess)) {
                return false;
            }
        }

        return $this->updateCatalog();
    }
}
