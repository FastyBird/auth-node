# FastyBird auth node

[![Build Status](https://img.shields.io/travis/FastyBird/auth-node.svg?style=flat-square)](https://travis-ci.org/FastyBird/auth-node)
[![Code coverage](https://img.shields.io/coveralls/FastyBird/auth-node.svg?style=flat-square)](https://coveralls.io/r/FastyBird/auth-node)
![PHP from Travis config](https://img.shields.io/travis/php-v/fastybird/auth-node?style=flat-square)
[![Licence](https://img.shields.io/packagist/l/FastyBird/auth-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/auth-node)
[![Downloads total](https://img.shields.io/packagist/dt/FastyBird/auth-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/auth-node)
[![Latest stable](https://img.shields.io/packagist/v/FastyBird/auth-node.svg?style=flat-square)](https://packagist.org/packages/FastyBird/auth-node)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

## What is FastyBird auth node?

Auth node is a microservice for managing application accounts & sessions.

FastyBird auth node is an [Apache2](https://github.com/fastybird/auth-node/blob/master/license.md) licensed distributed authentication microservice, developed in PHP with [Nette framework](https://nette.org).

## Requirements

FastyBird auth node is tested against PHP 7.4 and [ReactPHP http](https://github.com/reactphp/http) 0.8 event-driven, streaming plaintext HTTP server and [RabbitMQ](https://www.rabbitmq.com/) 3.7 message broker

## Getting started

> **NOTE:** If you don't want to install it manually, try [docker image](#install-with-docker)

The best way to install **fastybird/auth-node** is using [Composer](https://getcomposer.org/). If you don't have Composer yet, [download it](https://getcomposer.org/download/) following the instructions.
Then use command:

```sh
$ composer create-project --no-dev fastybird/auth-node path/to/install
$ cd path/to/install
```

Everything required will be then installed in the provided folder `path/to/install`

This microservice has several console command.

##### HTTP server

```sh
$ vendor/bin/fb-console fb:node:server:start
```

This command is to start build-in web server which is listening for incoming http api request messages from clients and is listening for new data from exchange bus from other microservices. 

## Install with docker

![Docker Image Version (latest by date)](https://img.shields.io/docker/v/fastybird/auth-node?style=flat-square)
![Docker Image Size (latest by date)](https://img.shields.io/docker/image-size/fastybird/auth-node?style=flat-square)
![Docker Cloud Build Status](https://img.shields.io/docker/cloud/build/fastybird/auth-node?style=flat-square)

Docker image: [fastybird/auth-node](https://hub.docker.com/r/fastybird/auth-node/)

### Use docker hub image

```bash
$ docker run -d -it --name auth fastybird/auth-node:latest
```

### Generate local image

```bash
$ docker build --tag=auth-node .
$ docker run -d -it --name auth-node auth-node
```

## Configuration

This microservice is preconfigured for default connections, but your infrastructure could be different.

Configuration could be made via environment variables:

| Environment Variable | Description |
| ---------------------- | ---------------------------- |
| `FB_NODE_PARAMETER__EXCHANGE_HOST=127.0.0.1` | RabbitMQ host address |
| `FB_NODE_PARAMETER__EXCHANGE_PORT=5672` | RabbitMQ access port |
| `FB_NODE_PARAMETER__EXCHANGE_VHOST=/` | RabbitMQ vhost |
| `FB_NODE_PARAMETER__EXCHANGE_USERNAME=guest` | Username |
| `FB_NODE_PARAMETER__EXCHANGE_PASSWORD=guest` | Password |
| | |
| `FB_NODE_PARAMETER__DATABASE_VERSION=5.7` | MySQL server version |
| `FB_NODE_PARAMETER__DATABASE_HOST=127.0.0.1` | MySQL host address |
| `FB_NODE_PARAMETER__DATABASE_PORT=3306` | MySQL access port |
| `FB_NODE_PARAMETER__DATABASE_DBNAME=auth_node` | MySQL database name |
| `FB_NODE_PARAMETER__DATABASE_USERNAME=root` | Username |
| `FB_NODE_PARAMETER__DATABASE_PASSWORD=` | Password |
| | |
| `FB_NODE_PARAMETER__SERVER_ADDRESS=0.0.0.0` | HTTP server host address |
| `FB_NODE_PARAMETER__SERVER_PORT=8000` | HTTP server access port |
| | |
| `FB_NODE_PARAMETER__SECURITY_SIGNATURE=` | Security token signature string |

> **NOTE:** In case you are not using docker image or you are not able to configure environment variables, you could edit configuration file `./config/default.neon`

## Initialization

This microservice is using database, and need some initial data to be inserted into it. This could be done via shell command:

```sh
$ vendor/bin/fb-console fb:initialize
```

This console command is interactive and will ask for all required information.

After this steps, microservice could be started with [server command](#http-server)

## Feedback

Use the [issue tracker](https://github.com/FastyBird/auth-node/issues) for bugs or [mail](mailto:code@fastybird.com) or [Tweet](https://twitter.com/fastybird) us for any idea that can improve the project.

Thank you for testing, reporting and contributing.

## Changelog

For release info check [release page](https://github.com/FastyBird/auth-node/releases)

## Maintainers

<table>
	<tbody>
		<tr>
			<td align="center">
				<a href="https://github.com/akadlec">
					<img width="80" height="80" src="https://avatars3.githubusercontent.com/u/1866672?s=460&amp;v=4">
				</a>
				<br>
				<a href="https://github.com/akadlec">Adam Kadlec</a>
			</td>
		</tr>
	</tbody>
</table>

***
Homepage [https://fastybird.com](https://fastybird.com) and repository [https://github.com/fastybird/auth-node](https://github.com/fastybird/auth-node).
