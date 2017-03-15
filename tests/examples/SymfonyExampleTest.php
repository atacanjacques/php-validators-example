<?php

namespace tests\examples;


use ValidatorsExample\examples\support\ValidationException;
use ValidatorsExample\examples\SymfonyExample;

class SymfonyExampleTest extends BaseTest
{
    public function runValidation(array $input = [])
    {
        try {
            return (new SymfonyExample())->exampleValidation($input);
        } catch (ValidationException $exception) {
            return false;
        }
    }

    public function testShouldFailWhenMissingRequiredFields()
    {
        $validator = new SymfonyExample();

        try {
            $validator->exampleValidation([]);
        } catch (ValidationException $ex) {
            // needs a bit work to get errors in simpler format
            $errors = [];
            foreach ($ex->getMessages() as $message) {
                $errors[str_replace(['[', ']'], '', $message->getPropertyPath())] = $message->getMessage();
            }
            $this->assertArrayHasKey('dateField', $errors);
            $this->assertArrayHasKey('optionRequiredField', $errors);
        }
    }
}