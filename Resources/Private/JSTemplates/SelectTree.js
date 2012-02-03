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
        baseParams: buildRequestParams('getTree', {})
    });



	var yagCategoryTree = new Tree.TreePanel({
	    autoScroll:true,
	    animate:true,
	    enableDD:true,
	    containerScroll: true,
        root:new Ext.tree.AsyncTreeNode({text:'Tree'}),
	    rootVisible: false,
	    loader: Tree_Category_Loader,
	    listeners: {
	        click: 	function (node,event){
				Ext.get("selectedCategory").set({value: node.id});
                selectedNode = node;
			}
		}
    })
 /*
    var root = new Tree.AsyncTreeNode({
        text:'Categories',
        draggable:false,
        id:1
    });
    yagCategoryTree.setRootNode(root);
    */
    yagCategoryTree.render('categoryTreeDiv');
    //root.expand();
   // yagCategoryTree.getSelectionModel().select(yagCategoryTree.getNodeById("2"));

});