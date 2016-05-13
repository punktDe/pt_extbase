<?php
namespace PunktDe\PtExtbase\Domain\Model;

/***************************************************************
 *  Copyright (C) 2016 punkt.de GmbH
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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Access control service
 */
class LoggedInUser implements SingletonInterface
{
    /**
     * @var \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected $typoScriptFrontend;


    /**
     * @return LoggedInUser
     */
    public function __construct()
    {
        $this->typoScriptFrontend = $GLOBALS['TSFE'];
    }



    /**
     * Check if Frontend User is logged in
     *
     * @return bool
     */
    public function isFrontendUserLoggedIn()
    {
        return $this->typoScriptFrontend->loginUser;
    }



    /**
     * Check if frontend user is initialized
     *
     * This method is semantically different to self::isFrontendUserLoggedIn()!
     * In TypoScriptFrontendController::initFEuser() and its hooks property $GLOBALS['TSFE']->loginUser might be empty.
     * Nevertheless a valid frontend user is already available.
     * See also: TypoScriptFrontendController::initUserGroups() which sets ->loginUser and ->gr_list based on front-end user status.
     *
     * @return bool
     */
    public function isFrontendUserInitialized()
    {
        return ($this->typoScriptFrontend->fe_user instanceof FrontendUserAuthentication) && is_array($this->typoScriptFrontend->fe_user->user);
    }



    /**
     * @return bool
     */
    public function isFrontendUserAndBackendAdminLoggedIn()
    {
        return ($this->isFrontendUserInitialized() && $this->isBackendAdminLoggedIn());
    }



    /**
     * @return bool
     */
    public function isBackendAdminLoggedIn()
    {
        $backendUser = $this->typoScriptFrontend->initializeBackendUser();
        return $this->typoScriptFrontend->isBackendUserLoggedIn() && $backendUser->isAdmin();
    }



    /**
     * @param string $attribute
     * @return mixed
     * @throws \Exception
     */
    protected function getUserAttribute($attribute)
    {
        if (isset($this->typoScriptFrontend->fe_user->user[$attribute])) {
            return $this->typoScriptFrontend->fe_user->user[$attribute];
        } else {
            throw new \Exception('The property ' . $attribute . ' does not exist in user object.', 1452519829);
        }
    }



    /**
     * @return array
     */
    public function getUserGroupsUids()
    {
        $userGroupUids = [];
        if (isset($this->typoScriptFrontend->gr_list) && is_string($this->typoScriptFrontend->gr_list)) {
            $userGroupUids = GeneralUtility::trimExplode(',', $this->typoScriptFrontend->gr_list);
        }
        return $userGroupUids;
    }
}
