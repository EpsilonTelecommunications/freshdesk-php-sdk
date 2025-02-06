<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\Resources\Forum;
use Freshdesk\Resources\Topic;
use Freshdesk\tests\TestCase;

/**
 * Topic resource tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class TopicTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = Topic::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['create'],
            ['all'],
            ['view'],
            ['update'],
            ['delete'],
            ['monitor'],
            ['unmonitor'],
            ['monitorStatus'],
        ];
    }
}
