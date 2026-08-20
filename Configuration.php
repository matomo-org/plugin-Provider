<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider;

use Piwik\Config;

class Configuration
{
    public const KEY_SKIP_REVERSE_DNS_LOOKUP = 'skip_reverse_dns_lookup';
    public const DEFAULT_SKIP_REVERSE_DNS_LOOKUP = 1;

    /**
     * Whether the DNS reverse lookup should be skipped when the configured location provider
     * doesn't return any ISP or organisation information for a visitor's IP.
     *
     * The lookup is a blocking, uncached DNS request within the tracking request, which can add
     * several seconds to it whenever no PTR record exists or the resolver is slow to answer, so
     * it is skipped by default. Set `skip_reverse_dns_lookup = 0` in the `[Provider]` section of
     * `config/config.ini.php` to perform the lookup anyway.
     *
     * @return bool
     */
    public function shouldSkipReverseDnsLookup()
    {
        $value = $this->getConfigValue(self::KEY_SKIP_REVERSE_DNS_LOOKUP, self::DEFAULT_SKIP_REVERSE_DNS_LOOKUP);

        if ($value === '' || $value === null) {
            $value = self::DEFAULT_SKIP_REVERSE_DNS_LOOKUP;
        }

        return (bool) $value;
    }

    private function getConfigValue($name, $default)
    {
        $providerConfig = Config::getInstance()->Provider;

        if (is_array($providerConfig) && array_key_exists($name, $providerConfig)) {
            return $providerConfig[$name];
        }

        return $default;
    }
}
