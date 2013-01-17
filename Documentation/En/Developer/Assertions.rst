----------
Assertions
----------

The static class ``Tx_PtExtbase_Assertions_Assert`` which includes different assertion functions.

Example: Test if a value is a positive integer. The second parameter denotes if the integer is allowed to be false::

	Tx_PtExtbase_Assertions_Assert::isPositiveInteger($var, FALSE, array('message' => 'The given value has to be a positive integer');