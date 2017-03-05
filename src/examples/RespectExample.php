<?php

namespace ValidatorsExample\examples;

use Respect\Validation\Exceptions\ValidationException;
use Respect\Validation\Validator as v;

class RespectExample
{
    private $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function exampleValidation(array $input = [])
    {
        $validator = $this->createValidator();

        if (!$validator->validate($input)) {
            // in real project log error, create error payload for end user
            return false;
        }
        return true;
    }

    private function createValidator()
    {
        $userService = $this->userService;

        return v::key('digitField', v::positive(), false)
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
                v::key('optionRequiredField', v::equals('csv')),
                v::key('optionField', v::equals('person'))
            )
            ->when(
                v::allOf(v::key('optionRequiredField', v::equals('csv')), v::key('optionField', v::equals('person'))),
                v::key('digitField', v::notOptional())
            );
    }

    public function exampleAssertion(array $input = [])
    {
        $validator = $this->createValidator();

        try {
            $validator->assert($input);
        } catch (ValidationException $validationException) {
            // in real project log error, create error payload for end user
            print_r($validationException->getMessages());
            throw $validationException;
        }
        return true; // to make phpunit happy
    }

}