<?php

declare(strict_types=1);

namespace SAML2\Tests\Response\Validation\ConstraintValidator;

use SAML2\Constants;
use SAML2\Response;
use SAML2\Response\Validation\Result;
use SAML2\Response\Validation\ConstraintValidator\IsSuccessful;

class IsSuccessfulTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var \Mockery\MockInterface
     */
    private $response;

    public function setUp()
    {
        $this->response = \Mockery::mock(Response::class);
    }

    /**
     * @group response-validation
     * @test
     */
    public function validating_a_successful_response_gives_a_valid_validation_result()
    {
        $this->response->shouldReceive('isSuccess')->once()->andReturn(true);

        $validator = new IsSuccessful();
        $result    = new Result();

        $validator->validate($this->response, $result);

        $this->assertTrue($result->isValid());
    }

    /**
     * @group response-validation
     * @test
     */
    public function an_unsuccessful_response_is_not_valid_and_generates_a_proper_error_message()
    {
        $responseStatus = [
            'Code'    => 'foo',
            'SubCode' => Constants::STATUS_PREFIX . 'bar',
            'Message' => 'this is a test message'
        ];
        $this->response->shouldReceive('isSuccess')->once()->andReturn(false);
        $this->response->shouldReceive('getStatus')->once()->andReturn($responseStatus);

        $validator = new IsSuccessful();
        $result    = new Result();

        $validator->validate($this->response, $result);
        $errors = $result->getErrors();

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $errors);
        $this->assertEquals('foo/bar this is a test message', $errors[0]);
    }
}
