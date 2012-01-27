Ext.onReady(function(){
        
    var baseRequest = {extensionName:'yag',pluginName:'pi1',controllerName:'Category'};

    Ext.getUrlParam = function(param) {
	   var params = Ext.urlDecode(location.search.substring(1));
	   return param ? params[param] : params;
	};
    
	function buildRequest(action, arguments) {
		var actionRequest = baseRequest;
		actionRequest.actionName = action;

		if(arguments) {
			actionRequest.arguments = arguments;
		}

		return actionRequest;
	}
    
    var Tree = Ext.tree;

	// Define the tree Category Loader
	var Tree_Category_Loader = new Tree.TreeLoader({
        dataUrl:"ajax.php",
        baseParams: {
        	id: Ext.getUrlParam('id'),
        	ajaxID: 'yagAjaxDispatcher',
			request: Ext.encode(buildRequest('getSubTree')),
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
        },{
        	xtype: 'label',
        	text: 'Description:'
        },{
            xtype: 'textarea',
            name: 'description',
            flex: 1  // Take up all *remaining* vertical space
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
					        url:'ajax.php',
					        params: {
								id: Ext.getUrlParam('id'),
								ajaxID: 'yagAjaxDispatcher',
					        	request: Ext.encode(
							        	buildRequest('removeCategory',{
							        		nodeId: selectedItem.id,
								        }))
							},
					        success:function(response, request) {
								selectedItem.remove();
					        },
					        failure:function() {
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
			                    	handleSave(createEditForm.getForm().findField('name').getValue(), createEditForm.getForm().findField('description').getValue());
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
			        	createEditForm.getForm().findField('description').setValue('');
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
		
					handleCreate = function(text, description){
						if(text){

							var newNode = new Ext.tree.TreeNode ({
								id : 'new',
								text : text,
								description: description
							});
							
							if(selectedItem.isLeaf()) {
								selectedItem.parentNode.insertBefore(newNode, selectedItem.nextSibling);
							} else {
								selectedItem.insertBefore(newNode, selectedItem.firstChild);
								selectedItem.expand();
							}

							Ext.Ajax.request({
						        url:'ajax.php',
						        params: {
									id: Ext.getUrlParam('id'),
									ajaxID: 'yagAjaxDispatcher',
						        	request: Ext.encode(
								        	buildRequest('addCategory',{
								        		parentNodeId: selectedItem.id,
									        	nodeTitle: text,
									        	nodeDescription: description
									        }))
								},
						        success:function(response, request) {
							        alert(response.responseText);
						        },
						        failure:function() {
						            alert("Error while saving the new node.");
						            newNode.delete();
						        }
						    
						    });
						}
					}
						
					
					if(!createNewWindow){
			            createNewWindow = new Ext.Window({
			                title: 'Add new Category',
			                width: 300, height: 200, layout: 'fit', plain: true, bodyStyle: 'padding:5px;', buttonAlign: 'center',
			                items: createEditForm,
			                buttons: [{
			                    text: 'Save',
			                    handler: function(){
			                    	handleCreate(createEditForm.getForm().findField('name').getValue(), createEditForm.getForm().findField('description').getValue());
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
		        	createEditForm.getForm().findField('description').setValue('');
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
    yagCategoryTree.getSelectionModel().select(yagCategoryTree.getNodeById("2"));

});