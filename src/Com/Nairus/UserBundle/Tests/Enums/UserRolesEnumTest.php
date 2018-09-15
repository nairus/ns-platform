<?php

namespace Com\Nairus\UserBundle\Enums;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Test of UserRolesEnum class.
 *
 * @author nairus <nicolas.surian@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class UserRolesEnumTest extends KernelTestCase {

    /**
     * Test the constants.
     *
     * @return void
     */
    public function testConstants(): void {
        $this->assertSame("ROLE_ADMIN", UserRolesEnum::ADMIN, "1. The admin role expected is not ok.");
        $this->assertSame("ROLE_AUTHOR", UserRolesEnum::AUTHOR, "2. The author role expected is not ok.");
        $this->assertSame("IS_AUTHENTICATED_FULLY", UserRolesEnum::IS_AUTHENTICATED_FULLY, "3. The authenticated fully role expected is not ok.");
        $this->assertSame("IS_AUTHENTICATED_REMEMBERED", UserRolesEnum::IS_AUTHENTICATED_REMEMBERED, "4. The authenticated remembered role expected is not ok.");
        $this->assertSame("ROLE_MODERATOR", UserRolesEnum::MODERATOR, "5. The moderator role expected is not ok.");
        $this->assertSame("ROLE_SUPER_ADMIN", UserRolesEnum::SUPER_ADMIN, "6. The super admin role expected is not ok.");
        $this->assertSame("ROLE_USER", UserRolesEnum::USER, "7. The user role expected is not ok.");
    }

    /**
     * Test the "getRolesList" static method.
     *
     * @return void
     */
    public function testGetRolesList(): void {
        $this->assertCount(5, UserRolesEnum::getRolesList(), "1. The list doesn't contain the expected number of roles.");
        $this->assertArrayHasKey("ROLE_ADMIN", UserRolesEnum::getRolesList(), "2. The list doesn't contain the admin role.");
        $this->assertArrayHasKey("ROLE_AUTHOR", UserRolesEnum::getRolesList(), "3. The list doesn't contain the author role.");
        $this->assertArrayHasKey("ROLE_MODERATOR", UserRolesEnum::getRolesList(), "4. The list doesn't contain the moderator role.");
        $this->assertArrayHasKey("ROLE_SUPER_ADMIN", UserRolesEnum::getRolesList(), "5. The list doesn't contain the super admin role.");
        $this->assertArrayHasKey("ROLE_USER", UserRolesEnum::getRolesList(), "6. The list doesn't contain the user role.");
        $this->assertArrayNotHasKey("IS_AUTHENTICATED_FULLY", UserRolesEnum::getRolesList(), "7. The list hasn't to contain the authenticated fully role.");
        $this->assertArrayNotHasKey("IS_AUTHENTICATED_REMEMBERED", UserRolesEnum::getRolesList(), "8. The list hasn't to contain the authenticated remembered role.");
    }

}
