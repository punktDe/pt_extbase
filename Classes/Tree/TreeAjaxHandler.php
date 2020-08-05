<?php
declare(strict_types=1);

namespace PunktDe\PtExtbase\Tree;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PunktDe\PtExtbase\Controller\TreeController;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\SingletonInterface;

class TreeAjaxHandler implements SingletonInterface
{

    /**
     * @var TreeController
     */
    protected $treeController;

    /**
     * @param TreeController $treeController
     */
    public function injectTreeController(TreeController $treeController): void
    {
        $this->treeController = $treeController;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function determineAndRunAction(ServerRequestInterface $request): ResponseInterface
    {
        $params = $request->getQueryParams();
        $arguments = $params['arguments'];

        if ($params['controllerName'] !== 'Tree' || $params['pluginName'] !== 'ptx' || $params['extensionName'] !== 'PtExtbase') {
            return new NullResponse();
        }

        switch ($params['actionName']) {
            case 'getTree':
                $returnValue = $this->treeController->getTreeAction();
                break;
            case 'addNode':
                $returnValue = $this->treeController->addNodeAction($arguments['parent'], $arguments['label']);
                break;
            case 'moveNodeBefore':
                $returnValue = $this->treeController->moveNodeBeforeAction($arguments['node'], $arguments['targetNode']);
                break;
            case 'moveNodeAfter':
                $returnValue = $this->treeController->moveNodeAfterAction($arguments['node'], $arguments['targetNode']);
                break;
            case 'moveNodeInto':
                $returnValue = $this->treeController->moveNodeIntoAction($arguments['node'], $arguments['targetNode']);
                break;
            case 'saveNode':
                $returnValue = $this->treeController->saveNodeAction($arguments['node'], $arguments['label']);
                break;
            default:
                return new NullResponse();
        }

        return new JsonResponse($returnValue);
    }

}
