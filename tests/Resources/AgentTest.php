<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\tests\TestCase;
use Freshdesk\Resources\Agent;

/**
 * Agent resource tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class AgentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->class = Agent::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['all'],
            ['view'],
            ['current'],
        ];
    }
}
