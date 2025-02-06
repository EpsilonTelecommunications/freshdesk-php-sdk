<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\tests\TestCase;
use Freshdesk\Resources\Group;

/**
 * Agent Api tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class GroupTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = Group::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['create'],
            ['all'],
            ['view'],
            ['update'],
            ['delete'],
        ];
    }
}
