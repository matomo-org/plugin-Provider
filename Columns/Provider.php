<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider\Columns;

use Matomo\Network\IP;
use Piwik\Common;
use Piwik\Plugins\Provider\Provider as ProviderPlugin;
use Piwik\Plugins\UserCountry\LocationProvider;
use Piwik\Tracker\Action;
use Piwik\Tracker\Request;
use Piwik\Tracker\Visitor;

class Provider extends \Piwik\Plugins\UserCountry\Columns\Base
{
    protected $columnName = 'location_provider';
    protected $segmentName = 'provider';
    protected $category = 'UserCountry_VisitLocation';
    protected $nameSingular = 'Provider_ColumnProvider';
    protected $namePlural = 'Provider_WidgetProviders';
    protected $acceptValues = 'comcast.net, proxad.net, etc.';
    protected $type = self::TYPE_TEXT;

    /**
     * @param Request     $request
     * @param Visitor     $visitor
     * @param Action|null $action
     * @return mixed
     */
    public function onNewVisit(Request $request, Visitor $visitor, $action)
    {
        // if provider info has already been set, abort
        $locationValue = $visitor->getVisitorColumn('location_provider');
        if (!empty($locationValue)) {
            return false;
        }

        $userInfo = $this->getUserInfo($request, $visitor);

        $isp = $this->getLocationDetail($userInfo, LocationProvider::ISP_KEY);
        $org = $this->getLocationDetail($userInfo, LocationProvider::ORG_KEY);

        // if the location has provider/organization info, set it
        if (!empty($isp)) {
            $providerValue = $isp;

            // if the org is set and not the same as the isp, add it to the provider value
            if (!empty($org) && $org != $providerValue) {
                $providerValue .= ' - ' . $org;
            }

            return $providerValue;
        }

        if (!empty($org)) {
            return $org;
        }

        // Adding &dp=1 will disable the provider plugin, this is an "unofficial" parameter used to speed up log importer
        $disableProvider = $request->getParam('dp');

        if (!empty($disableProvider)) {
            return false;
        }

        $ip = $userInfo['ip'];

        // In case the IP was anonymized, we should not continue since the DNS reverse lookup will fail and this will slow down tracking
        if (substr($ip, -2, 2) == '.0') {
            Common::printDebug("IP Was anonymized so we skip the Provider DNS reverse lookup...");
            return false;
        }

        if (defined('PIWIK_TEST_MODE')) {
            return false; // skip reverse lookup while testing
        }

        $hostname          = $this->getHost($ip);
        $hostnameExtension = ProviderPlugin::getCleanHostname($hostname);

        // add the provider value in the table log_visit
        return substr($hostnameExtension, 0, 200);
    }

    public function getRequiredVisitFields()
    {
        return ['location_ip'];
    }

    /**
     * Returns the hostname given the IP address string
     *
     * @param string $ipStr IP Address
     * @return string hostname (or human-readable IP address)
     */
    private function getHost($ipStr)
    {
        $ip = IP::fromStringIP($ipStr);

        $host = $ip->getHostname();
        $host = ($host === null ? $ipStr : $host);

        return trim(strtolower($host));
    }
}
