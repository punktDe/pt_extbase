<?php
namespace PunktDe\PtExtbase\Utility\Git\Result;

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

/**
 * Path Status
 *
 * Syntax: XY PATH1 -> PATH2
 *
 * X          Y     Meaning
 * -------------------------------------------------
 *           [MD]   not updated
 * M        [ MD]   updated in index
 * A        [ MD]   added to index
 * D         [ M]   deleted from index
 * R        [ MD]   renamed in index
 * C        [ MD]   copied in index
 * [MARC]           index and work tree matches
 * [ MARC]     M    work tree changed since index
 * [ MARC]     D    deleted in work tree
 * -------------------------------------------------
 * D           D    unmerged, both deleted
 * A           U    unmerged, added by us
 * U           D    unmerged, deleted by them
 * U           A    unmerged, added by them
 * D           U    unmerged, deleted by us
 * A           A    unmerged, both added
 * U           U    unmerged, both modified
 * -------------------------------------------------
 * ?           ?    untracked
 * !           !    ignored
 * -------------------------------------------------
 *
 * @package PunktDe\PtExtbase\Utility\Git\Result
 */
class PathStatus implements ComponentInterface {

	/**
	 * @var string
	 */
	protected $indexStatus = '';


	/**
	 * @var string
	 */
	protected $workTreeStatus = '';


	/**
	 * @var string
	 */
	protected $path = '';


	/**
	 * @var string
	 */
	protected $correspondingPath = '';


	/**
	 * @return string
	 */
	public function getCorrespondingPath() {
		return $this->correspondingPath;
	}



	/**
	 * @param string $correspondingPath
	 */
	public function setCorrespondingPath($correspondingPath) {
		$this->correspondingPath = $correspondingPath;
	}



	/**
	 * @return string
	 */
	public function getIndexStatus() {
		return $this->indexStatus;
	}



	/**
	 * @param string $indexStatus
	 */
	public function setIndexStatus($indexStatus) {
		$this->indexStatus = $indexStatus;
	}



	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}



	/**
	 * @param string $path
	 */
	public function setPath($path) {
		$this->path = $path;
	}



	/**
	 * @return string
	 */
	public function getWorkTreeStatus() {
		return $this->workTreeStatus;
	}



	/**
	 * @param string $workTreeStatus
	 */
	public function setWorkTreeStatus($workTreeStatus) {
		$this->workTreeStatus = $workTreeStatus;
	}



	/**
	 * @return array
	 */
	public function toArray() {
		return array_values(get_object_vars($this));
	}

}
