<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
 *
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * View helper which returns button with icon
 * = Available Icons =
 *  [0] => actions-document-close
    [1] => actions-document-duplicates-select
    [2] => actions-document-edit-access
    [3] => actions-document-export-csv
    [4] => actions-document-export-t3d
    [5] => actions-document-history-open
    [6] => actions-document-import-t3d
    [7] => actions-document-info
    [8] => actions-document-localize
    [9] => actions-document-move
    [10] => actions-document-new
    [11] => actions-document-open
    [12] => actions-document-open-read-only
    [13] => actions-document-paste-after
    [14] => actions-document-paste-into
    [15] => actions-document-save
    [16] => actions-document-save-close
    [17] => actions-document-save-new
    [18] => actions-document-save-view
    [19] => actions-document-select
    [20] => actions-document-synchronize
    [21] => actions-document-view
    [22] => actions-edit-add
    [23] => actions-edit-copy
    [24] => actions-edit-copy-release
    [25] => actions-edit-cut
    [26] => actions-edit-cut-release
    [27] => actions-edit-delete
    [28] => actions-edit-hide
    [29] => actions-edit-insert-default
    [30] => actions-edit-localize-status-high
    [31] => actions-edit-localize-status-low
    [32] => actions-edit-merge-localization
    [33] => actions-edit-pick-date
    [34] => actions-edit-rename
    [35] => actions-edit-restore
    [36] => actions-edit-undelete-edit
    [37] => actions-edit-undo
    [38] => actions-edit-unhide
    [39] => actions-edit-upload
    [40] => actions-input-clear
    [41] => actions-insert-record
    [42] => actions-insert-reference
    [43] => actions-message-error-close
    [44] => actions-message-information-close
    [45] => actions-message-notice-close
    [46] => actions-message-ok-close
    [47] => actions-message-warning-close
    [48] => actions-move-down
    [49] => actions-move-left
    [50] => actions-move-move
    [51] => actions-move-right
    [52] => actions-move-to-bottom
    [53] => actions-move-to-top
    [54] => actions-move-up
    [55] => actions-page-move
    [56] => actions-page-new
    [57] => actions-page-open
    [58] => actions-selection-delete
    [59] => actions-system-backend-user-emulate
    [60] => actions-system-backend-user-switch
    [61] => actions-system-cache-clear
    [62] => actions-system-cache-clear-impact-high
    [63] => actions-system-cache-clear-impact-low
    [64] => actions-system-cache-clear-impact-medium
    [65] => actions-system-cache-clear-rte
    [66] => actions-system-extension-documentation
    [67] => actions-system-extension-download
    [68] => actions-system-extension-import
    [69] => actions-system-extension-install
    [70] => actions-system-extension-uninstall
    [71] => actions-system-extension-update
    [72] => actions-system-help-open
    [73] => actions-system-list-open
    [74] => actions-system-options-view
    [75] => actions-system-pagemodule-open
    [76] => actions-system-refresh
    [77] => actions-system-shortcut-new
    [78] => actions-system-tree-search-open
    [79] => actions-system-typoscript-documentation
    [80] => actions-system-typoscript-documentation-open
    [81] => actions-template-new
    [82] => actions-version-document-remove
    [83] => actions-version-page-open
    [84] => actions-version-swap-version
    [85] => actions-version-swap-workspace
    [86] => actions-version-workspace-preview
    [87] => actions-version-workspace-sendtostage
    [88] => actions-view-go-back
    [89] => actions-view-go-down
    [90] => actions-view-go-forward
    [91] => actions-view-go-up
    [92] => actions-view-list-collapse
    [93] => actions-view-list-expand
    [94] => actions-view-paging-first
    [95] => actions-view-paging-first-disabled
    [96] => actions-view-paging-last
    [97] => actions-view-paging-last-disabled
    [98] => actions-view-paging-next
    [99] => actions-view-paging-next-disabled
 * = Examples =
 *
 * <code title="Default">
 * <f:be.buttons.icon uri="{f:uri.action()}" />
 * </code>
 * <output>
 * An icon button as known from the TYPO3 backend, skinned and linked with the default action of the current controller.
 * Note: By default the "close" icon is used as image
 * </output>
 *
 * <code title="Default">
 * <f:be.buttons.icon uri="{f:uri.action(action:'new')}" icon="new_el" title="Create new Foo" />
 * </code>
 * <output>
 * This time the "new_el" icon is returned, the button has the title attribute set and links to the "new" action of the current controller.
 * </output>
 *
 * @author Steffen Kamper <info@sk-typo3.de>
 * @author Bastian Waidelich <bastian@typo3.org>
 * @author Daniel Lienert <daniel@lienert.cc>
 *
 * @license http://www.gnu.org/copyleft/gpl.html
 */
class Tx_PtExtbase_ViewHelpers_Be_Buttons_IconViewHelper extends Tx_Fluid_ViewHelpers_Be_AbstractBackendViewHelper {

	/**
	 * Renders an icon link as known from the TYPO3 backend
	 *
	 * @param string $uri the target URI for the link. If you want to execute JavaScript here, prefix the URI with "javascript:"
	 * @param string $icon Icon to be used.
	 * @param string $title Title attribte of the resulting link
	 * @return string the rendered icon link
	 */
	public function render($uri, $icon = 'actions-document-close', $title = '') {
		return '<a href="' . $uri . '">' . t3lib_iconWorks::getSpriteIcon($icon, array('title' => $title)) . '</a>';
	}
}
?>
