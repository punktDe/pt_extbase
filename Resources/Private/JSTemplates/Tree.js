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
        baseParams: {
			extensionName: 'ptExtbase',
			pluginName: 'ptx',
			controllerName: 'Tree',
			actionName: 'getTree'
        }
    });


	// Create / Edit Form definition
	 var createEditForm = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        labelWidth: 55,
        layout: {
            type: 'vbox',
            align: 'stretch'  // Child items are stretched to full width
        },
        defaults: {
            xtype: 'textfield'
        },

        items: [{
        	xtype: 'label',
        	text: 'Name:'
        },{
        	xtype: 'textfield',
            name: 'name'
        }]
    });

	
	var createNewWindow;
	var editWindow;
    var button = Ext.get('show-btn');
    
	var yagCategoryTree = new Tree.TreePanel({
	    autoScroll:true,
	    animate:true,
	    enableDD:true,
	    containerScroll: true,
	    rootVisible: true,
	    loader: Tree_Category_Loader,
	    listeners: {
	        click: 	function (node,event){
				Ext.get("selectedCategory").set({value: node.id});
			}
		},

	    tbar : [{
		    	xtype : 'button' , text : 'Delete', 
				handler : function(){ 
			    	var selectedItem = yagCategoryTree.getSelectionModel().getSelectedNode();
					
					if(selectedItem){
						
						Ext.Ajax.request({
					        url:baseURL,
                            params: buildRequestParams('removeNode',{
                                node: selectedItem.id
                            }),
					        success:function(response, request) {
								selectedItem.remove();
                                if(response.responseText) alert(response.responseText);
					        },
					        failure:function(response) {
					            alert("Error while deleting the Category.");
					        }
					    });
					}
		    	}
	    	},{
		    	xtype : 'button' , text : 'Edit', 
				handler : function(){ 
			    	var selectedItem = yagCategoryTree.getSelectionModel().getSelectedNode();
					
					if(selectedItem){
						createEditForm.getForm().findField('name').setValue(selectedItem.text);
			        	createEditForm.getForm().findField('description').setValue(selectedItem.attributes.description);

			        	handleSave = function(text, description){
							if(text){

								Ext.Ajax.request({
							        url:'ajax.php',
							        params: {
										id: Ext.getUrlParam('id'),
										ajaxID: 'yagAjaxDispatcher',
							        	request: Ext.encode(
									        	buildRequest('saveCategory',{
									        		category: selectedItem.id,
										        	categoryTitle: text,
										        	categoryDescription: description
										        }))
									},
							        success:function(response, request) {
								        selectedItem.setText(text);
								        selectedItem.description = description;
							        },
							        failure:function() {
							            alert("Error while saving the category.");
							        }
							    
							    });
							}
						}
						
			        	editWindow = new Ext.Window({
			                title: 'Edit Category',
			                width: 300, height: 200, layout: 'fit', plain: true, bodyStyle: 'padding:5px;', buttonAlign: 'center',
			                items: createEditForm,
			                buttons: [{
			                    text: 'Save',
			                    handler: function(){
			                    	handleSave(createEditForm.getForm().findField('name').getValue());
			                    	editWindow.hide();
	                    		}
			                },{
			                    text: 'Cancel',
			                    handler: function(){
			                		editWindow.hide();
		                    	}
			                }]
			            });
			            
			        	createEditForm.getForm().findField('name').setValue('');
			        	editWindow.show(this);
			        	
					} else {
						alert('No category selected!');
					}
		    	}
	    	}, {
			    xtype : 'button' , text : 'Add', 
				handler : function(){ 
					var selectedItem = yagCategoryTree.getSelectionModel().getSelectedNode();
		
					if(!selectedItem){
						selectedItem = yagCategoryTree.getRootNode();
					}
		
					handleCreate = function(text){
						if(text){
                            Ext.Ajax.request({
                                url:baseURL,
                                params:buildRequestParams('addNode', {
                                    parent:selectedItem.id,
                                    label:text
                                }),
                                success:function (response, request) {

                                    var newNode = new Ext.tree.TreeNode ({
                                        id : response.responseText,
                                        text : text
                                    });

                                    if(selectedItem.isLeaf()) {
                                        selectedItem.parentNode.insertBefore(newNode, selectedItem.nextSibling);
                                    } else {
                                        selectedItem.insertBefore(newNode, selectedItem.firstChild);
                                        selectedItem.expand();
                                    }
                                },
                                failure:function () {
                                    alert("Error while saving the new node.");
                                }
                            });
						}
					}
						
					
					if(!createNewWindow){
			            createNewWindow = new Ext.Window({
			                title: 'Add new Category',
			                width: 300, height: 120, layout: 'fit', plain: true, bodyStyle: 'padding:5px;', buttonAlign: 'center',
			                items: createEditForm,
			                buttons: [{
			                    text: 'Save',
			                    handler: function(){
			                    	handleCreate(createEditForm.getForm().findField('name').getValue());
			                    	createNewWindow.hide();
	                    		}
			                },{
			                    text: 'Cancel',
			                    handler: function(){
			                		createNewWindow.hide();
		                    	}
			                }]
			            });
			        };
			        
			        createEditForm.getForm().findField('name').setValue('');
					createNewWindow.show(this);
			}
		}],
	});
    
    var root = new Tree.AsyncTreeNode({
        text:'Categories',
        draggable:false,
        id:1
    });
    yagCategoryTree.setRootNode(root);
    yagCategoryTree.render('categoryTreeDiv');
    root.expand();
   // yagCategoryTree.getSelectionModel().select(yagCategoryTree.getNodeById("2"));

});