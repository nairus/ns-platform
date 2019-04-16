<?php

namespace Com\Nairus\ResumeBundle\Repository;

use Com\Nairus\CoreBundle\Dto\ImageConfigDto;
use Com\Nairus\CoreBundle\Listener\ImageEntityListener;
use Com\Nairus\CoreBundle\Manager\ImageManagerInterface;
use Com\Nairus\CoreBundle\Tests\AbstractKernelTestCase;
use Com\Nairus\ResumeBundle\NSResumeBundle;
use Com\Nairus\ResumeBundle\Entity\Profile;
use Com\Nairus\ResumeBundle\Tests\DataFixtures\Unit\LoadProfile;
use Com\Nairus\UserBundle\NSUserBundle;
use Com\Nairus\UserBundle\Entity\User;
use phpmock\MockBuilder;

/**
 * Test de la classe ProfileRepository.
 *
 * @author Nicolas Surian <nicolas.surian@gmail.com>
 */
class ProfileRepositoryTest extends AbstractKernelTestCase {

    /**
     * @var ProfileRepository
     */
    private static $repository;

    /**
     * Gestionnaire des utilisateur.
     *
     * @var \Com\Nairus\UserBundle\Repository\UserRepository
     */
    private static $userRepository;

    /**
     * Current user entity.
     *
     * @var User
     */
    private static $author;

    /**
     * Load traits to manipulate test datas.
     */
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasLoaderTrait;
    use \Com\Nairus\CoreBundle\Tests\Traits\DatasCleanerTrait;

    /**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        static::$repository = static::$em->getRepository(NSResumeBundle::NAME . ":Profile");
        static::$userRepository = static::$em->getRepository(User::class);
        static::$author = static::$userRepository->findOneByUsername("author");
    }

    /**
     * Test entities insert, update and delete.
     */
    public function testInsertUpdateAndDelete() {
        // Test d'insertion.
        /* @var $user User */
        $user = static::$userRepository->findOneByUsername("sadmin");
        $newProfile = new Profile();
        $newProfile
                ->setAddress("Adresse 4")
                ->setAddressAddition("Adresse 5")
                ->setCell("06.02.02.02.02")
                ->setCity("Istres")
                ->setCountry("France")
                ->setFirstName("Prénom")
                ->setPhone("04.02.01.01.01")
                ->setLastName("Nom")
                ->setZip("13800")
                ->setUser($user);
        static::$em->persist($newProfile);
        static::$em->flush();
        static::$em->clear();

        $profiles = static::$repository->findAll();
        $this->assertCount(2, $profiles, "1.1. Il doit y avoir 2 entité en base.");
        /* @var $profile Profile */
        $profile = $profiles[1];
        $this->assertSame("Adresse 4", $profile->getAddress(), "1.2. Le champ [address] doit être identique.");
        $this->assertSame("Adresse 5", $profile->getAddressAddition(), "1.3. Le champ [addressAddition] doit être identique.");
        $this->assertSame("06.02.02.02.02", $profile->getCell(), "1.4. Le champ [cell] doit être identique.");
        $this->assertSame("Istres", $profile->getCity(), "1.5. Le champ [city] doit être identique.");
        $this->assertSame("France", $profile->getCountry(), "1.6. Le champ [country] doit être identique.");
        $this->assertSame("04.02.01.01.01", $profile->getPhone(), "1.7. Le champ [phone] doit être identique.");
        $this->assertSame("13800", $profile->getZip(), "1.8. Le champ [zip] doit être identique.");
        $this->assertSame($user->getId(), $profile->getUser()->getId(), "1.9. Le champ [user] doit être identique.");
        $this->assertSame("Prénom", $profile->getFirstName(), "1.10. Le champ [firstName] doit être identique.");
        $this->assertSame("Nom", $profile->getLastName(), "1.11. Le champ [lastName] doit être identique.");

        // Update test.
        $profile
                ->setAddress("Adresse 6")
                ->setAddressAddition("Adresse 7")
                ->setCell("07.02.02.02.02")
                ->setCity("Marseille")
                ->setCountry("USA")
                ->setFirstName("Prénom2")
                ->setPhone("04.03.01.01.01")
                ->setLastName("Nom2")
                ->setZip("13004");
        static::$em->flush();
        static::$em->clear();
        /* @var $profileUpdated Profile */
        $profileUpdated = static::$repository->find($profile->getId());
        $this->assertSame("04.03.01.01.01", $profileUpdated->getPhone(), "2.1. Le champ [phone] doit être identique.");
        $this->assertSame("Adresse 6", $profileUpdated->getAddress(), "2.2. Le champ [address] doit être identique.");
        $this->assertSame("Adresse 7", $profileUpdated->getAddressAddition(), "2.3. Le champ [addressAddition] doit être identique.");
        $this->assertSame("07.02.02.02.02", $profileUpdated->getCell(), "2.4. Le champ [cell] doit être identique.");
        $this->assertSame("Marseille", $profileUpdated->getCity(), "2.5. Le champ [city] doit être identique.");
        $this->assertSame("USA", $profileUpdated->getCountry(), "2.6. Le champ [country] doit être identique.");
        $this->assertSame("13004", $profileUpdated->getZip(), "2.7. Le champ [zip] doit être identique.");
        $this->assertSame("Prénom2", $profile->getFirstName(), "2.8. Le champ [firstName] doit être identique.");
        $this->assertSame("Nom2", $profile->getLastName(), "2.9. Le champ [lastName] doit être identique.");

        // Delete test.
        $id = $profileUpdated->getId();
        static::$em->remove($profileUpdated);
        static::$em->flush();
        static::$em->clear();
        /* @var $profileDeleted Profile */
        $profileDeleted = static::$repository->find($id);
        $this->assertNull($profileDeleted, "3.1. L'entité doit être supprimée.");
    }

    /**
     * Insert test without foreign key.
     *
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     */
    public function testInsertWithoutUser() {
        $profile = new Profile();
        $profile
                ->setAddress("Adresse 1")
                ->setAddressAddition("Adresse 2")
                ->setCell("06.01.01.01.01")
                ->setCity("Marseille")
                ->setCountry("France")
                ->setFirstName("Prénom")
                ->setPhone("04.01.01.01.01")
                ->setLastName("Nom")
                ->setZip("13004");
        static::$em->persist($profile);
        static::$em->flush();
    }

    /**
     * Test the <code>getByUserWithAvatar</code>.
     *
     * @return void
     */
    public function testGetWithAvatarForUser(): void {
        try {
            // Set the mock for the image manager.
            $DS = DIRECTORY_SEPARATOR;
            $uploadBaseDir = static::$container->getParameter('kernel.project_dir') . $DS . "var";
            $relativeBaseDir = $DS . "tests" . $DS . "image_manager" . $DS;
            $imageConfigDto = new ImageConfigDto();
            $imageConfigDto->setBaseUploadDir($uploadBaseDir)
                    ->setCrop(false)
                    ->setRelativeBaseDir($relativeBaseDir)
                    ->setSrcHeight(100)
                    ->setSrcWidth(100)
                    ->setThbHeight(50)
                    ->setThbWidth(50);

            $mockImageManager = $this->getMockBuilder(ImageManagerInterface::class)
                    ->disableOriginalConstructor()
                    ->setMethods(["getExtensionFromMimeType", "buildRelativePath", "getExtraFolders", "getConfig", "resize", "crop"])
                    ->getMock();

            $mockImageManager
                    ->expects($this->any())
                    ->method("getExtensionFromMimeType")
                    ->willReturn("png");

            $mockImageManager
                    ->expects($this->any())
                    ->method("getExtraFolders")
                    ->willReturn("0" . $DS . "1" . $DS);

            $mockImageManager
                    ->expects($this->any())
                    ->method("getConfig")
                    ->willReturn($imageConfigDto);

            $mockImageManager
                    ->expects($this->any())
                    ->method("resize")
                    ->willReturn(true);

            $mockImageManager
                    ->expects($this->any())
                    ->method("crop")
                    ->willReturn(true);

            /* @var $imageEntityListener ImageEntityListener */
            $imageEntityListener = static::$container->get("ns_core.image_entity_listener");

            // Set the mock in the app container.
            $imageEntityListener->setImageManager($mockImageManager);

            // Create the builtin php functions mock to enable.
            $reflectionClass = new \ReflectionClass(ImageEntityListener::class);
            $builder = new MockBuilder();
            $builder->setNamespace($reflectionClass->getNamespaceName())
                    ->setName("unlink")
                    ->setFunction(function (string $filename, $context = null): bool {
                        return true;
                    })
                    ->build()
                    ->enable();

            $builder->setName("mkdir")
                    ->setFunction(function (string $pathname, int $mode = 0777, bool $recursive = false, $context = null): bool {
                        return true;
                    })
                    ->build()
                    ->enable();

            // load datas.
            $this->loadDatas(static::$em, [new LoadProfile()]);

            $user = static::$em->getRepository(NSUserBundle::NAME . ":User")->findOneByUsername("sadmin");
            /* @var $profile Profile */
            $profile = static::$repository->getWithAvatarForUser($user);

            $this->assertNotNull($profile->getAvatar(), "1. The avatar has to be fetch with the profile.");
        } catch (\Exception | \Error $exc) {
            $this->fail("No exception expected: " . $exc->getMessage());
        } finally {
            // clean the datas
            static::cleanDatasAfterTest(static::$container, [new LoadProfile()]);

            // Disable all mocks.
            \phpmock\Mock::disableAll();
        }
    }

    /**
     * Test the <code>getByUserWithAvatar</code> with <code>NoResultException</code>.
     *
     * @expectedException \Doctrine\ORM\NoResultException
     */
    public function testGetByUserWithAvatarNoResult() {
        $user = static::$userRepository->findOneByUsername("user");
        static::$repository->getWithAvatarForUser($user);
    }

}
