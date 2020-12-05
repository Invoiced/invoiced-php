<?php

namespace Invoiced;

class Collection
{
    /**
     * @var array<string>
     */
    public $links;

    /**
     * @var int
     */
    public $total_count;

    /**
     * @param string $linkHeader
     * @param int    $totalCount
     */
    public function __construct($linkHeader, $totalCount)
    {
        $this->links = $this->parseLinkHeader($linkHeader);
        $this->total_count = $totalCount;
    }

    /**
     * Parses the Link header.
     *
     * @param string $header
     *
     * @return array<string>
     */
    public function parseLinkHeader($header)
    {
        $links = [];

        // Parse each part into a named link
        foreach (explode(',', $header) as $part) {
            $section = explode(';', $part);

            // pull out URL from <...>
            $url = substr(trim($section[0]), 1, -1);

            // pull out rel="..."
            $matches = [];
            preg_match('/rel="(.*)"/', $section[1], $matches);
            $name = $matches[1];

            $links[$name] = $url;
        }

        return $links;
    }
}
