/**
 * ExtJs Tree
 *
 * @author Daniel Lienert <daniel@lienert.cc>
 */

Ext.onReady(function(){
    var Tree = Ext.tree;

    // base URL is set depending on FE or BE environment in widget controller for tree
    var baseURL = '###baseUrl###';
    var baseRequest = {
        extensionName:'ptExtbase',
        pluginName:'ptx',
        controllerName:'Tree'
    };


    Ext.getUrlParam = function(param) {
	   var params = Ext.urlDecode(location.search.substring(1));
	   return param ? params[param] : params;
	};


    /**
     * @param action
     * @param arguments
     */
	function buildRequestParams(action, arguments) {
		var actionRequest = baseRequest;

        actionRequest.actionName = action;

		if(arguments) {
			actionRequest.arguments = arguments;
		}

        var param = {
            request: Ext.encode(actionRequest)
        };

		return param;
	}


    /**
     * Tree node loader
     */
	var Tree_Category_Loader = new Tree.TreeLoader({
        dataUrl:baseURL,
        baseParams: buildRequestParams('getTree', {}),
        baseAttrs: {
            checked:false
        }
    });

	var ptExtbaseTree = new Tree.TreePanel({
	    autoScroll:true,
	    animate:true,
	    enableDD:false,
	    containerScroll: true,
        useArrows:true,
        root:new Ext.tree.AsyncTreeNode({text:'Tree'}),
	    rootVisible: false,
        frame: true,
	    loader: Tree_Category_Loader,
	    listeners: {
/*            'load': function(node) {
                var selectedValues = Ext.get("selectedCategory").getValue().split(',');

                if(selectedValues.indexOf(node.id.toString()) > 0) {
                    node.getUI().toggleCheck(true);
                }

                node.eachChild(function(n) {
                    if(selectedValues.indexOf(n.id.toString()) > 0) {
                        n.getUI().toggleCheck(true);
                    }
                });

            },
*/
            'checkchange': function(node, checked){
                var ids = '', selNodes = ptExtbaseTree.getChecked();

                Ext.each(selNodes, function(node){
                    if(ids.length > 0){
                        ids += ', ';
                    }
                    ids += node.id;
                });

                Ext.get("selectedCategory").set({value: ids});
            }
		}
    })
    ptExtbaseTree.render('ptExtbaseTreeDiv');
    ptExtbaseTree.expandAll();
});