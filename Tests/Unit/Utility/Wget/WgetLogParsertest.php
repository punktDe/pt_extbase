<?php
 /***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
 *
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

namespace PunktDe\PtExtbase\Tests\Utility\Wget;

use PunktDe\PtExtbase\Utility\Files;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class WgetLogParserTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \PunktDe\PtExtbase\Utility\Wget\WgetLogParser
	 */
	protected $wgetLogParser;


	/**
	 * @var string
	 */
	protected $logOutPutFile;


	public function setUp() {
		$wgetLogParserProxyClass = $this->buildAccessibleProxy('\PunktDe\PtExtbase\Utility\Wget\WgetLogParser');
		$this->wgetLogParser = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager')->get($wgetLogParserProxyClass);

		$this->logOutPutFile = __DIR__ . '/testLogFile.log';
	}


	public function tearDown() {
		if(file_exists($this->logOutPutFile)) Files::unlink($this->logOutPutFile);
	}


	protected $testWgetLogEntry1 = <<<'EOD'
  HTTP/1.1 200 OK
  Server: Apache/2.4.6 (Red Hat)
  Last-Modified: Mon, 14 Apr 2014 13:44:20 GMT
  Vary: Accept-Encoding,User-Agent
  Cache-Control: max-age=86400
  Expires: Tue, 27 Jan 2015 16:46:42 GMT
  Content-Type: image/x-icon
  Content-Length: 1406
  Date: Mon, 26 Jan 2015 16:50:43 GMT
  X-Varnish: 938681105 938680885
  Age: 241
  Via: 1.1 varnish
  Connection: keep-alive
  2015-01-26 17:50:43 URL:https://test.das-partnerportal.deutschepost.de/index.php?id=login&logintype=logout [6285/6285] -> "2014-11-28-0958/test.das-partnerportal.deutschepost.de/login/hdl/helpdesklogin/4092341/23589310/a9dc685506e98630/104" [1]
EOD;

	protected $testWgetLogEntry2 = <<<'EOD'
  HTTP/1.1 200 OK
  Server: Apache/2.4.6 (Red Hat)
  Last-Modified: Fri, 09 Jan 2015 09:04:44 GMT
  Vary: Accept-Encoding,User-Agent
  Cache-Control: max-age=86400
  Expires: Tue, 27 Jan 2015 16:46:42 GMT
  Content-Type: text/css
  Content-Length: 0360
  Date: Mon, 26 Jan 2015 16:50:43 GMT
  X-Varnish: 938681107 938680887
  Age: 241
  Via: 1.1 varnish
  Connection: keep-alive
  2015-01-26 17:50:43 URL:https://test.das-partnerportal.deutschepost.de/typo3temp/stylesheet_c89de9523c.1422025907.css [9169/9169] -> "2014-11-28-0958/test.das-partnerportal.deutschepost.de/typo3temp/stylesheet_c89de9523c.1422025907.css" [1]
EOD;

	/**
	 * @var array
	 */
	protected $testWgetLogEntrySplitted1 = array('header' => '2015-01-26 17:50:43 URL:https://test.das-partnerportal.deutschepost.de/index.php?id=login&logintype=logout [6285/6285] -> "2014-11-28-0958/test.das-partnerportal.deutschepost.de/login/hdl/helpdesklogin/4092341/23589310/a9dc685506e98630/104" [1]',
		'body' => 'HTTP/1.1 200 OK
  Server: Apache/2.4.6 (Red Hat)
  Last-Modified: Mon, 14 Apr 2014 13:44:20 GMT
  Vary: Accept-Encoding,User-Agent
  Cache-Control: max-age=86400
  Expires: Tue, 27 Jan 2015 16:46:42 GMT
  Content-Type: image/x-icon
  Content-Length: 1406
  Date: Mon, 26 Jan 2015 16:50:43 GMT
  X-Varnish: 938681105 938680885
  Age: 241
  Via: 1.1 varnish
  Connection: keep-alive');


	/**
	 * @test
	 */
	public function splitLogFileEntries() {

		$logInput = $this->testWgetLogEntry1 . "\n" . $this->testWgetLogEntry2;
		$actualLogEntryArray = $this->wgetLogParser->_call('splitLogFileEntries', $logInput);

		$expectedLogEntryArray = array(
			0 => $this->testWgetLogEntrySplitted1,
			2 => array('header' => '2015-01-26 17:50:43 URL:https://test.das-partnerportal.deutschepost.de/typo3temp/stylesheet_c89de9523c.1422025907.css [9169/9169] -> "2014-11-28-0958/test.das-partnerportal.deutschepost.de/typo3temp/stylesheet_c89de9523c.1422025907.css" [1]',
						'body' =>'HTTP/1.1 200 OK
  Server: Apache/2.4.6 (Red Hat)
  Last-Modified: Fri, 09 Jan 2015 09:04:44 GMT
  Vary: Accept-Encoding,User-Agent
  Cache-Control: max-age=86400
  Expires: Tue, 27 Jan 2015 16:46:42 GMT
  Content-Type: text/css
  Content-Length: 0360
  Date: Mon, 26 Jan 2015 16:50:43 GMT
  X-Varnish: 938681107 938680887
  Age: 241
  Via: 1.1 varnish
  Connection: keep-alive'
	)
		);

		$this->assertEquals($expectedLogEntryArray[0], $actualLogEntryArray[0], 'First log entry differs');
		$this->assertEquals($expectedLogEntryArray[2], $actualLogEntryArray[2], 'Second log entry differs');
		$this->assertEquals($expectedLogEntryArray, $actualLogEntryArray);
	}


	/**
	 * @test
	 */
	public function buildLogFileEntry() {
		$logEntryObject = $this->wgetLogParser->_call('buildLogFileEntry', $this->testWgetLogEntrySplitted1); /** @var $logEntryObject \PunktDe\PtExtbase\Utility\Wget\WgetLogEntry */

		$this->assertInstanceOf('\PunktDe\PtExtbase\Utility\Wget\WgetLogEntry', $logEntryObject);

		$this->assertInstanceOf('\DateTime', $logEntryObject->getFetchDate());
		$this->assertEquals('2015-01-26 17:50:43', $logEntryObject->getFetchDate()->format('Y-m-d H:i:s'));
		$this->assertEquals('https://test.das-partnerportal.deutschepost.de/index.php?id=login&logintype=logout', $logEntryObject->getUrl());
		$this->assertEquals(200, $logEntryObject->getStatus());
		$this->assertEquals('image/x-icon', $logEntryObject->getContentType());
		$this->assertEquals(1406, $logEntryObject->getContentLength());
	}


	/**
	 * @test
	 */
	public function parseLog() {
		$wgetCommand = new \PunktDe\PtExtbase\Utility\Wget\WgetCommand();
		$wgetCommand->setNoVerbose(TRUE)->setOutputFile($this->logOutPutFile);

		file_put_contents($this->logOutPutFile,  $this->testWgetLogEntry1 . "\n" . $this->testWgetLogEntry2);

		$logFileEntries = $this->wgetLogParser->parseLog($wgetCommand);

		$this->assertCount(2, $logFileEntries);
		$this->assertInstanceOf('\PunktDe\PtExtbase\Utility\Wget\WgetLogEntry', $logFileEntries[0]);
		$this->assertInstanceOf('\PunktDe\PtExtbase\Utility\Wget\WgetLogEntry', $logFileEntries[1]);
	}
}
