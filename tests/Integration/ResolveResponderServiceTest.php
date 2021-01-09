<?php

namespace Flugg\Responder\Tests\Integration;

use Flugg\Responder\Contracts\Responder as ResponderContract;
use Flugg\Responder\Facades\Responder as ResponderFacade;
use Flugg\Responder\Http\Builders\ErrorResponseBuilder;
use Flugg\Responder\Http\Builders\SuccessResponseBuilder;
use Flugg\Responder\MakesJsonResponses;
use Flugg\Responder\Responder;
use Flugg\Responder\Tests\IntegrationTestCase;

/**
 * Integration tests for testing different ways of resolving the [Responder] class.
 */
class ResolveResponderServiceTest extends IntegrationTestCase
{
    /**
     * Assert that you can get access to the service through the service container and dependency injection.
     */
    public function testResolveFromContainer()
    {
        $result = $this->app->make(ResponderContract::class);

        $this->assertInstanceOf(Responder::class, $result);
    }

    /**
     * Assert that you can get access to the service through a helper function.
     */
    public function testResolveFromHelperFunction()
    {
        $responder = $this->mock(ResponderContract::class);

        $result = responder();

        $this->assertSame($responder, $result);
    }

    /**
     * Assert that calls you make on the facade is forwarded to the service.
     */
    public function testResolveFromFacade()
    {
        $responder = $this->mock(ResponderContract::class);

        $result = ResponderFacade::getFacadeRoot();

        $this->assertSame($responder, $result);
    }

    /**
     * Assert that calls you make with the trait is forwarded to the service.
     */
    public function testResolveFromTrait()
    {
        $successResponseBuilder = $this->mock(SuccessResponseBuilder::class);
        $successResponseBuilder->allows('make')->andReturnSelf();
        $errorResponseBuilder = $this->mock(ErrorResponseBuilder::class);
        $errorResponseBuilder->allows('make')->andReturnSelf();
        $trait = $this->getMockForTrait(MakesJsonResponses::class);

        $successResult = $trait->success([]);
        $errorResult = $trait->error([]);

        $this->assertSame($successResponseBuilder, $successResult);
        $this->assertSame($errorResponseBuilder, $errorResult);
    }
}