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
     * @return string[]
     */
    public function getArtistsListing(): array
    {
        return $this->artistsListing;
    }

    private function setCatalogPath()
    {
        $path = settings::getInstance()->get('paths/bhv_catalog');
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

    private function setCatalog()
    {
        if (!isFileValid($this->catalogPath)) {
            $this->updateCatalog();
        }

        $content = parseList($this->catalogPath);
        array_pop($content);

        $this->catalog = $content;
    }

    public function getCatalog()
    {
        if (!$this->catalog) $this->setCatalog();

        return $this->catalog;
    }

    public function updateCatalog()
    {
        $f = fopen($this->catalogPath, 'wt');

        $i = new RecursiveDirectoryIterator($this->path);
        foreach (new RecursiveIteratorIterator($i) as $path => $file) {
            if ($file->getFileName() === '.' || $file->getFileName() === '..') continue;
            if (substr($file->getFileName(), 0, 1) === '_') continue;

            $record = str_replace($this->path, '', $path);
            $record = mb_convert_encoding($record, 'UTF-8', 'Windows-1251');
            if (strlen($record) > 240) {
                $strlen = strlen($record);


                $a = 1;
            }

            fwrite($f, $record . "\n");
        }

        fclose($f);
    }

    /**
     * @param bool $autoRenamingIfSuccess
     * @throws Exception
     * @return bool
     */
    public function updateMetadata(bool $autoRenamingIfSuccess): bool
    {
        echo "\nMetadata updating:\n";

        foreach ($this->getArtistsListing() as $artistTitle) {
            if (!$this->isMarkedToBeUpdated($artistTitle)) continue;

            $artist = new artist($artistTitle);
            echo "\n    " . substr($artist->getTitle(), 1);
            if (!$artist->updateMetadata($autoRenamingIfSuccess)) {
                return false;
            }
        }

        return true;
    }
}
