<?php

use Laminas\Dom\Query as Query;

class resource_album
{
    protected $pageUrl;
    protected $pageDomain;
    protected $pageHtml;
    protected $pageDom;


    /** @var string */
    protected $artist;
    const ARTIST_XP = '//*[@class="main-details"]//*[@itemprop="byArtist"]';

    /** @var string */
    protected $released;
    const RELEASED_XP = '//*[@class="main-details"]//*[@itemprop="datePublished"]/../a';

    /** @var string */
    protected $title;
    const TITLE_XP = '//*[@class="breadcrumbs"]/span[@itemprop="title"]';

    /** @var array */
    protected $songs = [];
    const SONGS_XP = '//div[@itemscope="itemscope"]';
    const SONG_NAME_XP = self::SONGS_XP . '[%s]//p/a';
    const SONG_REF_XP = self::SONGS_XP . '[%s]//div[@class="play"]/span';
    const SONG_REF_ATTR = 'data-url';
    const SONG_SIZE_XP = self::SONGS_XP . '[%s]//div[@class="details"]/div[@class="time"]';


    /**
     * @param string $url
     * @throws Exception
     */
    public function __construct(string $url)
    {
        $this->pageUrl = $url;
        $this->pageDomain = getDomain($url);
        $this->pageHtml = getHtml($url);
        $this->pageDom = new Query($this->pageHtml);

        $this->artist = getTextByXpath($this->pageDom, self::ARTIST_XP);
        $this->released = getTextByXpath($this->pageDom, self::RELEASED_XP);
        $this->title = getTextByXpath($this->pageDom, self::TITLE_XP);
        $this->setSongs($this->pageDom);
    }


    /**
     * @param Query $dom
     * @throws Exception
     */
    private function setSongs(Query $dom)
    {
        $selection = $dom->queryXpath(self::SONGS_XP);
        $c = count($selection);
        for ($s = 1; $s <= $c; $s++) {

            $songName = getTextByXpath($this->pageDom, sprintf(self::SONG_NAME_XP, $s));
            $songName = sprintf("%02d", $s) . '. ' . $songName;
            $songName = smartPrepareFileName($songName) . '.' . settings::getInstance()->get('extensions/music');

            $songUrl = getAttributeByXpath($this->pageDom, sprintf(self::SONG_REF_XP, $s), self::SONG_REF_ATTR);
            $songUrl = $this->pageDomain . str_replace('/Song/Play/', '/Song/Download/', $songUrl);

            $songSize = getTextByXpath($this->pageDom, sprintf(self::SONG_SIZE_XP, $s));
            $songSize = str_replace(',', '.', $songSize);
            $songSize = (string)floatval($songSize);

            $this->songs[$s] = ['filename' => $songName, 'url' => $songUrl, 'size' => $songSize];
        }
    }

    /** @return string */
    public function getArtist(): string
    {
        return $this->artist;
    }

    /** @return string */
    public function getReleased(): string
    {
        return $this->released;
    }

    /** @return string */
    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return array */
    public function getSongs(): array
    {
        return $this->songs;
    }


    /**
     * @return bool
     * @throws Exception
     */
    public function downloadSongs(): bool
    {
        say("\t" . sprintf('"%s %s" by "%s":', $this->getReleased(), $this->getTitle(), $this->getArtist()));

        $path = $this->prepareDirsStructure();
        $songs = $this->getSongs();
        foreach ($songs as $pos => $song) {
            $this->getSongFile($song['url'], $path . $song['filename'], $song['size']);
        }

        if (count($songs) !== count(getDirFilesList($path))) {
            throw new Exception(prepareIssueCard('Not all Songs were downloaded.', $path));
        }
        echo "downloaded!\n";

        return true;
    }

    /**
     * @return string
     * @throws Exception
     */
    private function prepareDirsStructure(): string
    {
        $downloadPath = settings::getInstance()->get('libraries/queue') . DS;
        $artistName = smartPrepareFileName($this->getArtist());
        $albumName = smartPrepareFileName($this->getTitle());

        $path = bendSeparatorsRight($downloadPath . $artistName . DS);
        createDir($path);
        $path = bendSeparatorsRight($downloadPath . $artistName . DS . $this->getReleased() . ' ' . $albumName . DS);
        createDir($path);

        return $path;
    }

    /**
     * @param string $url
     * @param string $filePath
     * @param string $expectedSongSize
     * @throws Exception if File can't be downloaded OR if it isn't downloaded validly
     */
    private function getSongFile(string $url, string $filePath, string $expectedSongSize)
    {
        for ($r = 3; $r >= 0; $r--) {
            if ($this->isDownloaded($filePath, $expectedSongSize)) {
                break;
            }
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            downloadFile($url, $filePath);
//            sleep(mt_rand(1, 4));
        }

        if (!$this->isDownloaded($filePath, $expectedSongSize)) {
            throw new Exception(prepareIssueCard('File wss downloaded invalidly.', $filePath));
        }

        say('.');
    }

    /**
     * @param string $filePath
     * @param string $expectedFileSizeMB
     * @return bool
     */
    private function isDownloaded(string $filePath, string $expectedFileSizeMB): bool
    {
        if (!isFileValid($filePath)) return false;

        $actualFileSizeMB = filesize($filePath) / 1024 / 1024;
        $actualFileSize1 = (string)round($actualFileSizeMB, 2);
        $actualFileSize2 = (string)$actualFileSizeMB;

        if (strpos($actualFileSize1, $expectedFileSizeMB) === 0) return true;
        if (strpos($actualFileSize2, $expectedFileSizeMB) === 0) return true;

        return false;
    }
}
