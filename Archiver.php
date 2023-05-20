<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider;

class Archiver extends \Piwik\Plugin\Archiver
{
    const PROVIDER_RECORD_NAME = 'Provider_hostnameExt';
    const PROVIDER_FIELD = "location_provider";
}
