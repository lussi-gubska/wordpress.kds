<?php

namespace PenciPaywall\Payments\Stripe\Lib\Sigma;

use PenciPaywall\Payments\Stripe\Lib\Api_Resource;
use PenciPaywall\Payments\Stripe\Lib\Api_Operations;

/**
 * Class ScheduledQueryRun
 *
 * @property string $id
 * @property string $object
 * @property int $created
 * @property int $data_load_time
 * @property mixed $error
 * @property \Stripe\File|null $file
 * @property bool $livemode
 * @property int $result_available_until
 * @property string $sql
 * @property string $status
 * @property string $title
 *
 * @package Stripe\Sigma
 */
class Scheduled_Query_Run extends Api_Resource {

	const OBJECT_NAME = 'scheduled_query_run';

	use Api_Operations\All;
	use Api_Operations\Retrieve;

	public static function classUrl() {
		 return '/v1/sigma/scheduled_query_runs';
	}
}
