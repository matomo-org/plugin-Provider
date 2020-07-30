<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\Provider\tests\System;

use Piwik\Tests\Fixtures\ManyVisitsWithMockLocationProvider;
use Piwik\Tests\Framework\TestCase\SystemTestCase;

/**
 * @group Provider
 * @group APITest
 * @group Plugins
 */
class APITest extends SystemTestCase
{
    /**
     * @var ManyVisitsWithMockLocationProvider
     */
    public static $fixture = null; // initialized below class definition

    /**
     * @dataProvider getApiForTesting
     */
    public function testApi($api, $params)
    {
        $this->runApiTests($api, $params);
    }

    public function getApiForTesting()
    {
        $api = [
            'Provider.getProvider',
        ];

        $apiToTest   = [];
        $apiToTest[] = [
            $api,
            [
                'idSite'     => 1,
                'date'       => self::$fixture->dateTime,
                'periods'    => ['day'],
                'testSuffix' => '',
            ],
        ];

        return $apiToTest;
    }

    public static function getOutputPrefix()
    {
        return '';
    }

    public static function getPathToTestDirectory()
    {
        return dirname(__FILE__);
    }

}

APITest::$fixture = new ManyVisitsWithMockLocationProvider();