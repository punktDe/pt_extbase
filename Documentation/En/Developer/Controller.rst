----------
Controller
----------

AbstractActionController
------------------------

The abstraction action controller extends the extbase ActionController and adds some new functions and behaviours.

^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Exchange Single Fluid Templates and Views
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Single fluid templates can be exchanged by TypoScript configuration by using the following syntax::

	plugin.<plugin_key>.settings.controller.<Controller_Name_Without_Controller>.<action_name_without_action>.template = full_path_to_template_with.html

View can be set via TS. View has to be set in TS via::

	plugin.<plugin_key>.settings.controller.<Controller_Name_without_Controller>.<action_Name_without_Action>.view = ViewClassName

^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Lifecycle manager
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The lifecycle manager can be used to trigger actions during the extensions lifecycle. This for example is used to automatically restore the session array at the beginning of the extensions lifecycle and to store it to the database again at the end.