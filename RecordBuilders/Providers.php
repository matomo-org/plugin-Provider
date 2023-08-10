<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link    https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\Provider\RecordBuilders;

use Piwik\ArchiveProcessor;
use Piwik\ArchiveProcessor\Record;
use Piwik\ArchiveProcessor\RecordBuilder;
use Piwik\Config as PiwikConfig;
use Piwik\DataTable;
use Piwik\Metrics;
use Piwik\Plugins\Provider\Archiver;

class Providers extends RecordBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this->maxRowsInTable = PiwikConfig::getInstance()->General['datatable_archiving_maximum_rows_standard'];
    }

    public function getRecordMetadata(ArchiveProcessor $archiveProcessor): array
    {
        return [
            Record::make(Record::TYPE_BLOB, Archiver::PROVIDER_RECORD_NAME),
        ];
    }

    protected function aggregate(ArchiveProcessor $archiveProcessor): array
    {
        $record = new DataTable();

        $query = $archiveProcessor->getLogAggregator()->queryVisitsByDimension(['label' => Archiver::PROVIDER_FIELD]);
        while ($row = $query->fetch()) {
            $columns = [
                Metrics::INDEX_NB_UNIQ_VISITORS => $row[Metrics::INDEX_NB_UNIQ_VISITORS],
                Metrics::INDEX_NB_VISITS => $row[Metrics::INDEX_NB_VISITS],
                Metrics::INDEX_NB_ACTIONS => $row[Metrics::INDEX_NB_ACTIONS],
                Metrics::INDEX_NB_USERS => $row[Metrics::INDEX_NB_USERS],
                Metrics::INDEX_MAX_ACTIONS => $row[Metrics::INDEX_MAX_ACTIONS],
                Metrics::INDEX_SUM_VISIT_LENGTH => $row[Metrics::INDEX_SUM_VISIT_LENGTH],
                Metrics::INDEX_BOUNCE_COUNT => $row[Metrics::INDEX_BOUNCE_COUNT],
                Metrics::INDEX_NB_VISITS_CONVERTED => $row[Metrics::INDEX_NB_VISITS_CONVERTED],
            ];

            $record->sumRowWithLabel($row['label'], $columns);
        }

        return [Archiver::PROVIDER_RECORD_NAME => $record];
    }
}
