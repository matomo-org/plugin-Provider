<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Config;
use Piwik\Plugins\Provider\Configuration;

/**
 * @group Provider
 * @group ConfigurationTest
 * @group Plugins
 */
class ConfigurationTest extends TestCase
{
    /**
     * @var mixed
     */
    private $originalConfig;

    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConfig = Config::getInstance()->Provider;
        $this->configuration  = new Configuration();
    }

    protected function tearDown(): void
    {
        Config::getInstance()->Provider = $this->originalConfig;

        parent::tearDown();
    }

    public function testShouldSkipReverseDnsLookupIsEnabledByDefault()
    {
        Config::getInstance()->Provider = [];

        $this->assertTrue($this->configuration->shouldSkipReverseDnsLookup());
    }

    /**
     * @dataProvider getConfigValuesToTest
     */
    public function testShouldSkipReverseDnsLookupRespectsConfiguredValue($configuredValue, $expected)
    {
        Config::getInstance()->Provider = [
            Configuration::KEY_SKIP_REVERSE_DNS_LOOKUP => $configuredValue,
        ];

        $this->assertSame($expected, $this->configuration->shouldSkipReverseDnsLookup());
    }

    public function getConfigValuesToTest()
    {
        return [
            'disabled as int'      => [0, false],
            'disabled as string'   => ['0', false],
            'disabled as bool'     => [false, false],
            'enabled as int'       => [1, true],
            'enabled as string'    => ['1', true],
            'enabled as bool'      => [true, true],
            'empty value defaults' => ['', true],
            'null value defaults'  => [null, true],
        ];
    }
}
