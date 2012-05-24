/**
 * Interactive jsTree for extbase
 *
 * @author Sebastian Helzle <helzle@punkt.de>
 */

(function($) {

	var baseURL = '###baseUrl###',
		treeDiv = '#ptExtbaseTreeDiv',
		debug = true,
		jsTreeInstance = undefined,
		baseRequest = {
			extensionName: 'ptExtbase',
			pluginName: 'ptx',
			controllerName: 'Tree'
		};

	var treeOptions = {
		'json_data': {
			//'progressive_render' : true,
			'ajax': {
				'url': baseURL,
				'data': function(node) {
					log('Requesting tree data for ' + node);
					return $.extend({}, baseRequest, {
						"actionName" : "getTree"
					});
				}
			}
		},
		'plugins': ['themes', 'json_data', 'ui', 'cookies', 'dnd']
	};

	function log(message) {
		window.console && debug ? window.console.log(message) : alert(message);
	}

	function moveNode(e, data) {
		data.rslt.o.each(function(i) {

			var node = $(this),
				nodeId = node.attr('id'),
				newPosition = data.rslt.cp + i,
				targetNode = undefined,
				parent = $(data.rslt.cr),
				actionName = 'test';//'moveNodeAfter';

			if (newPosition == 1) {
				// Check if node has a sibling next to it
				var next = node.next();
				if (next.length) {
					actionName = 'moveNodeBefore';
					targetNode = next.attr('id');
				} else {
					actionName = 'moveNodeInto';
					targetNode = parent.attr('id');
				}
			} else if (newPosition > 1) {
				// Check if node has a sibling before
				var prev = node.prev();
				if (prev.length) {
					actionName = 'moveNodeAfter';
					targetNode = prev.attr('id');
				} else {
					actionName = 'moveNodeInto';
					targetNode = parent.attr('id');
				}
			} else {
				actionName = 'moveNodeInto';
				targetNode = parent.attr('id');
			}

			log(actionName + " node " + nodeId + " to position " + newPosition);
			log("Node parent is " + parent.attr('id'));

			// Send move request
			$.ajax({
				async : false,
                type: 'get',
                url: baseURL,
                data : $.extend({}, baseRequest, {
                    actionName : actionName,
                	arguments: {
	                    node : nodeId,
	                    targetNode: targetNode
                	}
                }),
                success : function (r) {
                	log(r);
                    /*if(!r.status) {
                        $.jstree.rollback(data.rlbk);
                    } else {
                        $(data.rslt.oc).attr("id", r.id);
                        if(data.rslt.cy && $(data.rslt.oc).children("ul").length)
                            data.inst.refresh(data.inst._get_parent(data.rslt.oc));
                    }*/
                }
			});
		});
	}

	$(function() {
		// Load data and init tree
		log('Creating tree');
		jsTreeInstance = $(treeDiv).jstree(treeOptions)
			.bind("move_node.jstree", moveNode);
	});
})(jQuery);
