<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class TreeBuilder implements TreeBuilderInterface
{
    /**
     * Holds an instance of node repository
     *
     * @var NodeRepositoryInterface
     */
    protected $nodeRepository;



    /**
     * If set to true, restricted depth will be respected when building the tree
     *
     * @var bool
     */
    protected $respectRestrictedDepth;



    /**
     * If set to a value > 0, tree will only be build up to given level.
     *
     * Level -1 = all levels are build
     * Level 1 = means, only root node will be build
     * Level 2 = root node and its children are build
     * ...
     *
     * @var integer
     */
    protected $restrictedDepth;


    
    /**
     * @var array
     */
    protected $treeCache;




    /**
     * Constructor for treebuilder. Requires node repository as parameter.
     *
     * @param NodeRepositoryInterface $nodeRepository
     */
    public function __construct(NodeRepositoryInterface $nodeRepository)
    {
        $this->nodeRepository = $nodeRepository;
    }



    /**
     * Returns an empty tree with root node labeled by given label
     *
     * @param string $namespace Namespace for tree
     * @param string $rootLabel Label for root node
     * @return Tree Empty tree object.
     */
    public function getEmptyTree($namespace, $rootLabel = '')
    {
        return Tree::getEmptyTree($namespace, $rootLabel);
    }



    /**
     * @param string $namespace
     * @return Tree
     */
    public function buildTreeForNamespaceWithoutInaccessibleSubtrees($namespace)
    {
        if (empty($this->treeCache[$namespace])) {
            $this->treeCache[$namespace] = $this->buildTreeForNamespace($namespace);
        }

        $tree = $this->treeCache[$namespace];
        $root = $tree->getRoot();

        if ($root->isAccessible()) {
            $clonedRoot = $this->getClonedNode($root);
            $this->buildAccessRestrictedTreeRecursively($root, $clonedRoot);
        }

        $clonedTree = null;
        $clonedTree = Tree::getInstanceByRootNode($clonedRoot);
        $clonedTree->setRestrictedDepth($this->restrictedDepth);
        $clonedTree->setRespectRestrictedDepth($this->respectRestrictedDepth);

        return $clonedTree;
    }



    /**
     * @param Node $originalNode
     * @param Node $clonedNode
     */
    protected function buildAccessRestrictedTreeRecursively($originalNode, $clonedNode)
    {
        foreach ($originalNode->getChildren() as $child) {
            if ($child->isAccessible()) {
                $clonedChild = $this->getClonedNode($child);
                $clonedChild->setParent($clonedNode);
                $this->buildAccessRestrictedTreeRecursively($child, $clonedChild);
            }
        }
    }



    /**
     * @param Node $node
     * @return Node
     */
    protected function getClonedNode(Node $node)
    {
        $clonedNode = clone $node;
        $clonedNode->clearRelatives();
        return $clonedNode;
    }



    /**
     * Builds a tree for given namespace.
     *
     * If there are no nodes for given namespace, a new, empty tree with a single root node will be returned.
     *
     * @param $namespace
     * @return Tree
     * @throws \Exception
     */
    public function buildTreeForNamespace($namespace)
    {
        $nodes = $this->nodeRepository->findByNamespace($namespace);

        // We have no nodes for given namespace, so we return empty tree with single root node
        if ($nodes->count() == 0) {
            return $this->getEmptyTree($namespace);
        }

        $stack = new Stack();
        $prevLft = PHP_INT_MAX;

        foreach ($nodes as $node) {
            /* @var $node Node */
            /* Assertion: Nodes must be given in descending left-value order. */
            if ($node->getLft() > $prevLft) {
                throw new \Exception('Nodes must be given in descending left-value order', 1307861852);
            }

            $prevLft = $node->getLft();
            #echo "<br><br>Knoten: " . $node->toString();

            if ($stack->isEmpty() || $stack->top()->getRgt() > $node->getRgt()) {
                $stack->push($node);
                #echo "Pushed on stack:" . $stack->toString();
            } else {
                #echo "Adding children:";
                while (!$stack->isEmpty() && $stack->top()->getRgt() < $node->getRgt()) {
                    #echo "In while - current node " . $node->toString() . " current topStack: " . $stack->top()->toString();
                    $stack->top()->setParent($node, false);
                    $node->addChild($stack->top(), false);
                    $stack->pop();
                    #echo "After while-iteration: ". $stack->toString();
                }
                $stack->push($node);
                #echo "After pushing after while: <ul>" . $stack->toString() . "</ul>";
            }
        }

        $tree = Tree::getInstanceByRootNode($stack->top());

        $tree->setRestrictedDepth($this->restrictedDepth);
        $tree->setRespectRestrictedDepth($this->respectRestrictedDepth);

        #echo "Finished tree: " . $tree->toString();
        return $tree;
    }



    /**
     * Setter for restricted depth.
     *
     * If depth is restricted, tree is build only to given level by tree builder.
     *
     * @param integer $restrictedDepth
     */
    public function setRestrictedDepth($restrictedDepth)
    {
        $this->restrictedDepth = $restrictedDepth;
    }




    /**
     * Sets respect restricted depth to given value.
     *
     * If set to true, tree builder will respect restricted depth, when building tree.
     *
     * @param bool $respectRestrictedDepth
     */
    public function setRespectRestrictedDepth($respectRestrictedDepth = true)
    {
        $this->respectRestrictedDepth = $respectRestrictedDepth;
    }
}
