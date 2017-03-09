<?php

namespace tests\examples;

use ValidatorsExample\examples\IlluminateExample;
use ValidatorsExample\examples\support\ValidationException;

class IlluminateExampleTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        try {
            return (new IlluminateExample())->exampleAssertion($input);
        } catch (ValidationException $exception) {
            return false;
        }
    }

    public function testShouldFailWhenMissingRequiredFields()
    {
        $validator = new IlluminateExample();

        try {
            $validator->exampleAssertion([]);
        } catch (ValidationException $ex) {
            $messages = $ex->getMessages();
            $this->assertArrayHasKey('dateField', $messages);
            $this->assertArrayHasKey('optionRequiredField', $messages);
        }
    }
}