<?php

class queue
{
    // predefined
    /** @var array */
    protected $paths = [];
    /** @var array */
    protected $artistsListing = [];


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setPaths();
        $this->setArtistsListing();
    }


    /**
     * @throws Exception
     */
    private function setPaths()
    {
        $libraries = settings::getInstance()->get('libraries');
        $location = $libraries['queue'];
        unset ($libraries['queue']);
        $queue = getDirDirsList($location);

        foreach ($queue as $name) {
            $path = bendSeparatorsRight($location . DS . $name);

            if (in_array($path, $libraries)) {
                continue;
            }

            if (!is_dir($path)) {
                throw new Exception(prepareIssueCard('Dir is absent.', $path));
            }

            $this->paths[$name] = $path;
        }
    }

    /**
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * @throws Exception
     */
    private function setArtistsListing()
    {
        foreach ($this->paths as $name => $path) {
            $this->artistsListing[$name] = getDirDirsList($path);
        }
    }

    /**
     * @return array
     */
    public function getArtistsListing(): array
    {
        return $this->artistsListing;
    }
}
