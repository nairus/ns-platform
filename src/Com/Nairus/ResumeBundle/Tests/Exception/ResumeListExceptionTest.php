<?php

namespace Com\Nairus\ResumeBundle\Exception;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2017-08-28 at 07:56:06.
 */
class ResumeListExceptionTest extends KernelTestCase {

    /**
     * @var ResumeListException
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ResumeListException(99);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }

    /**
     * @covers Com\Nairus\ResumeBundle\Exception\ResumeListException::getPage
     */
    public function testGetPage()
    {
        $this->assertSame(99, $this->object->getPage(), "La propriété [page] doit être correctement définit");
    }

    /**
     * @expectedException \TypeError
     */
    public function testWrongType()
    {
        new ResumeListException("foo");
    }

}