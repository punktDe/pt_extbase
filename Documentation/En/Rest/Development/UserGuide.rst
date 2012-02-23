Controller
==========


AbstractActionController
------------------------

ViewHelpers
===========

CommentViewHelper
-----------------

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

Example

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



Utility
=======



AjaxDispatcher
--------------



eIDDispatcher
-------------



FakeFrontendFactory
-------------------
FakeFrontendFactory enables the use of frontend functionality like cObj-Rendering in the backend. To fake a frontend you just have to call the factory:

t3lib_div::makeInstance('Tx_PtExtbase_Utility_FakeFrontendFactory')->createFakeFrontend();


HeaderInclusion
---------------



NameSpace
---------



Tca
---