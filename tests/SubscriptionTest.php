<?php

namespace Invoiced\Tests;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Invoiced\Client;
use Invoiced\Subscription;
use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\UpdateTrait;
use PHPUnit_Framework_TestCase;

class SubscriptionTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;

    const OBJECT_CLASS = 'Invoiced\\Subscription';
    const EXPECTED_ENDPOINT = '/subscriptions/123';

    /**
     * @return void
     */
    public function testPreview()
    {
        $client = $this->makeClient(new Response(200, [], '{"first_invoice":{"id":false},"mrr":9}'));
        $subscription = new Subscription($client, 123);
        $response = $subscription->preview();

        // we do not expect subscription object; just json
        $this->assertNotInstanceOf('Invoiced\\Subscription', $response);
        // but json should be formed correctly
        $this->assertEquals($response->mrr, 9); /* @phpstan-ignore-line */
    }
}
