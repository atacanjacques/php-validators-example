<?php

namespace tests\examples;

use ValidatorsExample\examples\support\ValidationException;
use ValidatorsExample\examples\ZendExample;

class ZendExampleTest extends BaseTest
{

    public function runValidation(array $input = [])
    {
        try {
            return (new ZendExample())->exampleValidation($input);
        } catch (ValidationException $exception) {
            return false;
        }
    }

    public function testShouldFailWhenMissingRequiredFields()
    {
        $validator = new ZendExample();

        try {
            $validator->exampleValidation([]);
        } catch (ValidationException $ex) {
            $messages = $ex->getMessages();
            $this->assertArrayHasKey('dateField', $messages);
            $this->assertArrayHasKey('optionRequiredField', $messages);
        }
    }
}