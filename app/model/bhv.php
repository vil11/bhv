<?php

class bhv extends unit
{
    // technical
    protected $_type = 'dir';

    // predefined
    protected array $artistsListing = [];
    protected string $catalogPath;

    // lazy
    protected array $newArtistsListing = [];
    protected array $catalog;


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


    /** @throws Exception if dir is absent by specified path */
    protected function setPath()
    {
        $this->path = settings::getInstance()->get('libraries/bhv');
        parent::setPath();
    }

    /** @throws Exception */
    protected function setArtistsListing()
    {
        $this->artistsListing = getDirDirsList($this->path);
    }

    public function getArtistsListing(): array
    {
        return $this->artistsListing;
    }

    protected function setCatalogPath()
    {
        $path = $this->path . DS . settings::getInstance()->get('paths/bhv_catalog');
        $path = bendSeparatorsRight($path);

        $this->catalogPath = $path;
    }

    public function getCatalogPath(): string
    {
        return $this->catalogPath;
    }

    /** @throws Exception */
    private function setCatalog()
    {
        if (!isFileValid($this->catalogPath)) {
            $this->resetCatalog();
        }

        $this->catalog = parseList($this->catalogPath);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getCatalog(): array
    {
        if (!isset($this->catalog)) $this->setCatalog();
        return $this->catalog;
    }

    private function setNewArtistsListing()
    {
        foreach ($this->getArtistsListing() as $artistTitle) {
            if (!$this->isMarkedToBeUpdated($artistTitle)) continue;

            $this->newArtistsListing[] = $artistTitle;
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getNewArtistsListing(): array
    {
        if (!$this->newArtistsListing) $this->setNewArtistsListing();
        return $this->newArtistsListing;
    }

    private function copyCatalogUnderProject(): bool
    {
        $mt = microtime(true);
        say("\n\tcopying under Git takes ");

        $name = bendSeparatorsRight(PATH_APP . 'data' . DS . getPathSectionBackwards($this->catalogPath));
        $result = copy($this->catalogPath, $name);

        say(round((microtime(true) - $mt), 4) . ' seconds.');
        return $result;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function resetCatalog(): bool
    {
        $this->catalogueFolderTree($this->getPath());
        return $this->copyCatalogUnderProject();
    }

    /**
     * @param string $rootPath
     * @throws Exception
     */
    private function catalogueFolderTree(string $rootPath)
    {
        $modeReset = 'wt';
        $modeLog = 'at';

        $f = fopen($this->getCatalogPath(), $modeReset);
        if ($f === false || !is_resource($f)) {
            throw new Exception(prepareIssueCard('File open failed!', $this->getCatalogPath()));
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath)) as $path => $file) {
            if ($file->getFileName() === '.') {
                continue;
            }
            if ($file->getFileName() === '..') {
                fwrite($f, "\n");
                continue;
            }

            $record = bendSeparatorsRight($path);
            $record = str_replace($rootPath, '', $record);

            if (strlen($record) > settings::getInstance()->get('limits/path_length_max')) {
                throw new Exception(prepareIssueCard('Too long record.', $path));
            }
            if (fwrite($f, $record . "\n") === false) {
                throw new Exception(prepareIssueCard('Writing failed!', $path));
            }
        }

        $f = fclose($f);
        if ($f === false) {
            throw new Exception(prepareIssueCard('File close failed!', $this->getCatalogPath()));
        }
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     */
    public function updateMetadata(bool $autoRenamingIfSuccess)
    {
        foreach ($this->getNewArtistsListing() as $artistTitle) {
            $artist = new artistB($artistTitle);
            echo "\n\t" . substr($artist->getTitle(), 1);
            $artist->updateMetadata($autoRenamingIfSuccess);
        }
    }
}
