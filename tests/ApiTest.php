<?php
namespace Freshdesk\tests;
use Freshdesk\Api;

/**
 * Api tests
 *
 * @package Freshdesk
 * @category Freshdesk
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class ApiTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->class = Api::class;
    }

    public static function methodsThatShouldExist(): array
    {
        return [
            ['request'],
        ];
    }

    /**
     * @dataProvider publicPropertiesThatShouldExist
     */
    public function testPublicPropertiesAreAccessible($property)
    {
        static::assertTrue(property_exists($this->class, $property));
    }

    public static function publicPropertiesThatShouldExist(): array
    {
        return [
            ['agents'],
            ['companies'],
            ['contacts'],
            ['groups'],
            ['tickets'],
            ['conversations'],
            ['categories'],
            ['forums'],
            ['topics'],
            ['comments'],
            ['emailConfigs'],
            ['products'],
            ['businessHours'],
            ['slaPolicies'],
        ];
    }
}
