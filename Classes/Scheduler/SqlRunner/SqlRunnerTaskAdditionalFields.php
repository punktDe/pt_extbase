<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
 *  Authors:
 *    Christian Herberger <herberger@punkt.de>,
 *    Ursula Klinger <klinger@punkt.de>,
 *    Daniel Lienert <lienert@punkt.de>,
 *    Joachim Mathes <mathes@punkt.de>
 *  All rights reserved
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
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * SQL Runner Task Additional Fields
 *
 * @package pt_extbase
 * @subpackage Scheduler
 */
class Tx_PtExtbase_Scheduler_SqlRunner_SqlRunnerTaskAdditionalFields implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $configuration = [
        'id' => 'tx_ptextbase_sqlfile',
        'label' => 'SQL file'
    ];

    /**
     * @var string
     */
    protected $fileExtensionList = 'sql,php';



    /**
     * Gets additional fields to render in the form to add/edit a task
     *
     * @param array $taskInfo Values of the fields from the add/edit task form
     * @param tx_scheduler_Task $task The task object being eddited. Null when adding a task!
     * @param tx_scheduler_Module $schedulerModule Reference to the scheduler backend module
     * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
     */
    public function getAdditionalFields(array &$taskInfo, $task,  \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule)
    {
        $configuration = $this->configuration;
        $additionalFields = [];

        $this->objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

        if (empty($taskInfo[$configuration['id']])) {
            $id = $configuration['id'];
            if ($schedulerModule->CMD == 'edit') {
                $taskInfo[$id] = $task->$id;
            } else {
                $taskInfo[$id] = '';
            }
        }

        $view = $this->getView();
        $view->assign('id', $configuration['id']);
        $view->assign('value', $taskInfo[$configuration['id']]);
        $view->assign('sqlFilePaths', $this->getSqlFilePaths());

        $additionalFields[$configuration['id']] = [
            'code'     => $view->render(),
            'label'    => $configuration['label']
        ];

        return $additionalFields;
    }



    /**
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected function getView()
    {
        $view = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView'); /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName('EXT:pt_extbase/Resources/Private/Templates/Scheduler/SqlRunner/TaskAdditionalFields.html'));
        $view->setPartialRootPath(GeneralUtility::getFileAbsFileName('EXT:pt_extbase/Resources/Private/Partials'));
        return $view;
    }



    /**
     * @return array
     */
    protected function getSqlFilePaths()
    {
        $extensions = $this->getLoadedExtensions();
        $sqlFilePaths = [];
        foreach ($extensions as $extension) {
            $sqlFilePaths = array_merge($sqlFilePaths, $this->getSqlFilePathsOfExtension($extension));
        }
        return $sqlFilePaths;
    }



    /**
     * @return array
     */
    protected function getLoadedExtensions()
    {
        $loadedExtensions = [];

        $listUtility = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\ListUtility'); /** @var $listUtility \TYPO3\CMS\Extensionmanager\Utility\ListUtility */
        $availableExtensions = $listUtility->getAvailableExtensions();
        $availableAndInstalledExtensions = $listUtility->getAvailableAndInstalledExtensions($availableExtensions);
        $enabledExtensions = array_keys($availableAndInstalledExtensions);

        foreach ($enabledExtensions as $enabledExtension) {
            if (ExtensionManagementUtility::isLoaded($enabledExtension)) {
                $loadedExtensions[] = $enabledExtension;
            }
        }
        return $loadedExtensions;
    }



    /**
     * @param string $extension
     * @return array
     */
    protected function getSqlFilePathsOfExtension($extension)
    {
        $pathNameShortCuts = [];
        $filePaths = $this->getFilePathsOfPath($extension, 'Resources/Private/Sql/');
        $filePaths = array_merge($filePaths, $this->getFilePathsOfPath($extension, 'Classes/Domain/SqlGenerator/'));
        foreach ($filePaths as $filePath) {
            $pathNameShortCut = $this->buildPathNameShortcut($extension, $filePath);
            $pathNameShortCuts[$pathNameShortCut] = $pathNameShortCut;
        }
        return $pathNameShortCuts;
    }



    /**
     * @param string $extension
     * @param string $path
     * @return array
     */
    protected function getFilePathsOfPath($extension, $path)
    {
        $path = ExtensionManagementUtility::extPath($extension, $path);
        return GeneralUtility::getAllFilesAndFoldersInPath([], $path, $this->fileExtensionList);
    }



    /**
     * @param $extension
     * @param $pathName
     * @return string
     */
    protected function buildPathNameShortcut($extension, $pathName)
    {
        return 'EXT:' . $extension . '/' . substr($pathName, strlen(ExtensionManagementUtility::extPath($extension)), strlen($pathName));
    }



    /**
     * Validates the additional fields' values
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param tx_scheduler_Module $schedulerModule Reference to the scheduler backend module
     * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
     */
    public function validateAdditionalFields(array &$submittedData,  \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $schedulerModule)
    {
        $submittedData[$this->configuration['id']] = trim($submittedData[$this->configuration['id']]);
        return true;
    }



    /**
     * Takes care of saving the additional fields' values in the task's object
     *
     * @param array $submittedData An array containing the data submitted by the add/edit task form
     * @param tx_scheduler_Task $task Reference to the scheduler backend module
     * @return void
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task)
    {
        $configuration = $this->configuration;

        $id = $configuration['id'];
        $task->$id = $submittedData[$id];
    }
}
