<?php

namespace tests\examples;

use ValidatorsExample\examples\RespectExample;
use ValidatorsExample\examples\support\ValidationException;

class RespectExamplesTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        $validator = new RespectExample();

        try {
            return $validator->exampleValidation($input);
        } catch (ValidationException $exception) {
            return false;
        }
    }

    public function testShouldFailWhenMissingRequiredFields()
    {
        $validator = new RespectExample();

        try {
            $validator->exampleValidation([]);
        } catch (ValidationException $ex) {
            $messages = $ex->getMessages(); // needs work to get messages as ['field' => 'message'] structure
            $this->assertContains('Key dateField must be present', $messages);
            $this->assertContains('Key optionRequiredField must be present', $messages);
        }
    }

}