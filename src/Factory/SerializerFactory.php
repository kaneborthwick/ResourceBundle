<?php

declare (strict_types = 1);

namespace ResourceBundle\Factory;

use Hateoas\HateoasBuilder;
use Hateoas\UrlGenerator\CallableUrlGenerator;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class SerializerFactory {
	function __invoke(ContainerInterface $container, $requestedName) {

		$config = $container->get("config");

		$jmsConfig = isset($config["jms_serializer"]) ? $config["jms_serializer"] : [];
		$metadataConfig = isset($jmsConfig["metadata"]) ? $jmsConfig["metadata"] : [];

		$language = new ExpressionLanguage();
		$serializerBuilder = SerializerBuilder::create();
		$serializerBuilder->setExpressionEvaluator(new ExpressionEvaluator($language));

		foreach ($metadataConfig as $metadata) {
			$serializerBuilder->addMetadataDir($metadata["path"] ?? "", $metadata["namespace"] ?? "");
		}

		$a = HateoasBuilder::create($serializerBuilder)
			->setUrlGenerator(
				null, // By default all links uses the generator configured with the null name
				new CallableUrlGenerator(function ($route, array $parameters, $absolute) {
					return '';
				})
			)
		//->setCacheDir('C:\xampp\htdocs\retailer-out-post-server\data\cache\SerializerCache')
			->build();

		return $a;
	}
}
