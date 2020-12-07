<?php declare(strict_types = 1);

use FastyBird\AuthNode;

return [
	'messageDeviceDeleted' => [
		AuthNode\Constants::RABBIT_MQ_DEVICES_DELETED_ENTITY_ROUTING_KEY,
		'{"id":"bf4cd870-2aac-45f0-a85e-e1cefd2d6d9a","type":"network","identifier":"second-device","name":"Name from message bus","title":null,"comment":null,"state":"ready","enabled":true,"control":["configure"],"device":"second-device","owner":"5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34","parent":null}',
	],
];
