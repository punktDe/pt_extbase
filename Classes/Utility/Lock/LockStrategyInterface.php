<?php
namespace PunktDe\PtExtbase\Utility\Lock;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */


/**
 * Contract for a lock strategy.
 *
 * @api
 */
interface LockStrategyInterface
{
    /**
     * @param string $subject
     * @param boolean $exclusiveLock TRUE to, acquire an exclusive (write) lock, FALSE for a shared (read) lock.
     * @return boolean TRUE if lock could be acquired, FALSE otherwise
     */
    public function acquire($subject, $exclusiveLock);


    /**
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function release();
}
