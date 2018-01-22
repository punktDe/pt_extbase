<?php

/*
 * This file is part of the pt_extbase package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

/**
 * Singleton registry
 */
final class Tx_PtExtbase_Registry_Registry extends PunktDe\PtExtbase\Collection\Collection
{
    /**
     * @var Tx_PtExtbase_Registry_Registry Unique instance of this class
     */
    private static $uniqueInstance = null;

    /**
     * Returns a unique instance of the Singleton object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  Tx_PtExtbase_Registry_Registry      unique instance of the Singleton object
     */
    public static function getInstance(): Tx_PtExtbase_Registry_Registry
    {
        if (self::$uniqueInstance === null) {
            self::$uniqueInstance = new Tx_PtExtbase_Registry_Registry();
        }
        return self::$uniqueInstance;
    }

    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
     */
    final public function __clone()
    {
        trigger_error('Clone is not allowed for ' . get_class($this) . ' (Singleton)', E_USER_ERROR);
    }

    /**
     * Registers an object to the registry
     *
     * @param    mixed $label label, use namespaces here to avoid conflicts
     * @param    mixed $object object
     * @param    bool $overwrite (optional) overwrite existing object, default is false
     * @return    void
     * @throws    Exception    if the given label already exists and overwrite if false
     */
    public function register($label, $object, $overwrite = false)
    {
        // swapping $label (id) and $object parameters
        Tx_PtExtbase_Assertions_Assert::isNotEmpty($label, ['message' => 'Registry keys cannot be empty!']);

        if (!$this->hasItem($label) || $overwrite == true) {

            // add object to the collection
            parent::addItem($object, $label);
        } else {
            throw new \Exception(sprintf('There is already an element stored with the label "%s" (and overwriting not permitted)!', $label), 1517211247);
        }
    }

    /**
     * @param mixed $itemObj
     * @param int $id
     * @throws Exception
     */
    public function addItem($itemObj, $id = 0)
    {
        throw new \Exception('The Method addItem is not available anymore, please use the method register to add item.', 1516951742);
    }

    /**
     * Unregisters a label
     *
     * @param mixed $label
     * @throws Exception if the label does not exists (uncaught exception from "deleteItem")
     */
    public function unregister($label)
    {
        $this->deleteItem($label);
    }

    /**
     * Gets the object for a given label
     *
     * @param mixed $label
     * @return mixed object
     * @throws Exception if the label does not exists (uncaught exception from "getItemById")
     */
    public function get($label)
    {
        return $this->getItemById($label);
    }

    /**
     * Checks if the label exists
     *
     * @param    mixed    label
     * @return    bool
     */
    public function has($label)
    {
        return $this->hasItem($label);
    }


    /***************************************************************************
     * Magic methods wrappers for registry pattern methods
     *
     * $reg = tx_pttools_registry::getInstance();
     * $reg->myObject = new SomeObject();
     * if (isset($reg->myObject)) {
     *        // there is a myObject value
     * } else {
     *        // there is not a myObject value
     * }
     * $obj = $reg->myObject;
     * unset($reg->myObject);
     **************************************************************************/

    /**
     * @see Tx_PtExtbase_Registry_Registry::register
     */
    public function __set($label, $object)
    {
        $this->register($label, $object);
    }

    /**
     * @see Tx_PtExtbase_Registry_Registry::unregister
     */
    public function __unset($label)
    {
        $this->unregister($label);
    }

    /**
     * @see Tx_PtExtbase_Registry_Registry::get
     */
    public function __get($label)
    {
        return $this->get($label);
    }

    /**
     * @see Tx_PtExtbase_Registry_Registry::has
     */
    public function __isset($label)
    {
        return $this->has($label);
    }
}
