<?php
namespace PunktDe\PtExtbase\ViewHelpers;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PunktDe\PtExtbase\Utility\Files;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

/**
 * Class fileTypeIcon
 *
 * @package PunktDe/PtExtbase/ViewHelpers
 */
class FileTypeIconViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'img';

    /**
     * @var Files
     */
    protected $fileUtility;

    /**
     * @param Files $fileUtility
     */
    public function injectFileUtility(Files $fileUtility): void
    {
        $this->fileUtility = $fileUtility;
    }


    /**
     * @param string $fileExtension
     * @param string $iconBaseDirectory
     * @param string $iconExtension
     * @return null||string
     */
    public function render($fileExtension, $iconBaseDirectory = '', $iconExtension = 'gif')
    {
        if ($iconBaseDirectory === '') {
            $iconBaseDirectory = $this->fileUtility->concatenatePaths([TYPO3_mainDir, 'gfx/fileicons/']);
        }

        $iconPath = $this->fileUtility->concatenatePaths([$iconBaseDirectory, $fileExtension . '.' . $iconExtension]);

        if ($this->validateFileIsImage($iconPath)) {
            $this->tag->addAttribute('src', $iconPath);
            return $this->tag->render();
        } else {
            return null;
        }
    }

    /**
     * @param string $pathToFile
     * @return boolean
     */
    protected function validateFileIsImage($pathToFile)
    {
        if (!is_file($pathToFile)) {
            return false;
        }
        $finfo = new \finfo();
        $mimeType = $finfo->file($pathToFile, FILEINFO_MIME_TYPE);
        if (strncmp($mimeType, 'image/', 6) === 0) {
            return true;
        }
        return false;
    }
}
