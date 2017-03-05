<?php

namespace examples;


use PHPUnit\Framework\TestCase;
use Respect\Validation\Exceptions\NestedValidationException;
use ValidatorsExample\examples\RespectExample;

class RespectExamplesTest extends TestCase
{
    const INPUT_OK = [
        'digitField' => 1,
        'usernameField' => 'long_valid-username',
        'dateField' => '2017-03-05 11:12:13',
        'booleanField' => 'true',
        'optionField' => 'person',
        'optionRequiredField' => 'csv',
    ];
    const INPUT_WITHOUT_OPTIONALS_OK = [
        'dateField' => '2017-03-05 11:12',
        'optionRequiredField' => 'csv'
    ];

    public function testShouldPass() {
        $validator = new RespectExample();

        $this->assertTrue($validator->exampleAssertion(static::INPUT_OK));
    }

    public function testShouldNotPassBusiness2Rule() {
        $validator = new RespectExample();

        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['optionField' => 'deal']);

        $this->assertFalse($validator->exampleValidation($optionFieldNotCorrect));
    }

    public function testShouldNotPassBusiness3Rule() {
        $validator = new RespectExample();

        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['digitField' => null]);

        $this->assertFalse($validator->exampleValidation($optionFieldNotCorrect));
    }

    public function testShouldNotPassBusiness1Rule() {
        $validator = new RespectExample();

        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['usernameField' => 'short']);

        $this->assertFalse($validator->exampleValidation($optionFieldNotCorrect));
    }

    public function testShouldFailWhenMissingRequiredFields() {
        $validator = new RespectExample();

        try {
            $validator->exampleAssertion([]);
        } catch (NestedValidationException $ex) {
            $this->assertContains('Key dateField must be present', $ex->getMessages());
            $this->assertContains('Key optionRequiredField must be present', $ex->getMessages());
        }
    }

    public function testShouldValidateAsTrue() {
        $validator = new RespectExample();

        $this->assertTrue($validator->exampleValidation(static::INPUT_OK));
    }

}