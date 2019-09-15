<?php

class unit
{
    // technical
    protected $_type;

    // predefined
    /** @var string */
    protected $title;
    /** @var string */
    protected $path;
    protected $data;


    /**
     * @param string $title
     * @throws Exception
     */
    public function __construct(string $title)
    {
        $this->setTitle($title);
        $this->setPath();
    }


    /**
     * @param string $title
     */
    private function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @throws Exception if unit (dir||file) is absent by specified path
     */
    protected function setPath()
    {
        $path = bendSeparatorsRight($this->path);
        if ((($this->_type === 'dir') && !is_dir($path)) || ($this->_type === 'file' && !is_file($path))) {
            throw new Exception(prepareIssueCard($this->_type . ' is absent.', $path));
        }

        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function prepareTagsString(array $data)
    {
        if (!array_key_exists('tags', $data)) return '';

        $delimiters = settings::getInstance()->get('delimiters');

        $result = $delimiters['section'];
        foreach ($data['tags'] as $k => $v) {
            $v = implode($delimiters['tag_info'], $v);

            if ($k === 'info') {
                $result .= $delimiters['tag_open'] . "$v" . $delimiters['tag_close'];
            } else {
                $result .= $delimiters['tag_open'] . "$k" . $delimiters['tag_name'] . "$v" . $delimiters['tag_close'];
            }
        }

        return $result;
    }

    /**
     * @param string $tagsSection
     */
    protected function setTags($tagsSection)
    {
        $delimiters = settings::getInstance()->get('delimiters');

        $tags = substr($tagsSection, 1, -1);
        $tags = explode($delimiters['tag_close'] . $delimiters['tag_open'], $tags);
        foreach ($tags as $tag) {
            if (strpos($tag, $delimiters['tag_name'])) {
                $tag = explode($delimiters['tag_name'], $tag);
                $this->data['tags'][$tag[0]] = explode($delimiters['tag_info'], $tag[1]);
            } else {
                $this->data['tags']['info'] = explode($delimiters['tag_info'], $tag);
            }
        }
    }

    /**
     * @param string $tag
     * @return string
     */
    protected static function encode($tag)
    {
        return mb_convert_encoding($tag, 'UTF-8', 'Windows-1251');
    }

    /**
     * @param string $tag
     * @return string
     */
    protected static function decode($tag)
    {
        return mb_convert_encoding($tag, 'Windows-1251', 'UTF-8');
    }

    /**
     * @param ?string $pattern
     * @throws Exception
     */
    protected function verifyFileName($pattern = null)
    {
        if ($pattern) {
            preg_match($pattern, $this->title, $matches);
            if (empty($matches) || $matches[0] !== $this->title) {
                $err = sprintf(
                    "Invalid %s filename format. Format it to match the %s pattern.",
                    ucwords(get_class($this)),
                    $pattern
                );
                throw new Exception(prepareIssueCard($err, $this->path));
            }
        }
    }

    /**
     * @param string $string
     * @return bool
     */
    protected function isMarkedToBeUpdated(string $string): bool
    {
        $updatePrefixMark = settings::getInstance()->get('tags/update_metadata');
        return (substr($string, 0, strlen($updatePrefixMark)) === $updatePrefixMark);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function adjustName($string)
    {
        $updatePrefixMark = settings::getInstance()->get('tags/update_metadata');
        if ($this->isMarkedToBeUpdated($string)) {
            $string = substr($string, strlen($updatePrefixMark));
        }

        return $string;
    }

    /**
     * @return bool
     */
    protected function renameUpdated(): bool
    {
        return rename(
            $this->getPath(),
            str_replace($this->getTitle(), $this->adjustName($this->getTitle()), $this->getPath())
        );
    }
}
