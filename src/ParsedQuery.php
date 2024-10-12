<?php

namespace Pseudo;

class ParsedQuery
{
    private string $rawQuery;
    private string $hash;

    /**
     * @param string $query
     */
    public function __construct(string $query)
    {
        $this->rawQuery = $query;
        $this->hash = sha1($query);
    }

    public function isEqualTo(ParsedQuery|string $query): bool
    {
        if (!($query instanceof self)) {
            $query = new self($query);
        }
        return $this->hash === $query->getHash();
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getRawQuery(): string
    {
        return $this->rawQuery;
    }
}
