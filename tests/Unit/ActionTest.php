<?php
/**
 * Copyright (c) Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 */

namespace Dbout\WpHook\Tests\Unit;

use Dbout\WpHook\Action;
use Dbout\WpHook\Enums\ActionType;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversMethod(Action::class, 'getDependencyKey')]
class ActionTest extends TestCase
{
    #[DataProvider('providerGetDependencyKey')]
    public function testGetDependencyKey(string $methodName, string $expectedKey): void
    {
        $action = new Action(
            name: 'test',
            priority: 1,
            acceptedArgs: 2,
            classInstance: new TestClass(),
            methodName: $methodName,
            dependencies: [],
            actionType: ActionType::Action,
        );

        $this->assertEquals($expectedKey, $action->getDependencyKey());
    }

    /**
     * @return \Generator
     */
    public static function providerGetDependencyKey(): \Generator
    {
        yield 'Should returns className only' => [
            '__invoke',
            TestClass::class,
        ];

        yield 'Should returns composed key' => [
            'execute',
            TestClass::class . '::execute',
        ];
    }

}

class TestClass
{
}
