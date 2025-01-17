<?php

namespace PenciPaywall\Payments\Stripe\Lib\Reporting;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;
use PenciPaywall\Payments\Stripe\Lib\Api_Operations;

/**
 * Class ReportType
 *
 * @property string $id
 * @property string $object
 * @property int $data_available_end
 * @property int $data_available_start
 * @property string[]|null $default_columns
 * @property string $name
 * @property int $updated
 * @property int $version
 *
 * @package Stripe\Reporting
 */
class Report_Type extends Api_Resource {

	const OBJECT_NAME = 'reporting.report_type';

	use Api_Operations\All;
	use Api_Operations\Retrieve;
}
