ViewHelpers
===========

CommentViewHelper
-----------------

Just removes everything between the tags.

Example::

	<ptx:comment>
	<!--
	 Here comes the comment which is never rendered.
	-->
	</ptx:comment>



RemoveLineBreaksViewHelper
--------------------------

Remove line breaks from the string.

Arguments:

:``string``: The string to remove the linebreaks from.

Example

``{ptx:format.removeLineBreaks(string:'bla')}``



ExplodeViewHelper
-----------------

Explodes a string by the given delimiter.

Arguments:

:``delimiter``: The delimiter character

:``string``: The string to explode.

Example

``{ptx:explode(delimiter: ',', '1,2,3,4')}``



Tree / PathViewHelper
---------------------

Iterates over the path from a given node Id to the root, to draw a path or a rootline menu.

Adds the following variables to the template:

:node: current node
:firstNode: Boolean, true if first node

Arguments:

:``repository``: Specifies the node repository

:``namespace``: Specifies the tree namespace

:``node``: The node uid

:``skipRoot``: Skip the root node

Example::

	<f:for each="{ptx:explode(delimiter: ',', string:listRow.categoryUid.value.categoryUid)}" as="categoryUid">
	  <div>
	   <ptx:tree.path node="{categoryUid}" skipRoot="1" namespace="tx_ptcertification_domain_model_category" repository="Tx_PtCertification_Domain_Repository_CategoryRepository" >
	   <f:if condition="{firstNode}">
		 <f:then>{node.label}</f:then>
		 <f:else>&raquo; {node.label}</f:else>
	   </f:if>
	  </ptx:tree.path>
	  </div>
	</f:for>


Be / FormTokenViewHelper
-------------------

Just returns a formToken to be used in backend form links

Arguments:

Example:

``{ptx:be.formToken()}``

Returns:

&formToken=<formTokenHash>