/**
 * Interactive jsTree for extbase
 *
 * @author Sebastian Helzle <helzle@punkt.de>
 */

(function($) {

	var baseURL = '###baseUrl###',
		dbNodeTable = '###dbNodeTable###',
		treeDiv = '#ptExtbaseTreeDiv',
		debug = false,
		jsTreeInstance = undefined,
		baseRequest = {
			extensionName: 'ptExtbase',
			pluginName: 'ptx',
			controllerName: 'Tree',
			actionName: '',
			arguments: {}
		};

	function log(message) {
		if (window.console && debug)
			window.console.log(message);
	}

	/**
	 * Creates a request data object
	 */
	function createRequestData(action, arguments) {
		if (!arguments) arguments = {};

		return $.extend(true, {}, baseRequest, { actionName: action, arguments: arguments });
	}

	/**
	 * Options for the jstree jQuery plugin
	 */
	var treeOptions = {
		'plugins': ['themes', 'json_data', 'ui', 'cookies', 'dnd', 'contextmenu', 'crrm'],
		'json_data': {
			//'progressive_render' : true,
			'ajax': {
				'url': baseURL,
				'data': function(node) {
					log('Requesting tree data for node id ' + node);
					return createRequestData("getTree");
				}
			}
		},
		'contextmenu': {
			'items': {
				'create': {
					'label': 'Neue Unterkategorie'
				},
				'rename': {
					'label': 'Umbenennen'
				},
				'remove': false,
				/*
				'remove': {
					'label': 'Loeschen'
				},*/
				'editNode': {
					'label': 'Bearbeiten',
					'action': editNode
				},
				'ccp': false
			}
		}
	};

	/**
	 * Opens a new window to edit the record
	 */
	function editRecord(table, id) {    
		var returnUrl = escape('mod.php?M=txdpppeditor_PtCertificationQuestioncategory'),
			editurl = "alt_doc.php?edit["+table+"]["+id+"]=edit&returnUrl=" + returnUrl;

		// window.open(editurl, 'Edit record');
		self.location.href = editurl;
	}

	function editNode(node) {
		var nodeId = node.attr('id');

		log('Edit node with id ' + nodeId);

		editRecord(dbNodeTable, nodeId);
	}

	function treeLoaded() {
		var treeObj = $(treeDiv);

		var nodes = treeObj.find('li');

		log('There are ' + nodes.length + ' nodes');

		$.each(nodes, function() {
			var node = $(this),
				id = node.attr('id'),
				metadata = node.attr('data-meta');

			// Add metadata to node html when an id and metadata is set
			if (id && metadata !== undefined)
				node.children('a:first').after('<span>(' + metadata + ')</span>');
		});
	}

	function moveNode(e, data) {
		data.rslt.o.each(function(i) {
			var node = $(this),
				nodeId = node.attr('id'),
				newPosition = data.rslt.cp + i,
				requestData = createRequestData(undefined, { node: nodeId }),
				parent = $(data.rslt.cr);

			if (newPosition > 1) {
				// Check if node has a sibling before
				var prev = node.prev();
				if (prev.length) {
					requestData.actionName = 'moveNodeAfter';
					requestData.arguments.targetNode = prev.attr('id');
				} else {
					requestData.actionName = 'moveNodeInto';
					requestData.arguments.targetNode = parent.attr('id');
				}
			} else {
				// Check if node has a sibling next to it
				var next = node.next();
				if (next.length) {
					requestData.actionName = 'moveNodeBefore';
					requestData.arguments.targetNode = next.attr('id');
				} else {
					requestData.actionName = 'moveNodeInto';
					requestData.arguments.targetNode = parent.attr('id');
				}
			}

			log('Data for request:');
			log(requestData);

			// Send move request
			if (requestData.actionName !== undefined) {
				$.getJSON(baseURL, requestData, function (r) {
					log('MoveNode Success: ' + r.status);

					if(!r.status)
						$.jstree.rollback(data.rlbk);
					else {
						$(data.rslt.oc).attr("id", r.id);
						if(data.rslt.cy && $(data.rslt.oc).children("ul").length)
							data.inst.refresh(data.inst._get_parent(data.rslt.oc));
					}
				});
			} else {
				log('Error: action undefined');
			}
		});
	}

	function createNode(e, data) {
		var newNode = $(data.rslt.obj),
			parent = $(data.rslt.parent),
			position = data.rslt.position,
			requestData = createRequestData('addNode', {
				parent: parent.attr('id'),
				label: data.rslt.name
			});

		log(requestData);
		$.getJSON(baseURL, requestData, function (r) {
			log('CreateNode Success: ' + r.status);

			if(r.status) {
				$(data.rslt.obj).attr("id", r.id);
				data.inst.refresh();
			}
			else
				$.jstree.rollback(data.rlbk);
		});
	}

	function removeNode(e, data) {
		var node = $(data.rslt.obj),
			requestData = createRequestData('removeNode', {
				node: node.attr("id")
			});

		log(requestData);

		$.getJSON(baseURL, requestData, function (r) {
			log('RemoveNode Success: ' + r.status);
			
			if(!r.status)
                data.inst.refresh();
		});
	}

	function renameNode(e, data) {
		var node = $(data.rslt.obj),
			requestData = createRequestData('saveNode', {
				node: node.attr("id"),
				label: data.rslt.new_name
			});

		log(requestData);

		$.getJSON(baseURL, requestData, function (r) {
			log('RenameNode Success: ' + r.status);

			if(!r.status)
				$.jstree.rollback(data.rlbk);
		});
	}

	$(function() {
		// Load data and init tree
		log('Creating tree');
		jsTreeInstance = $(treeDiv).jstree(treeOptions)
			.bind("move_node.jstree", moveNode)
			.bind("create.jstree", createNode)
			.bind("remove.jstree", removeNode)
			.bind("rename.jstree", renameNode)
			.bind("reopen.jstree", treeLoaded);
	});
})(jQuery);
