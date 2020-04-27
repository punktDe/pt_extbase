<?php

namespace PunktDe\PtExtbase\Lifecycle;

/*
 *  (c) 2018 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class HookManager
{
    /**
     * Sends END signal to lifecycle manager, when TYPO3 is going to shut down
     *
     * @param array $params
     * @param unknown_type $reference
     */
    public function updateEnd(&$params, &$reference)
    {

        //If the class can not be resolved, we are not in an lifecycle-managed context. therefore exit here.
        if (!class_exists(\Tx_PtExtbase_Lifecycle_Manager::class)) {
            return;
        }

        // This is a singleton, so we can use \TYPO3\CMS\Core\Utility\GeneralUtility to get a singl\TYPO3\CMS\Core\Utility\GeneralUtilityance
        $lifecycle = GeneralUtility::makeInstance(\Tx_PtExtbase_Lifecycle_Manager::class);
        $lifecycle->updateState(\Tx_PtExtbase_Lifecycle_Manager::END);
    }
}
