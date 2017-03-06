<?php

namespace tests\examples;

use Respect\Validation\Exceptions\NestedValidationException;
use ValidatorsExample\examples\RespectExample;

class RespectExamplesTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        $validator = new RespectExample();

        try {
            return $validator->exampleAssertion($input);
        } catch (NestedValidationException $exception) {
            return false;
        }
    }

    public function testShouldFailWhenMissingRequiredFields()
    {
        $validator = new RespectExample();

        try {
            $validator->exampleAssertion([]);
        } catch (NestedValidationException $ex) {
            $this->assertContains('Key dateField must be present', $ex->getMessages());
            $this->assertContains('Key optionRequiredField must be present', $ex->getMessages());
        }
    }

}