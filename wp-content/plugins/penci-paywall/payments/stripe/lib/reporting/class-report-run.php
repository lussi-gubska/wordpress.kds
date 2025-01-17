<?php

namespace PenciPaywall\Payments\Stripe\Lib\Reporting;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;
use PenciPaywall\Payments\Stripe\Lib\Api_Operations;

/**
 * Class ReportRun
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property string|null $error
 * @property bool $livemode
 * @property mixed $parameters
 * @property string $report_type
 * @property mixed|null $result
 * @property string $status
 * @property int|null $succeeded_at
 *
 * @package Stripe\Reporting
 */
class Report_Run extends Api_Resource
{
    const OBJECT_NAME = 'reporting.report_run';

    use Api_Operations\All;
    use Api_Operations\Create;
    use Api_Operations\Retrieve;
}
