<?php declare(strict_types = 1);

use FastyBird\AuthNode;
use Nette\Utils;

return [
	'messageDeviceDeleted' => [
		AuthNode\Constants::RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY,
		Utils\ArrayHash::from([
			'id'         => 'bf4cd870-2aac-45f0-a85e-e1cefd2d6d9a',
			'identifier' => 'second-device',
			'name'       => 'Name from message bus',
			'title'      => null,
			'comment'    => null,
			'state'      => 'ready',
			'enabled'    => true,
			'control'    => ['configure'],
			'params'     => [],
			'device'     => 'second-device',
			'parent'     => null,
		]),
	],
];
