<?php

class qee
{
    // predefined
    protected array $paths = [];
    protected array $artistsListing = [];

    // lazy
    protected array $demoArtistsListing = [];
    protected array $progressArtistsListing = [];
    protected array $waitingArtistsListing = [];


    /** @throws Exception */
    public function __construct()
    {
        $this->setPaths();
        $this->setArtistsListing();
    }


    /** @throws Exception */
    private function setPaths()
    {
        $libraries = settings::getInstance()->get('libraries');
        $location = $libraries['qee'];
        unset ($libraries['qee']);
        $qee = getDirDirsList($location);

        foreach ($qee as $name) {
            $path = bendSeparatorsRight($location . $name);

            if (in_array($path, $libraries)) {
                continue;
            }

            if (!is_dir($path)) {
                throw new Exception(prepareIssueCard('Dir is absent.', $path));
            }

            $this->paths[$name] = $path;
        }
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    /** @throws Exception */
    private function setArtistsListing()
    {
        foreach ($this->getPaths() as $name => $path) {
            $this->artistsListing[$name] = getDirDirsList($path);
        }
    }

    public function getArtistsListing(): array
    {
        return $this->artistsListing;
    }

    private function setDemoArtistsListing()
    {
        $this->demoArtistsListing = $this->prepareArtistsListing(settings::getInstance()->get('tags/qee_demo_prefix'));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getDemoArtistsListing(): array
    {
        if (!$this->demoArtistsListing) $this->setDemoArtistsListing();
        return $this->demoArtistsListing;
    }

    private function setProgressArtistsListing()
    {
        $this->progressArtistsListing = $this->prepareArtistsListing(settings::getInstance()->get('tags/qee_progress_prefix'));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getProgressArtistsListing(): array
    {
        if (!$this->progressArtistsListing) $this->setProgressArtistsListing();
        return $this->progressArtistsListing;
    }

    private function setWaitingArtistsListing()
    {
        $this->waitingArtistsListing = $this->prepareArtistsListing(settings::getInstance()->get('tags/qee_waiting_prefix'));
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getWaitingArtistsListing(): array
    {
        if (!$this->waitingArtistsListing) $this->setWaitingArtistsListing();
        return $this->waitingArtistsListing;
    }

    private function prepareArtistsListing(string $prefix): array
    {
        $result = [];

        $artistsListing = $this->getArtistsListing();
        foreach ($artistsListing as $qeeName => $artistTitle) {
            if (!isMarkedWithPrefix($qeeName, $prefix)) continue;
            $result[$qeeName] = $artistsListing[$qeeName];
        }

        return $result;
    }
}
