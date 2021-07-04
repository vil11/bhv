<?php

class unit implements unitInterface
{
    // technical
    protected $_type;

    // predefined
    protected string $title;
    protected string $path;
    protected ?array $data;


    /**
     * @param string $title
     * @throws Exception
     */
    public function __construct(string $title)
    {
        $this->setTitle($title);
        $this->setPath();
    }


    private function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @throws Exception if unit type is absent by specified path */
    protected function setPath()
    {
        $path = bendSeparatorsRight($this->path);
        if ((($this->_type === 'dir') && !is_dir($path)) || ($this->_type === 'file' && !is_file($path))) {
            throw new Exception(prepareIssueCard($this->_type . ' is absent.', $path));
        }

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getData(): ?array
    {
        return $this->data;
    }


    protected function prepareTagsString(array $data): string
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

    protected function setTags(string $tagsSection)
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
     * @param string $string
     * @param string|null $pattern
     * @throws Exception
     */
    protected function verifyFileName(string $string, ?string $pattern = null)
    {
        if ($pattern) {
            preg_match($pattern, $string, $matches);
            if (empty($matches) || $matches[0] !== $string) {
                $err = sprintf(
                    "Invalid %s filename format. Format it to match the %s pattern.",
                    ucwords(get_class($this)),
                    $pattern
                );
                throw new Exception(prepareIssueCard($err, $this->getPath()));
            }
        }
    }

    public function isMarkedToBeUpdated(string $string): bool
    {
        return isMarkedWithPrefix($string, settings::getInstance()->get('tags/update_metadata'));
    }

    public function adjustName(string $string): string
    {
        $updatePrefixMark = settings::getInstance()->get('tags/update_metadata');
        if ($this->isMarkedToBeUpdated($string)) {
            $string = substr($string, strlen($updatePrefixMark));
        }

        return $string;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function renameUpdated(): bool
    {
        $oldPath = $this->getPath();
        $newPath = str_replace($this->getTitle(), $this->adjustName($this->getTitle()), $this->getPath());

        $this->provideAccess($oldPath);
        $result = rename($oldPath, $newPath);
        if (!$result) {
            $err = prepareIssueCard('Renaming is failed.', $this->getPath());
            throw new Exception($err);
        }

        return $result;
    }

    /**
     * @param string $path
     * @throws Exception
     */
    public function provideAccess(string $path): void
    {
        $result = chmod($path, 0777);
        if (!$result) {
            $err = prepareIssueCard('Permissions providing: failed.', $path);
            throw new Exception($err);
        }
    }
}
