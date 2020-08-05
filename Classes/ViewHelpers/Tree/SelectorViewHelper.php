<?php
namespace PunktDe\PtExtbase\ViewHelpers\Tree;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert
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

use PunktDe\PtExtbase\Tree\ExtJsJsonWriterVisitor;
use PunktDe\PtExtbase\Tree\JsonTreeWriter;
use PunktDe\PtExtbase\Tree\TreeContext;
use PunktDe\PtExtbase\Tree\TreeRepositoryBuilder;
use PunktDe\PtExtbase\ViewHelpers\Javascript\TemplateViewHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\ViewHelpers\Form\HiddenViewHelper;

/**
 * Class implements a viewhelper that renders a tree selector widget.
 *
 * @example Usage:<code>
 *
 * <ptx:tree.selector repository="{nodeRepositoryClassName}"
 *				 namespace="{treeNamespace}"
 *				 name="{formFieldName}"
 *				 id="{formFieldName}"
 *				 value="{selectedValuesCommaSeparated}"
 *				 multiple="1"
 *               restrictedDepth="{restrictedDepth}"
 * />
 *
 * Following parameters are available:
 *
 * repository       Repository class name to be used as node repository (not as tree repository!)
 * namespace        Namespace for which to create tree
 * name             Name of the form field (see input.text viewhelper!)
 * value            Uid of selected node (if in 1:N mode) or comma separated list of UIDs (if in M:N mode)
 * multiple         If set to 1, multiple nodes can be selected in widget
 * restrictedDepth  If a value is given, tree is only rendered to given depth (1 = only root node is rendered)
 *
 * </code>
 *
 * @author Daniel Lienert
 */
class SelectorViewHelper extends HiddenViewHelper
{
    /**
     * @var string
     */
    protected $nodes;



    /**
     * @var boolean
     */
    protected $multiple;



    /**
     * @var TreeContext
     */
    protected $treeContext;


    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * @param TreeContext $treeContext
     * @return void
     */
    public function injectTreeContext(TreeContext $treeContext)
    {
        $this->treeContext = $treeContext;
    }


    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }


    /**
     * Initialize arguments.
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('nodes', 'string', 'The tree nodes as JSON Array', false);
        $this->registerArgument('repository', 'string', 'Specifies the node repository', false);
        $this->registerArgument('namespace', 'string', 'Specifies the tree namespace', false);
        $this->registerArgument('multiple', 'boolean', 'Specifies if the tree is a multiple or single select tree', false, false);
        $this->overrideArgument('id', 'string', 'Specifies the field and div ID', true, 'ptExtbaseTreeSelector');
        $this->registerArgument('restrictedDepth', 'int', 'Depth of tree to be rendered', false);
        $this->registerArgument('expand', 'string', 'Expand Mode. "all" or "root"', false, 'root');
        $this->registerArgument('respectEnableFields', 'int', '0 = Show all entries, 1 = do not display hidden', false, 1);
    }


    /**
     * Initialize the viewHelper
     * @throws \Exception
     */
    public function initialize()
    {
        parent::initialize();

        $this->multiple = $this->arguments['multiple'];
        $this->nodes = trim($this->arguments['nodes']);

        if (!$this->nodes) {
            if (!$this->arguments['repository']) {
                throw new \Exception('Either treeNodes or a treeNodeRepository has to be given to use the viewHelper.', 1328536673);
            }
        }
    }



    /**
     * Renders the treeSelector.
     *
     * @return string
     */
    public function render()
    {
        $formField = parent::render($required, 'hidden', null);

        if (!$this->nodes) {
            $this->nodes = $this->getTreeNodes();
        }

        $treeDiv = $this->getTreeDiv();
        $treeJS = $this->getTreeJS($this->nodes);

        return $formField . $treeDiv . $treeJS;
    }



    /**
     * Get Tree nodes as JSON array
     *
     * @return string JSON array
     */
    protected function getTreeNodes()
    {
        $treeRepositoryBuilder = TreeRepositoryBuilder::getInstance();
        $treeRepositoryBuilder->setNodeRepositoryClassName($this->arguments['repository']);

        $treeRepository = $treeRepositoryBuilder->buildTreeRepository();

        if ($this->arguments['respectEnableFields']) {
            $this->treeContext->setRespectEnableFields(true);
        } else {
            $this->treeContext->setRespectEnableFields(false);
        }
        $tree = $treeRepository->loadTreeByNamespace($this->arguments['namespace']);

        if (isset($this->arguments['restrictedDepth'])) {
            $tree->setRestrictedDepth($this->arguments['restrictedDepth']);
            $tree->setRespectRestrictedDepth(true);
        }

        $arrayWriterVisitor = $this->objectManager->get(ExtJsJsonWriterVisitor::class);
        $arrayWriterVisitor->setMultipleSelect($this->arguments['multiple']);
        $arrayWriterVisitor->setSelection($this->getSelection());

        $jsonTreeWriter = $this->objectManager->get(JsonTreeWriter::class, [$arrayWriterVisitor], $arrayWriterVisitor);

        return $jsonTreeWriter->writeTree($tree);
    }



    /**
     * @return array|int
     */
    protected function getSelection()
    {
        if ($this->multiple) {
            return GeneralUtility::trimExplode(',', $this->arguments['value'], true);
        } else {
            return (int) trim($this->arguments['value']);
        }
    }


    /**
     * Build and return the javascript via the javascript viewHelper
     * @todo refactor JSViewHelper and move the marker code to a separate utility, call the utility here
     *
     * @param string $treeNodes treeNode JSON
     * @return string
     * @throws \Exception
     */
    protected function getTreeJS($treeNodes)
    {

        /** @var TemplateViewHelper $treeViewHelper  */
        $treeViewHelper = GeneralUtility::makeInstance(ObjectManager::class)->get(TemplateViewHelper::class);
        //$treeViewHelper->setControllerContext($this->controllerContext);

        $treeViewHelper->initialize();

        return $treeViewHelper->render('EXT:pt_extbase/Resources/Private/JSTemplates/Tree/SelectTree.js',
            [
                'nodeJSON' => $treeNodes,
                'multiple' => $this->multiple ? 'true': 'false',
                'fieldId' => $this->arguments['id'],
                'expand' => $this->arguments['expand'],
            ], false, false
        );
    }



    /**
     * @return string
     */
    protected function getTreeDiv()
    {
        return '<div id="'.$this->arguments['id'].'Div"></div>';
    }
}
