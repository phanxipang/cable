<?php

declare(strict_types=1);

namespace Fansipan\Cable\Tests;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Container\ContainerInterface;

abstract class TestCase extends BaseTestCase
{
    protected function createContainer(): ContainerInterface
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(__DIR__.'/services.php');

        return $builder->build();
    }
}
