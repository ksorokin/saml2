<?php

declare(strict_types=1);

namespace SAML2\Tests\Assertion\Validation\ConstraintValidator;

use SAML2\Assertion;
use SAML2\Assertion\Validation\ConstraintValidator\NotOnOrAfter;
use SAML2\Assertion\Validation\Result;
use SAML2\Tests\ControlledTimeTest;

/**
 * Because we're mocking a static call, we have to run it in separate processes so as to no contaminate the other
 * tests.
 *
 * @runTestsInSeparateProcesses
 */
class NotOnOrAfterTest extends ControlledTimeTest
{
    /**
     * @var \Mockery\MockInterface
     */
    private $assertion;

    public function setUp()
    {
        parent::setUp();
        $this->assertion = \Mockery::mock(Assertion::class);
    }

    /**
     * @group assertion-validation
     * @test
     */
    public function timestamp_in_the_past_before_graceperiod_is_not_valid()
    {
        $this->assertion->shouldReceive('getNotOnOrAfter')->andReturn($this->currentTime - 60);

        $validator = new NotOnOrAfter();
        $result    = new Result();

        $validator->validate($this->assertion, $result);

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    /**
     * @group assertion-validation
     * @test
     */
    public function time_within_graceperiod_is_valid()
    {
        $this->assertion->shouldReceive('getNotOnOrAfter')->andReturn($this->currentTime - 59);

        $validator = new NotOnOrAfter();
        $result    = new Result();

        $validator->validate($this->assertion, $result);

        $this->assertTrue($result->isValid());
    }

    /**
     * @group assertion-validation
     * @test
     */
    public function current_time_is_valid()
    {
        $this->assertion->shouldReceive('getNotOnOrAfter')->andReturn($this->currentTime);

        $validator = new NotOnOrAfter();
        $result    = new Result();

        $validator->validate($this->assertion, $result);

        $this->assertTrue($result->isValid());
    }
}
