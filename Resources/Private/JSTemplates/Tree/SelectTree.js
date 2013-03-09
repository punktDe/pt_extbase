/**
 * ExtJs Tree
 *
 * @author Daniel Lienert <daniel@lienert.cc>
 */

Ext.BLANK_IMAGE_URL = "###typo3Path###gfx/clear.gif";

Ext.onReady(function(){
    var Tree = Ext.tree;

    var multiple = ###multiple###;
    var expand = '###expand###';

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
            'click': function(node, event){
                if(!multiple) {
                    Ext.get("###fieldId###").set({value: node.id});
                }
            },
            'checkchange': function(node, checked){
                if(multiple) {
                    var ids = '', selNodes = ptExtbaseTree.getChecked();

                    Ext.each(selNodes, function(node){
                        if(ids.length > 0){
                            ids += ', ';
                        }
                        ids += node.id;
                    });

                    Ext.get("###fieldId###").set({value: ids});
                }
            }
		}
    })

    ptExtbaseTree.render('###fieldId###Div');

    if(expand == 'root') {
        Ext.each(ptExtbaseTree.getRootNode().childNodes, function(node){
            node.expand();
        });
    }

    if(expand == 'all') {
        ptExtbaseTree.expandAll();
    }
});

