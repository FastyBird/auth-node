<?php declare(strict_types = 1);

use FastyBird\AuthNode;

return [
	'messageDeviceCreated' => [
		AuthNode\Constants::RABBIT_MQ_DEVICES_CREATED_ENTITY_ROUTING_KEY,
		'{"id":"69786d15-fd0c-4d9f-9378-33287c2009fa","type":"network","identifier":"first-device","name":"Name from message bus","title":null,"comment":null,"state":"ready","enabled":true,"control":[],"device":"first-device","owner":"5e79efbf-bd0d-5b7c-46ef-bfbdefbfbd34","parent":null}',
		'69786d15-fd0c-4d9f-9378-33287c2009fa',
	],
];
