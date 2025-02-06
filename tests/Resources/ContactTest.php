<?php
namespace Freshdesk\tests\Resources;

use Freshdesk\tests\TestCase;
use Freshdesk\Resources\Contact;

/**
 * Contacts Api tests
 *
 * @author Matthew Clarkson <mpclarkson@gmail.com>
 */
class ContactTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->class = Contact::class;
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
            ['makeAgent'],
        ];
    }
}
