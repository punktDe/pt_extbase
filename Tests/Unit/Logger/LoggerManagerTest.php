<?php

namespace PunktDe\PtExtbase\Tests\Unit\Logger;

/***************************************************************
 *  Copyright (C) 2014 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use PunktDe\PtExtbase\Logger\LoggerManager;

/**
 * Logger Manager Testcase
 */
class LoggerManagerTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var LoggerManager
     */
    protected $proxy;

    /**
     * @var string
     */
    protected $proxyClass;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->proxyClass = $this->buildAccessibleProxy(LoggerManager::class);
        $this->proxy = new $this->proxyClass();
    }



    /**
     * @return array
     */
    public function unifyComponentNameUnifiesComponentNameDataProvider()
    {
        return [
            'deprecatedClassName' => [
                'componentName' => 'Tx_Acme_Utility_ToonDrawer',
                'expected' => 'Tx.Acme.Utility.ToonDrawer'
            ],
            'namespacedClassName' => [
                'componentName' => 'Acme\\Utility\\ToonDrawer',
                'expected' => 'Acme.Utility.ToonDrawer'
            ],
            'dotSeparatedComponentName' => [
                'componentName' => 'acme.utility.toondrawer',
                'expected' => 'acme.utility.toondrawer'
            ],
            'componentNameWithoutSeparator' => [
                'componentName' => 'AcmeUtilityToonDrawer',
                'expected' => 'AcmeUtilityToonDrawer'
            ],
        ];
    }



    /**
     * @test
     * @dataProvider unifyComponentNameUnifiesComponentNameDataProvider
     *
     * @param string $componentName
     * @param string $expected
     */
    public function unifyComponentNameUnifiesComponentName($componentName, $expected)
    {
        $actual = $this->proxy->_call('unifyComponentName', $componentName);
        $this->assertSame($expected, $actual);
    }



    /**
     * @return array
     */
    public function evaluateIndexNameByComponentNameDataProvider()
    {
        return [
            'noConfigurationAvailable' => [
                'componentName' => 'Tx.Acme.Utility.ToonDrawer',
                'loggerConfigurationConfiguration' => [
                ],
                'expected' => 'PTEXTBASE'
            ],
            'onlyWriterConfigurationAvailable' => [
                'componentName' => 'Tx.Acme.Utility.ToonDrawer',
                'loggerConfigurationConfiguration' => [
                    'Tx' => [
                        'writerConfiguration' => [
                            'Duffy', 'Duck'
                        ]
                    ]
                ],
                'expected' => 'PTEXTBASE.Tx'
            ],
            'onlyProcessorConfigurationAvailable' => [
                'componentName' => 'Tx.Acme.Utility.ToonDrawer',
                'loggerConfiguration' => [
                    'Tx' => [
                        'processorConfiguration' => [
                            'Duffy', 'Duck'
                        ]
                    ]
                ],
                'expected' => 'PTEXTBASE.Tx'
            ],
            'levelTwoConfigurationAvailable' => [
                'componentName' => 'Tx.Acme.Utility.ToonDrawer',
                'loggerConfiguration' => [
                    'Tx' => [
                        'Acme' => [
                            'processorConfiguration' => [
                                'Duffy', 'Duck'
                            ]
                        ]
                    ]
                ],
                'expected' => 'PTEXTBASE.Tx.Acme'
            ],
            'maximumSpecificConfigurationAvailable' => [
                'componentName' => 'Tx.Acme.Utility.ToonDrawer',
                'loggerConfiguration' => [
                    'Tx' => [
                        'Acme' => [
                            'Utility' => [
                                'ToonDrawer' => [
                                    'processorConfiguration' => [
                                        'Duffy', 'Duck'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'expected' => 'PTEXTBASE.Tx.Acme.Utility.ToonDrawer'
            ],
            'writerAndProcessorWithConfigurationOfUnequalSpecificityAvailable' => [
                'componentName' => 'Tx.Acme.Utility.ToonDrawer',
                'loggerConfiguration' => [
                    'Tx' => [
                        'Acme' => [
                            'Utility' => [
                                'ToonDrawer' => [
                                    'processorConfiguration' => [
                                        'Duffy', 'Duck'
                                    ]
                                ]
                            ]
                        ],
                        'writerConfiguration' => [
                            'Bugs', 'Bunny'
                        ]
                    ]
                ],
                'expected' => 'PTEXTBASE.Tx.Acme.Utility.ToonDrawer'
            ]
        ];
    }



    /**
     * @test
     * @dataProvider evaluateIndexNameByComponentNameDataProvider
     *
     * @param string $componentName
     * @param array $loggerConfiguration
     * @param string $expected
     */
    public function evaluateIndexNameByComponentName($componentName, $loggerConfiguration, $expected)
    {
        $this->proxy->_set('loggerConfiguration', $loggerConfiguration);
        $actual = $this->proxy->_call('evaluateIndexNameByComponentName', $componentName);
        $this->assertSame($expected, $actual);
    }



    /**
     * @return array
     */
    public function getLoggerCreatesValidIndexOfLoggersAndReturnsLoggerWithCorrectComponentNameDataProvider()
    {
        return [
            [
                'loggerNames' => [
                    'Tx.Acme.Utility.ToonDrawer',
                    'Tx.Acme.Utility',
                    'Tx.Acme',
                    'Tx.Roadrunner',
                ],
                'loggerConfiguration' => [
                    'Tx' => [
                        'Acme' => [
                            'processorConfiguration' => [
                                'Duffy', 'Duck'
                            ]
                        ]
                    ]
                ],
                'expectedLoggerIndexKeys' => [
                    '',
                    'PTEXTBASE.Tx.Acme',
                    'PTEXTBASE.Tx',
                ]
            ],
        ];
    }



    /**
     * @test
     * @dataProvider getLoggerCreatesValidIndexOfLoggersAndReturnsLoggerWithCorrectComponentNameDataProvider
     *
     * @param array $loggerNames
     * @param array $loggerConfiguration
     * @param array $expectedLoggerIndexKeys
     */
    public function getLoggerCreatesValidIndexOfLoggersAndReturnsLoggerWithCorrectComponentName($loggerNames, $loggerConfiguration, $expectedLoggerIndexKeys)
    {
        $loggerManagerMock = $this->getMockBuilder($this->proxyClass)
            ->setMethods(['setWritersForLogger','setProcessorsForLogger'])
            ->getMock();

        $loggerManagerMock->_set('loggerConfiguration', $loggerConfiguration);

        foreach ($loggerNames as $loggerName) {
            $loggerManagerMock->getLogger($loggerName);
        }

        $actualLoggerIndexKeys = $loggerManagerMock->getLoggerNames();
        $this->assertSame($expectedLoggerIndexKeys, $actualLoggerIndexKeys, 'Expected and actual logger index keys are not equal');
    }
}
