<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class ExtJsJsonWriterVisitor implements TreeWalkerVisitorInterface
{
    /**
     * @var array
     */
    protected $nodeArray = [];


    /**
     * Holds stack of unfinished nodes
     *
     * @var Stack
     */
    protected $nodeStack;


    /**
     * @var array
     */
    protected $selection;


    /**
     * @var boolean
     */
    protected $multipleSelect;


    /**
     * A callback function to call via call_user_func in doFirstVisit
     *
     * @var array(target => className|object, method => method)
     */
    protected $firstVisitCallback = null;


    /**
     * A callback function to call via call_user_func in doFirstVisit
     *
     * @var array(target => className|object, method => method)
     */
    protected $lastVisitCallback = null;


    /**
     * @var integer
     */
    protected $maxDepth = PHP_INT_MAX;



    /**
     * Constructor for visitor
     */
    public function __construct()
    {
        $this->nodeStack = new Stack();
    }


    /**
     * @see TreeWalkerVisitorInterface::doFirstVisit()
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treeWalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     * @throws \Exception
     */
    public function doFirstVisit(NodeInterface $node, &$index, &$level)
    {
        $arrayForNode = [
            'id' => $node->getUid(),
            'text' => $node->getLabel(),
            'children' => [],
            'leaf' => !$node->hasChildren(),
            'cls' => $node->isAccessible() ? '' : 'disabled',
        ];

        $this->setSelectionOnNodeArray($node, $arrayForNode);

        if ($this->firstVisitCallback) {
            $return = call_user_func([$this->firstVisitCallback['target'], $this->firstVisitCallback['method']], $node, $arrayForNode);
            if ($return === false) {
                throw new \Exception('It was not possible to call '.  get_class($this->firstVisitCallback['target']) . '::' . $this->firstVisitCallback['method'], 1328468070);
            } else {
                $arrayForNode = $return;
            }
        }

        $this->nodeStack->push($arrayForNode);
    }



    /**
     * @param $node
     * @param $arrayForNode
     */
    protected function setSelectionOnNodeArray($node, &$arrayForNode)
    {
        if ($this->multipleSelect) {
            if (is_array($this->selection) && in_array($node->getUid(), $this->selection)) {
                $arrayForNode['checked'] = true;
            } else {
                $arrayForNode['checked'] = false;
            }
        } else {
            if ($node->getUid() == (int) $this->selection) {
                $arrayForNode['cls'] = 'selectedNode';
            }
        }
    }



    /**
     * @see TreeWalkerVisitorInterface::doLastVisit()
     *
     * @param NodeInterface $node
     * @param integer &$index Holds the visitation index of treewalker
     * @param integer &$level Holds level of visitation in tree, starting at 1
     */
    public function doLastVisit(NodeInterface $node, &$index, &$level)
    {
        $currentNode = $this->nodeStack->top();
        $this->nodeStack->pop();

        if ($this->lastVisitCallback) {
            $currentNode = call_user_func([$this->lastVisitCallback['target'], $this->lastVisitCallback['method']], $node, $currentNode);
        }

        if (!$this->nodeStack->isEmpty()) {
            $parentNode = $this->nodeStack->top();
            $this->nodeStack->pop();
            $parentNode['children'][] = $currentNode;
            $currentNode['leaf'] = 'false';
            $this->nodeStack->push($parentNode);
        } else {
            $this->nodeArray = $currentNode;
        }
    }



    /**
     * Returns array structure for visited nodes
     *
     * @return array
     */
    public function getNodeArray()
    {
        return $this->nodeArray;
    }



    /**
     * @param boolean $multipleSelect
     */
    public function setMultipleSelect($multipleSelect)
    {
        $this->multipleSelect = $multipleSelect;
    }


    /**
     * @param $selection
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
    }

    /**
     * @param integer $maxDepth
     */
    public function setMaxDepth($maxDepth)
    {
        $this->maxDepth = $maxDepth;
    }



    /**
     * @param object|string $target object or className
     * @param string $method
     */
    public function registerFirstVisitCallback($target, $method)
    {
        $this->checkCallBack('firstVisitCallBack', $target, $method);

        $this->firstVisitCallback = [
            'target' => $target,
            'method' => $method
        ];
    }


    /**
     * @param mixed $target object or className
     * @param string $method
     */
    public function registerLastVisitCallBack($target, $method)
    {
        $this->checkCallBack('lastVisitCallBack', $target, $method);

        $this->lastVisitCallback = [
            'target' => $target,
            'method' => $method
        ];
    }


    /**
     * @param $type
     * @param $target
     * @param $method
     * @throws \Exception
     */
    protected function checkCallBack($type, $target, $method)
    {
        if (is_object($target)) {
            if (!method_exists($target, $method) || !is_callable([$target, $method])) {
                throw new \Exception('The method ' . $method . ' is not accessible on object of type ' . get_class($target) . ' for use as ' . $type, 1328462239);
            }
        } else {
            if (!class_exists($target)) {
                throw new \Exception('The class ' . $target . ' could not be found for use as ' . $type, 1328462359);
            }
            if (!method_exists($target, $method)) {
                throw new \Exception('The method ' . $method . ' is not accessible in class ' . $target . ' for use as ' . $type, 1328462244);
            }
        }
    }
}
