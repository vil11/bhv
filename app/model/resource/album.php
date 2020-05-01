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
//    const ARTIST_XP = '//*[@class="main-details"]//*[@itemprop="byArtist"]';
    const ARTIST_XP = '//*[@class="main-details"]//*[@itemprop="byArtist"]/..//a';
    /** @var array */
    protected $feat = [];

    /** @var string */
    protected $released;
    const RELEASED_XP = '//*[@class="main-details"]//*[@itemprop="datePublished"]/../a';

    /** @var string */
    protected $type;
    const TYPE_XP = '//*[@class="main-details"]//tr[contains(.,"Тип:")]/td[last()]';

    /** @var string */
    protected $title;
    const TITLE_XP = '//*[@class="breadcrumbs"]/span[@itemprop="title"]';

    /** @var array */
    protected $songs = [];
    const SONGS_XP = '//div[@itemscope="itemscope"]';
    const SONG_ARTISTS_XP = self::SONGS_XP . '[%s]//div[@class="details"]/*[@class="strong"]';
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

        $artists = getTextsByXpath($this->pageDom, self::ARTIST_XP);
        $this->artist = array_shift($artists);
        $this->feat = $artists;
        $this->released = getTextByXpath($this->pageDom, self::RELEASED_XP);
        $this->setType();
        $this->title = getTextByXpath($this->pageDom, self::TITLE_XP);

        $this->setSongs();
    }


    private function setType()
    {
        $rec = settings::getInstance()->get('record_types');

        $type = getTextByXpath($this->pageDom, self::TYPE_XP);
        if ($type === 'Сборник исполнителя') {
            $type = $rec['compilation'];
        }
        elseif ($type === 'Демо') {
            $type = $rec['demo'];
        }
        elseif ($type === 'Студийный альбом') {
            $type = $rec['studio'];
        }
        elseif ($type === 'EP') {
            $type = $rec['ep'];
        }
        elseif ($type === 'Live') {
            $type = $rec['live'];
        }
        elseif ($type === 'Тип не назначен' || $type === 'Сборник разных исполнителей') {
            $type = '';
        } else {
            throw new Exception(prepareIssueCard(err('"%s": unknown Album type.', $type)));
        }

        $this->type = strtoupper($type);
    }

    /** @throws Exception */
    private function setSongs()
    {
        $selection = $this->pageDom->queryXpath(self::SONGS_XP);
        $c = count($selection);
        for ($s = 1; $s <= $c; $s++) {
            $this->songs[$s] = [
                'filename' => $this->prepareSongName($s),
                'url' => $this->prepareSongUrl($s),
                'size' => $this->prepareSongSize($s)
            ];
        }
    }

    private function prepareSongName(int $s): string
    {
        $songName = getTextByXpath($this->pageDom, sprintf(self::SONG_NAME_XP, $s));
        $songArtists = getTextsByXpath($this->pageDom, sprintf(self::SONG_ARTISTS_XP, $s));

        if ($songArtists[0] !== $this->getArtist() && $this->type !== '') {
            throw new Exception();
        }
        if (count($songArtists) !== 1) {
            array_shift($songArtists);
            $songName .= $this->prepareFeat($songArtists);
        }

        $songName = sprintf("%02d", $s) . '. ' . smartPrepareFileName($songName);
        $songName .= '.' . settings::getInstance()->get('extensions/music');

        return $songName;
    }

    private function prepareSongUrl(int $s): string
    {
        $songUrl = getAttributeByXpath($this->pageDom, sprintf(self::SONG_REF_XP, $s), self::SONG_REF_ATTR);
        $songUrl = $this->pageDomain . str_replace('/Song/Play/', '/Song/Download/', $songUrl);

        return $songUrl;
    }

    private function prepareSongSize(int $s): string
    {
        $songSize = getTextByXpath($this->pageDom, sprintf(self::SONG_SIZE_XP, $s));
        $songSize = str_replace(',', '.', $songSize);

        return $songSize;
    }

    private function prepareFeat(array $artists): string
    {
        $delim = settings::getInstance()->get('delimiters');
        return $delim['section'] . $delim['tag_open'] . settings::getInstance()->get('info_tags/feat')
            . $delim['tag_name'] . implode($delim['tag_info'], $artists) . $delim['tag_close'];
    }

    /** @return string */
    public function getArtist(): string
    {
        return $this->artist;
    }

    /** @return array */
    public function getFeat(): array
    {
        return $this->feat;
    }

    /** @return string */
    public function getReleased(): string
    {
        return $this->released;
    }

    /** @return string */
    public function getType(): string
    {
        return $this->type;
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

        $path = $this->prepareLandingDirsStructure();
        $songs = $this->getSongs();
        foreach ($songs as $pos => $song) {
            $this->loadSongFile($song['url'], $path . $song['filename'], $song['size']);
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
    private function prepareLandingDirsStructure(): string
    {
        $path = settings::getInstance()->get('libraries/queue') . DS . smartPrepareFileName($this->getArtist()) . DS;
        $path = bendSeparatorsRight($path);
        createDir($path);

        $feat = (empty($this->getFeat())) ? '' : $this->prepareFeat($this->getFeat());
        $path .= sprintf(
                '%s %s - %s%s',
                $this->getReleased(), $this->getType(), smartPrepareFileName($this->getTitle()), $feat
            ) . DS;
        $path = bendSeparatorsRight($path);
        createDir($path);

        return $path;
    }

    /**
     * @param string $url
     * @param string $filePath
     * @param string $expectedSongSize
     * @throws Exception if File can't be downloaded OR if it isn't downloaded validly
     */
    private function loadSongFile(string $url, string $filePath, string $expectedSongSize)
    {
        if (!$this->isDownloaded($filePath, $expectedSongSize)) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            downloadFile($url, $filePath);
//            sleep(mt_rand(1, 4));

            if (!$this->isDownloaded($filePath, $expectedSongSize)) {
                throw new Exception(prepareIssueCard('File was downloaded invalidly.', $filePath));
            }
        }
        say('.');
    }

    /**
     * @param string $filePath
     * @param string $expectedSize
     * @return bool
     * @throws Exception if unsupportable metrics used (not "Мб" or "Кб")
     */
    private function isDownloaded(string $filePath, string $expectedSize): bool
    {
        if (!isFileValid($filePath)) return false;

        $size = (string)floatval($expectedSize);
        $metr = trim(str_replace($size, '', $expectedSize));

        if ($metr === 'Мб') {
            $actualFileSize = filesize($filePath) / 1024 / 1024;
        } elseif ($metr === 'Кб') {
            $actualFileSize = getFileSize($filePath);
//            return true;
            throw new Exception();
        } else {
            throw new Exception(prepareIssueCard(err('"%s": unsupportable metrics.', $metr)));
        }
        $actualFileSize1 = (string)round($actualFileSize, 2);
        $actualFileSize2 = (string)$actualFileSize;

        if (strpos($actualFileSize1, $size) === 0) return true;
        if (strpos($actualFileSize2, $size) === 0) return true;

        return false;
    }
}
