<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\Resources\Comment;
use Freshdesk\tests\TestCase;

/**
 * Topic resource tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class CommentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = Comment::class;
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
