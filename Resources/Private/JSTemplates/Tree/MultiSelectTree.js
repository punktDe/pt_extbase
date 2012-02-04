/**
 * ExtJs Tree
 *
 * @author Daniel Lienert <daniel@lienert.cc>
 */

Ext.onReady(function(){
    var Tree = Ext.tree;

	var ptExtbaseTree = new Tree.TreePanel({
	    autoScroll:true,
	    animate:true,
	    enableDD:false,
	    containerScroll: true,
        useArrows:true,
        root: new Ext.tree.AsyncTreeNode({
                    expanded: true,
                    children: ###nodeJSON###
        }),
	    rootVisible: false,
        frame: true,
	    loader: new Ext.tree.TreeLoader(),
	    listeners: {
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