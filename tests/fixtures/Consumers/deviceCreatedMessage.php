<?php declare(strict_types = 1);

use FastyBird\AuthNode;
use Nette\Utils;

return [
	'messageDeviceCreated' => [
		AuthNode\Constants::RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY,
		Utils\ArrayHash::from([
			'id'         => '69786d15-fd0c-4d9f-9378-33287c2009fa',
			'identifier' => 'first-device',
			'name'       => 'Name from message bus',
			'title'      => null,
			'comment'    => null,
			'state'      => 'ready',
			'enabled'    => true,
			'control'    => ['configure'],
			'params'     => [],
			'device'     => 'first-device',
			'parent'     => null,
		]),
		'69786d15-fd0c-4d9f-9378-33287c2009fa',
	],
];
