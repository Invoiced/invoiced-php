<?php

namespace Invoiced\Tests;

use Invoiced\Tests\Traits\AttachmentsTrait;
use Invoiced\Tests\Traits\CreateTrait;
use Invoiced\Tests\Traits\DeleteTrait;
use Invoiced\Tests\Traits\GetEndpointTrait;
use Invoiced\Tests\Traits\ListTrait;
use Invoiced\Tests\Traits\RetrieveTrait;
use Invoiced\Tests\Traits\SendTrait;
use Invoiced\Tests\Traits\UpdateTrait;
use Invoiced\Tests\Traits\VoidTrait;

class CreditNoteTest extends AbstractEndpointTestCase
{
    use GetEndpointTrait;
    use CreateTrait;
    use RetrieveTrait;
    use UpdateTrait;
    use DeleteTrait;
    use ListTrait;
    use VoidTrait;
    use SendTrait;
    use AttachmentsTrait;

    const OBJECT_CLASS = 'Invoiced\\CreditNote';
    const EXPECTED_ENDPOINT = '/credit_notes/123';
}
