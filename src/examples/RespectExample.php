<?php

namespace ValidatorsExample\examples;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class RespectExample extends BaseExample
{
    public function exampleAssertion(array $input = [])
    {
        $validator = $this->createValidator();

//        // supports ->validate() or ->assert()
//        if (!$validator->validate($input)) {
//            // in real project log error, create error payload for end user
//            throw new \RuntimeException();
//        }

        try {
            $validator->assert($input);
        } catch (NestedValidationException $validationException) {
            // in real project log error, create error payload for end user
            print_r($validationException->getMessages());
            throw $validationException;
        }
        return true; // to make phpunit happy
    }

    private function createValidator()
    {
        $userService = $this->userService;

        return v::key('digitField', v::digit()->positive(), false)
            ->key('usernameField',
                v::stringType()
                    ->alnum('-_')
                    ->length(4, 255)
                    ->callback(function ($input) use ($userService) {
                        return $userService->isUserExists($input);
                    }),
                false
            )
            ->key('dateField', v::oneOf(v::date('Y-m-d G:i:s'), v::date('Y-m-d G:i')))
            ->key('booleanField', v::boolVal(), false)
            ->key('optionField', v::in(['person', 'deal']), false)
            ->key('optionRequiredField', v::in(['csv', 'xls']))
            ->when(
                v::key('optionRequiredField', v::equals('csv')), // if
                v::key('optionField', v::equals('person')), // then
                v::alwaysValid() // else
            )
            ->when(
                v::allOf(v::key('optionRequiredField', v::equals('csv')), v::key('optionField', v::equals('person'))), // if
                v::key('digitField', v::notOptional()), // then
                v::alwaysValid() // else
            )
            ->key('arrayOfDigits', v::arrayType()->each(v::digit()->positive()), false);
    }
}