<?php
namespace PunktDe\PtExtbase\Tree;

/*
 *  (c) 2020 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

class TreeRepositoryBuilder
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * Holds singleton instance of TreeRepositoryBuilder
     *
     * @var TreeRepositoryBuilder
     */
    private static $instance = null;


    /**
     * Holds class name of node repository
     *
     * @var string
     */
    protected $nodeRepositoryClassName = NodeRepository::class;


    /**
     * Holds class name for tree storage
     *
     * @var string
     */
    protected $treeStorageClassName = NestedSetTreeStorage::class;


    /**
     * Holds class name for tree builder
     *
     * @var string class name for tree builder
     */
    protected $treeBuilderClassName = TreeBuilder::class;


    /**
     * Holds restricted depth of tree to be build
     *
     * @var integer
     */
    protected $restrictedDepth = -1;


    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }


    /**
     * Returns singleton instance of this class
     *
     * @return TreeRepositoryBuilder
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            self::$instance = $objectManager->get(TreeRepositoryBuilder::class);
        }
        return self::$instance;
    }


    /**
     * return TreeRepositoryBuilder
     */
    public function __construct()
    {
    }


    /**
     * Returns instance of tree repository
     *
     * @return TreeRepository
     */
    public function buildTreeRepository()
    {
        $nodeRepository = $this->buildNodeRepository();

        $treeBuilder = $this->buildTreeBuilder($nodeRepository);
        $treeBuilder->setRestrictedDepth($this->restrictedDepth);

        $treeStorage = $this->buildTreeStorage($nodeRepository);

        return $this->objectManager->get(TreeRepository::class, $nodeRepository, $treeBuilder, $treeStorage);
    }


    /**
     * Setter for node repository class name
     *
     * @param $nodeRepositoryClassName
     */
    public function setNodeRepositoryClassName($nodeRepositoryClassName)
    {
        $this->nodeRepositoryClassName = $nodeRepositoryClassName;
    }


    /**
     * Setter for tree storage classe name
     *
     * @param $treeStorageClassName
     */
    public function setTreeStorageClassName($treeStorageClassName)
    {
        $this->treeStorageClassName = $treeStorageClassName;
    }


    /**
     * Setter for tree builder class name
     *
     * @param $treeBuilderClassName
     */
    public function setTreeBuilderClassName($treeBuilderClassName)
    {
        $this->treeBuilderClassName = $treeBuilderClassName;
    }


    /**
     * Setter for restricted depth
     *
     * @param $restrictedDepth
     */
    public function setRestrictedDepth($restrictedDepth)
    {
        $this->restrictedDepth = $restrictedDepth;
    }


    /**
     * Returns instance of node repository for class name set in builder
     *
     * @return NodeRepositoryInterface Instance of node repository
     * @throws \Exception
     */
    protected function buildNodeRepository()
    {
        if (!$this->nodeRepositoryClassName) {
            throw new \Exception('No Repository Class Name given.', 1369732947);
        }
        if (!class_exists($this->nodeRepositoryClassName)) {
            throw new \Exception('The given class ' . $this->nodeRepositoryClassName . ' does not exist!', 1328287190);
        }

        $nodeRepository = $this->objectManager->get($this->nodeRepositoryClassName);

        if (!is_a($nodeRepository, NodeRepositoryInterface::class)) {
            throw new \Exception('Given class name ' . $this->nodeRepositoryClassName . ' must implement NodeRepositoryInterface!', 1328201591);
        }
        return $nodeRepository;
    }


    /**
     * Returns instance of tree builder for class name set in builder
     *
     * @param NodeRepositoryInterface $nodeRepository
     * @throws \Exception
     * @return TreeBuilderInterface
     */
    protected function buildTreeBuilder(NodeRepositoryInterface $nodeRepository)
    {
        $treeBuilder = new $this->treeBuilderClassName($nodeRepository);

        if (!is_a($treeBuilder, TreeBuilderInterface::class)) {
            throw new \Exception('Given class name ' . $this->treeBuilderClassName . ' must implement TreeBuilderInterface!', 1328201592);
        }
        /* @var $treeBuilder TreeBuilderInterface */

        $treeBuilder->setRespectRestrictedDepth(true);
        $treeBuilder->setRestrictedDepth($this->restrictedDepth);

        return $treeBuilder;
    }


    /**
     * Returns instance of tree storage for class name set in builder
     *
     * @param NodeRepositoryInterface $nodeRepository
     * @throws \Exception
     * @return TreeStorageInterface
     */
    protected function buildTreeStorage(NodeRepositoryInterface $nodeRepository)
    {
        $treeStorage = new $this->treeStorageClassName($nodeRepository);
        if (!is_a($treeStorage, TreeStorageInterface::class)) {
            throw new \Exception('Given class name ' . $this->treeStorageClassName . ' does not implement TreeStorageInterface!', 1328201593);
        }
        return $treeStorage;
    }
}
