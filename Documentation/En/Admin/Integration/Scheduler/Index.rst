---------------
Scheduler-Tasks
---------------

SqlRunnerTask
-------------

Classname
	``Tx_PtExtbase_Scheduler_SqlRunner_SqlRunnerTaskAdditionalFields``
Implements
	``tx_scheduler_AdditionalFieldProvider``

The SqlRunnerTask executes a batch of SQLs. The SQLs can be provided by

#. plain SQL files
#. classes, which implement interface ``Tx_PtExtbase_SqlGenerator_SqlGeneratorInterface``

By sticking to the paradigm *convention over configuration* the SqlRunner looks for these files in dedicated directories of each
installed extension:

SQL
	``Resources/Private/Sql/``
PHP
	``Classes/Domain/SqlGenerator/``

They can be selected in a dropdown of the SqlRunnerTask in the Scheduler module.
