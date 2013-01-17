-------------
Configuration
-------------


Abstract Configuration Builder
------------------------------

The configurationBuilder can be implemented in your extension to:

	- Evaluate your typoscript settings at ONE single point before running your actual extension-code
	- Build configuration objects with working code code completion within the IDE
	- Access this everywhere in the code

The configuration builder works as registry for the configuration objects. It builds these objects on its first access and the serves them on further requests from its local cache.


Abstract Configuration
----------------------

The abstract configuration can be used for implementing your extension configuration objects. The derived object has to implement the ``init()`` function, which is then called from the configurationBuilder during the init process. The init function evaluates and sets the properties of this object.

The following example shows an implementation of the AbstractConfiguration. The variable ``useSession`` is define ind the header and set to a default value. Within the init section this is value is set to a true boolean value with the utility function ``setBooleanIfExistsAndNotNothing`` which only sets the variable if it is set in the settings.::

	class Tx_PtExtlist_Domain_Configuration_Base_BaseConfig
		extends Tx_PtExtbase_Configuration_AbstractConfiguration {


    	/**
    	 * @var bool
    	 */
    	protected $useSession = FALSE;



        /**
         * Template method for initializing this config object by injected
         * TypoScript settings.
         *
         * @return void
         */
    	protected function init() {
    		$this->setBooleanIfExistsAndNotNothing('useSession');
    	}


    	/**
    	 * @return bool
    	 */
    	public function getUseSession() {
    		return $this->useSession;
    	}

    }

These utility functions are available:

- ``setValueIfExists($tsKey, $internalPropertyName = NULL)``: The first value is the key from the typoscript array, the second optional defines the internal property name if it differs from the settings key.

- ``setValueIfExistsAndNotNothing($tsKey, $internalPropertyName = NULL)``

- ``setBooleanIfExistsAndNotNothing($tsKey, $internalPropertyName = NULL)``

- ``setRequiredValue($tsKey, $errorMessageIfNotExists, $internalPropertyName = NULL)``: If this method is used and the value is not present, an exception is thrown. The second parameter defines the exception message.