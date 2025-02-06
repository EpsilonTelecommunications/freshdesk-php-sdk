<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\Resources\EmailConfig;
use Freshdesk\tests\TestCase;

/**
 * Email Config resource tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class EmailConfigTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = EmailConfig::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['all'],
            ['view'],
        ];
    }
}
