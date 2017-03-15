<?php

namespace ValidatorsExample\examples;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use ValidatorsExample\examples\support\BaseExample;
use ValidatorsExample\examples\support\ValidationException;

/** version 1.1.2 requires
        php: >=5.4
        symfony/polyfill-mbstring: ^1.2
 */
class RespectExample extends BaseExample
{
    public function exampleValidation(array $input = [])
    {
        $userService = $this->userService;

        $validator = v::key('digitField', v::digit()->positive(), false)
            ->key('usernameField',
                v::stringType()
                    ->alnum('-_')
                    ->length(4, 255)
                    ->callback(function ($input) use ($userService) { // business rule 1
                        return $userService->isUserExists($input);
                    }),
                false
            )
            ->key('dateField', v::oneOf(v::date('Y-m-d G:i:s'), v::date('Y-m-d G:i')))
            ->key('booleanField', v::boolVal(), false)
            ->key('optionField', v::in(['person', 'deal']), false)
            ->key('optionRequiredField', v::in(['csv', 'xls']))
            ->when( // business rule 2
                v::key('optionRequiredField', v::equals('csv')), // if
                v::key('optionField', v::equals('person')), // then
                v::alwaysValid() // else
            )
            ->when( // business rule 3
                v::allOf(v::key('optionRequiredField', v::equals('csv')), v::key('optionField', v::equals('person'))), // if
                v::key('digitField', v::notOptional()), // then
                v::alwaysValid() // else
            )
            ->key('arrayOfDigits', v::arrayType()->each(v::digit()->positive()), false)
            ->key('arrayOfObjects', v::arrayType()
                ->each(
                    v::key('digitField', v::digit()->positive(), false)
                        ->key('optionRequiredField', v::in(['csv', 'xls']))
                )
            );

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
            throw new ValidationException($validationException->getMessages());
        }
        return true; // to make phpunit happy
    }
}