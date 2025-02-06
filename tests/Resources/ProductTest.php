<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\Resources\Product;
use Freshdesk\tests\TestCase;

/**
 * Product resource tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class ProductTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = Product::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['all'],
            ['view'],
        ];
    }
}
