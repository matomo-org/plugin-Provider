<?php

/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider;

use Piwik\Archive;
use Piwik\Piwik;
use Piwik\Plugin;

/**
 * Exposes reporting endpoints for visitor internet provider and ISP data.
 *
 * Reports group visits by resolved provider hostname and display label.
 *
 * @method static \Piwik\Plugins\Provider\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * Returns internet provider metrics for the requested site(s), period, and date.
     *
     * @param int|string|int[] $idSite Website ID(s) to query.
     *                                 - Single site ID (e.g. 1)
     *                                 - Multiple site IDs (e.g. [1, 4, 5])
     *                                 - Comma-separated list ("1,4,5") or "all"
     * @param 'day'|'week'|'month'|'year'|'range' $period The period to process, processes data for the period
     *                                                    containing the specified date.
     * @param string $date The date or date range to process.
     *                     'YYYY-MM-DD', magic keywords (today, yesterday, lastWeek, lastMonth, lastYear),
     *                     or date range (ie, 'YYYY-MM-DD,YYYY-MM-DD', lastX, previousX).
     * @param string|null|false $segment Custom segment to filter the report.
     *                                   Example: "referrerName==example.com"
     *                                   Supports AND (;) and OR (,) operators.
     * @return \Piwik\DataTable|\Piwik\DataTable\Map Provider metrics by internet provider label.
     */
    public function getProvider($idSite, $period, $date, $segment = false)
    {
        $dir = Plugin\Manager::getPluginDirectory('Provider');
        require_once $dir . '/functions.php';

        Piwik::checkUserHasViewAccess($idSite);
        $archive   = Archive::build($idSite, $period, $date, $segment);
        $dataTable = $archive->getDataTable(Archiver::PROVIDER_RECORD_NAME);
        $dataTable->filter('ColumnCallbackAddMetadata', ['label', 'url', __NAMESPACE__ . '\getHostnameUrl']);
        $dataTable->filter('GroupBy', ['label', __NAMESPACE__ . '\getPrettyProviderName']);
        $dataTable->filter('AddSegmentValue', [
            function ($label) {
                if ($label === Piwik::translate('General_Unknown')) {
                    return '';
                }

                return $label;
            },
        ]);
        $dataTable->queueFilter('ReplaceColumnNames');
        $dataTable->queueFilter('ReplaceSummaryRowLabel');
        return $dataTable;
    }
}
