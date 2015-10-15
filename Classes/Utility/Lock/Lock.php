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


use TYPO3\CMS\Core\Utility\GeneralUtility;

class Lock
{
    const LOCK_STRATEGY_MYSQL = 'PunktDe\\PtExtbase\\Utility\\Lock\\MySqlLockStrategy';

    /**
     * @var \PunktDe\PtExtbase\Utility\Lock\LockStrategyInterface
     */
    protected $lockStrategy;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var boolean
     */
    protected $exclusiveLock = true;


    /**
     * @param string $subject
     * @param string $lockStrategyClass
     * @param boolean $exclusiveLock
     * @throws \Exception
     */
    public function __construct($subject, $lockStrategyClass = Lock::LOCK_STRATEGY_MYSQL, $exclusiveLock = true)
    {
        $this->lockStrategy = GeneralUtility::makeInstance($lockStrategyClass);
        if (!($this->lockStrategy instanceof LockStrategyInterface)) {
            throw new \Exception(sprintf('The given locking strategy class %s does not implement the LockStrategyInterface', $lockStrategyClass), 1428675841);
        }

        $this->lockStrategy->acquire($subject, $exclusiveLock);
    }


    /**
     * @return LockStrategyInterface
     */
    public function getLockStrategy()
    {
        return $this->lockStrategy;
    }


    /**
     * Releases the lock
     * @return boolean TRUE on success, FALSE otherwise
     */
    public function release()
    {
        if ($this->lockStrategy instanceof LockStrategyInterface) {
            return $this->lockStrategy->release();
        }
        return true;
    }


    /**
     * Destructor, releases the lock
     * @return void
     */
    public function __destruct()
    {
        $this->release();
    }
}
