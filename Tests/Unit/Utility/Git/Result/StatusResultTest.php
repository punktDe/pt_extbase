<?php
namespace PunktDe\PtExtbase\Tests\Unit\Utility\Git;

/***************************************************************
 *  Copyright (C)  punkt.de GmbH
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

/**
 * Status Result Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Unit\Utility\Git
 */
class StatusResultTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Git\Result\StatusResult
     */
    protected $proxy;

    
    /**
     * @return void
     */
    public function setUp(): void
    {
        $proxyClass = $this->buildAccessibleProxy('PunktDe\PtExtbase\Utility\Git\Result\StatusResult');
        $this->proxy = $this->getMockBuilder($proxyClass)
            ->setMethods(['foo'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->proxy->_set('logger', $this->getSimpleMock(\PunktDe\PtExtbase\Logger\Logger::class));
    }



    /**
     * @return array
     */
    public function buildResultBuildsValidResultDataProvider()
    {
        return [
            'ModifiedDeletedUntracked' => [
                'rawResult' => " M file01.txt\n D file02.txt\n?? file03.txt\n",
                'parsedResult' => [
                    [
                        'indexStatus' => '',
                        'worktreeStatus' => 'M',
                        'path' => 'file01.txt',
                        'correspondingPath' => ''
                    ],
                    [
                        'indexStatus' => '',
                        'worktreeStatus' => 'D',
                        'path' => 'file02.txt',
                        'correspondingPath' => ''
                    ],
                    [
                        'indexStatus' => '?',
                        'worktreeStatus' => '?',
                        'path' => 'file03.txt',
                        'correspondingPath' => ''
                    ],
                ],
            ],
            'Rename' => [
                'rawResult' => "R  file01.txt -> file02.txt",
                'parsedResult' => [
                    [
                        'indexStatus' => 'R',
                        'worktreeStatus' => '',
                        'path' => 'file01.txt',
                        'correspondingPath' => 'file02.txt'
                    ]
                ]
            ],
            'RenameFilesWithWhitespace' => [
                'rawResult' => "R  \"file 01.txt\" -> \"file 02.txt\"\nR  file03.txt -> \"file 04.txt\"\nR  \"file 05.txt\" -> file06.txt",
                'parsedResult' => [
                    [
                        'indexStatus' => 'R',
                        'worktreeStatus' => '',
                        'path' => 'file 01.txt',
                        'correspondingPath' => 'file 02.txt'
                    ],
                    [
                        'indexStatus' => 'R',
                        'worktreeStatus' => '',
                        'path' => 'file03.txt',
                        'correspondingPath' => 'file 04.txt'
                    ],
                    [
                        'indexStatus' => 'R',
                        'worktreeStatus' => '',
                        'path' => 'file 05.txt',
                        'correspondingPath' => 'file06.txt'
                    ]
                ],
            ],
            'DirectorySeperatorInFileName' => [
                'rawResult' => "R  Data/file01.txt -> Data/file02.txt",
                'parsedResult' => [
                    [
                        'indexStatus' => 'R',
                        'worktreeStatus' => '',
                        'path' => 'Data/file01.txt',
                        'correspondingPath' => 'Data/file02.txt'
                    ]
                ]
            ],
        ];
    }



    /**
     * @test
     * @dataProvider buildResultBuildsValidResultDataProvider
     *
     * @param string $rawResult
     * @param array $parsedResult
     */
    public function buildResultBuildsValidResult($rawResult, $parsedResult)
    {
        $this->markTestSkipped('Functionaltest');
        $statusCommandMock = $this->getMockBuilder('PunktDe\PtExtbase\Utility\Git\Command\StatusCommand')
            ->setMethods(['isShort'])
            ->getMock();
        $statusCommandMock->expects($this->once())
            ->method('isShort')
            ->will($this->returnValue(true));

        $this->proxy->_set('result', new \TYPO3\CMS\Extbase\Persistence\ObjectStorage());
        $this->proxy->_set('command', $statusCommandMock);
        $this->proxy->_set('objectManager', $this->objectManager);
        $this->proxy->_set('rawResult', $rawResult);

        $parsedResultIndex = 0;
        $result = $this->proxy->getResult();

        $this->assertCount(count($parsedResult), $result);
        foreach ($result as $pathStatus) { /** @var \PunktDe\PtExtbase\Utility\Git\Result\PathStatus $pathStatus */
            $this->assertSame($parsedResult[$parsedResultIndex]['indexStatus'], $pathStatus->getIndexStatus());
            $this->assertSame($parsedResult[$parsedResultIndex]['worktreeStatus'], $pathStatus->getWorkTreeStatus());
            $this->assertSame($parsedResult[$parsedResultIndex]['path'], $pathStatus->getPath());
            $this->assertSame($parsedResult[$parsedResultIndex]['correspondingPath'], $pathStatus->getCorrespondingPath());
            $parsedResultIndex++;
        }
    }



    /**
     * @return array
     */
    public function resultCanBeConvertedToArrayDataProvider()
    {
        return [
            'ModifiedDeletedUntracked' => [
                'rawResult' => " M file01.txt\n D file02.txt\n?? file03.txt\n",
                'expected' => [
                    [
                        'indexStatus' => '',
                        'worktreeStatus' => 'M',
                        'path' => 'file01.txt',
                        'correspondingPath' => ''
                    ],
                    [
                        'indexStatus' => '',
                        'worktreeStatus' => 'D',
                        'path' => 'file02.txt',
                        'correspondingPath' => ''
                    ],
                    [
                        'indexStatus' => '?',
                        'worktreeStatus' => '?',
                        'path' => 'file03.txt',
                        'correspondingPath' => ''
                    ],
                ],
            ],
        ];
    }



    /**
     * @test
     * @dataProvider resultCanBeConvertedToArrayDataProvider
     *
     * @param string $rawResult
     * @param array $expected
     */
    public function resultCanBeConvertedToArray($rawResult, $expected)
    {
        $this->markTestSkipped('Functionaltest');
        $statusCommandMock = $this->getMockBuilder('PunktDe\PtExtbase\Utility\Git\Command\StatusCommand')
            ->setMethods(['isShort'])
            ->getMock();
        $statusCommandMock->expects($this->once())
            ->method('isShort')
            ->will($this->returnValue(true));

        $this->proxy->_set('result', $this->objectManager->get('PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\ResultObjectStorage'));
        $this->proxy->_set('command', $statusCommandMock);
        $this->proxy->_set('objectManager', $this->objectManager);
        $this->proxy->_set('rawResult', $rawResult);

        $result = $this->proxy->getResult();
        $result = $result->toArray();

        $parsedResultIndex = 0;
        foreach ($result as $actual) { /** @var \PunktDe\PtExtbase\Utility\Git\Result\PathStatus $pathStatus */
            $this->assertSame($expected[$parsedResultIndex]['indexStatus'], $actual[0]);
            $this->assertSame($expected[$parsedResultIndex]['worktreeStatus'], $actual[1]);
            $this->assertSame($expected[$parsedResultIndex]['path'], $actual[2]);
            $this->assertSame($expected[$parsedResultIndex]['correspondingPath'], $actual[3]);
            $parsedResultIndex++;
        }
    }
}
