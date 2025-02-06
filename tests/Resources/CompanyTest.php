<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\tests\TestCase;
use Freshdesk\Resources\Company;


/**
 * Company resource tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class CompanyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = Company::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['create'],
            ['all'],
            ['view'],
            ['update'],
            ['delete'],
            ['fields'],
        ];
    }
}
