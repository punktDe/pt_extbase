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
use PunktDe\PtExtbase\Utility\Wget\WgetLog;
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
  2015-01-26 17:50:43 URL:https://test.de/index.php?id=login&logintype=logout [6285/6285] -> "2014-11-28-0958/test.de/login/hdl/helpdesklogin/4092341/23589310/a9dc685506e98630/104" [1]
EOD;

	protected $testWgetLogEntry2 = <<<'EOD'
WARNUNG: Kann das Zertifikat von »das-partnerportal.deutschepost.de« nicht prüfen, ausgestellt von »»/C=DE/ST=Nordrhein-Westfalen/L=Bonn/O=Deutsche Post/CN=DPDHL TLS SHA2 CA I3««:.
  Ein selbst-signiertes Zertifikat gefunden.
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
  2015-01-26 17:50:43 URL:https://test.de/typo3temp/stylesheet_c89de9523c.1422025907.css [9169/9169] -> "2014-11-28-0958/test.de/typo3temp/stylesheet_c89de9523c.1422025907.css" [1]
EOD;

	/**
	 * @var array
	 */
	protected $testWgetLogEntrySplitted1 = array('status' => 'HTTP/1.1 200 OK',
		'body' => 'Server: Apache/2.4.6 (Red Hat)
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
  2015-01-26 17:50:43 URL:https://test.de/index.php?id=login&logintype=logout [6285/6285] -> "2014-11-28-0958/test.de/login/hdl/helpdesklogin/4092341/23589310/a9dc685506e98630/104" [1]
WARNUNG: Kann das Zertifikat von »das-partnerportal.deutschepost.de« nicht prüfen, ausgestellt von »»/C=DE/ST=Nordrhein-Westfalen/L=Bonn/O=Deutsche Post/CN=DPDHL TLS SHA2 CA I3««:.
  Ein selbst-signiertes Zertifikat gefunden.');


	/**
	 * @test
	 */
	public function splitLogFileEntries() {

		$logInput = $this->testWgetLogEntry1 . "\n" . $this->testWgetLogEntry2;
		$actualLogEntryArray = $this->wgetLogParser->_call('splitLogFileEntries', $logInput);

		$expectedLogEntryArray = array(
			0 => $this->testWgetLogEntrySplitted1,
			2 => array('status' => 'HTTP/1.1 200 OK',
						'body' =>'Server: Apache/2.4.6 (Red Hat)
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
  2015-01-26 17:50:43 URL:https://test.de/typo3temp/stylesheet_c89de9523c.1422025907.css [9169/9169] -> "2014-11-28-0958/test.de/typo3temp/stylesheet_c89de9523c.1422025907.css" [1]'
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
		$this->assertEquals('https://test.de/index.php?id=login&logintype=logout', $logEntryObject->getUrl());
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


	/**
	 * @test
	 * @throws \Tx_PtExtbase_Exception_Internal
	 */
	public function parseLogWithErrors() {
		$logOutPutFile = Files::concatenatePaths(array(__DIR__, 'TestData/WgetTest.log'));

		$wgetCommand = new \PunktDe\PtExtbase\Utility\Wget\WgetCommand();
		$wgetCommand->setNoVerbose(TRUE)->setOutputFile($logOutPutFile);

		$logFileEntries = $this->wgetLogParser->parseLog($wgetCommand);

		$this->assertCount(4, $logFileEntries);

		// Case 200

		$logFileEntry1 = array(
			'date' => date_create_from_format('Y-m-d H:i:s', '2015-02-26 11:50:40'),
			'url' => 'https://test.de/data-ok.html',
			'status' => 200,
			'length' => 3951
		);

		$this->assertEquals($logFileEntry1, $logFileEntries->getItemByIndex(0)->toArray());


		// Case 404

		$logFileEntry2 = array(
			'date' => date_create_from_format('Y-m-d H:i:s', '2015-02-26 11:50:57'),
			'url' => 'https://test.de/typo3conf/jquery.selectBox-arrow.gif',
			'status' => 404,
			'length' => 275
		);

		$this->assertEquals($logFileEntry2, $logFileEntries->getItemByIndex(1)->toArray());

		// Case 200 after 404

		$logFileEntry3 = array(
			'date' => date_create_from_format('Y-m-d H:i:s', '2015-02-26 11:50:40'),
			'url' => 'https://test.de/data-ok2.html',
			'status' => 200,
			'length' => 1000
		);

		$this->assertEquals($logFileEntry3, $logFileEntries->getItemByIndex(2)->toArray());

		// Case 404 on last line

		$logFileEntry4 = array(
			'date' => date_create_from_format('Y-m-d H:i:s', '2015-02-27 12:07:49'),
			'url' => 'http://partnerportal.dpppa.dev.punkt.de/typo3conf/ext/pt_dppp_base/Resources/Public/Styles/jquery.selectBox-arrow.gif',
			'status' => 404,
			'length' => 275
		);

		$this->assertEquals($logFileEntry4, $logFileEntries->getItemByIndex(3)->toArray());
	}


	/**
	 * @test
	 */
	public function buildLogFileEntryArrayTest() {
		$wgetLog = $this->wgetLogParser->_call('buildLogFileEntryArray', $this->testWgetLogEntry2); /** @var $wgetLog WgetLog */

		$expected = array(
			'date' => date_create_from_format('Y-m-d H:i:s', '2015-01-26 17:50:43'),
			'url' => 'https://test.de/typo3temp/stylesheet_c89de9523c.1422025907.css',
			'status' => 200,
			'length' => 360
		);

		$this->assertEquals(1, $wgetLog->count());
		$this->assertEquals($expected, $wgetLog->getItemByIndex(0)->toArray());
	}
}
