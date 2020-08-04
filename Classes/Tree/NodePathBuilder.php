<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

class NodePathBuilder
{
    /**
     * @var <array>NodePathBuilder
     */
    protected static $instances = [];

    /**
     * @var Tree
     */
    protected $tree;


    /**
     * @var array
     */
    protected $nodePathCache = [];


    /**
     * @static
     * @param $repository
     * @param $nameSpace
     * @return NodePathBuilder
     */
    public static function getInstanceByRepositoryAndNamespace($repository, $nameSpace)
    {
        if (!self::$instances[$repository . $nameSpace]) {
            $instance = new NodePathBuilder();
            $instance->setTree($instance->buildTree($repository, $nameSpace));
            self::$instances[$repository . $nameSpace] = $instance;
        }

        return self::$instances[$repository . $nameSpace];
    }


    public function __construct()
    {
    }


    /**
     * @param $repository
     * @param $nameSpace
     * @return Tree
     */
    public function buildTree($repository, $nameSpace)
    {
        $treeRepositoryBuilder = TreeRepositoryBuilder::getInstance();
        $treeRepositoryBuilder->setNodeRepositoryClassName($repository);
        $treeRepository = $treeRepositoryBuilder->buildTreeRepository();

        return $treeRepository->loadTreeByNamespace($nameSpace);
    }


    /**
     * @param integer $nodeUid
     * @param integer $startIndex
     * @param integer $length
     * @return array nodes as plain array
     */
    public function getPathFromRootToNode(int $nodeUid, int $startIndex = 0, int $length = 1000): array
    {

        $pathFromRootNode = $this->buildPathFromNodeToRoot($nodeUid);
        if (count($pathFromRootNode) === 0) {
            return [];
        }
        $reversedArray = array_reverse($pathFromRootNode);
        return array_slice($reversedArray, $startIndex, $length);
    }


    /**
     * @param integer $nodeUid
     * @param integer $startIndex
     * @param integer $length
     * @return array nodes as plain array
     */
    public function getPathFromNodeToRoot(int $nodeUid, int $startIndex = 0, int $length = 1000): array
    {

        $pathFromRootNode = $this->buildPathFromNodeToRoot($nodeUid);
        if (count($pathFromRootNode) === 0) {
            return [];
        }
        return array_slice($pathFromRootNode, $startIndex, $length);
    }


    /**
     * @param integer $nodeUid
     * @return array
     */
    protected function buildPathFromNodeToRoot(int $nodeUid): array
    {
        if (!array_key_exists($nodeUid, $this->nodePathCache)) {
            $node = $this->tree->getNodeByUid($nodeUid);

            $pathFromNodeToRoot = [];

            if ($node instanceof Node) {
                $pathFromNodeToRoot[] = $node;

                while ($node != $this->tree->getRoot()) {
                    $node = $node->getParent();
                    $pathFromNodeToRoot[] = $node;
                }

                $this->nodePathCache[$nodeUid] = $pathFromNodeToRoot;
            } else {
                $this->nodePathCache[$nodeUid] = [];
            }
        }

        return $this->nodePathCache[$nodeUid];
    }


    /**
     * @param Tree $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }
}
