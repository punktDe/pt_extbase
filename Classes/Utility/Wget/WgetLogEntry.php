<?php
 /***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace PunktDe\PtExtbase\Utility\Wget;

class WgetLogEntry
{
    /**
     * Defines which status codes are errors
     *
     * @var array
     */
    protected $errorStatusPattern = '[4,5]..';


    /**
     * @var \DateTime
     */
    protected $fetchDate;


    /**
     * @var string
     */
    protected $url;


    /**
     * @var integer
     */
    protected $status;


    /**
     * @var string
     */
    protected $contentType;


    /**
     * @var integer
     */
    protected $contentLength;

    /**
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @param int $contentLength
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = $contentLength;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return \DateTime
     */
    public function getFetchDate()
    {
        return $this->fetchDate;
    }

    /**
     * @param \DateTime $fetchDate
     */
    public function setFetchDate($fetchDate)
    {
        $this->fetchDate = $fetchDate;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return array
     */
    public function getErrorStatusPattern()
    {
        return $this->errorStatusPattern;
    }

    /**
     * @param array $errorStatusPatterns
     */
    public function setErrorStatusPattern($errorStatusPatterns)
    {
        $this->errorStatusPattern = $errorStatusPatterns;
    }


    /**
     * @return bool
     */
    public function isError()
    {
        return preg_match(sprintf('/%s/', $this->errorStatusPattern), (string) $this->status) != 0 ?: false;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'date' => $this->getFetchDate(),
            'url' => $this->getUrl(),
            'status' => $this->getStatus(),
            'length' => $this->getContentLength()
        ];
    }
}
