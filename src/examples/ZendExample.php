<?php

namespace ValidatorsExample\examples;

use Zend\Validator\Callback;
use Zend\Validator\Date;
use Zend\Validator\Digits;
use Zend\Validator\GreaterThan;
use Zend\Validator\InArray;
use Zend\Validator\NotEmpty;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;
use Zend\Validator\ValidatorChain;

class ZendExample extends BaseExample
{
    // NB: missing isset() checks everywhere
    public function exampleAssertion(array $input = [])
    {
        $userService = $this->userService;
        $errors = [];

        // digitField
        $digitFieldValidator = new ValidatorChain();
        $digitFieldValidator->attach(new Digits()); // needs extra library (zendframework/zend-filter) included to work
        $digitFieldValidator->attach(new GreaterThan(['min' => 0, 'inclusive' => true]));
        if (isset($input['digitField']) && !$digitFieldValidator->isValid($input['digitField'])) {
            $errors[] = $digitFieldValidator->getMessages();
        }

        // usernameField
        $usernameValidator = new ValidatorChain();
        $usernameValidator->attach(new Regex(['pattern' => '/^[a-zA-Z0-9-_]+$/'])); // can be replace with 'new Zend\I18n\Validator\Alnum()' when 'zend-i18n' library is included
        $usernameValidator->attach(new StringLength(['min' => 4, 'max' => 255]));
        $usernameValidator->attach(new Callback(['callback' => function ($input) use ($userService) {
            // business rule 1
            return $userService->isUserExists($input);
        }]));
        if (!$usernameValidator->isValid($input['usernameField'])) {
            $errors[] = $usernameValidator->getMessages();
        }

        // dateField
        $dateValidator = new ValidatorChain();
        $dateValidator->attach(new NotEmpty(NotEmpty::STRING));
        $dateFormatValidator = new Date(['format' => 'Y-m-d G:i:s']);
        $dateValidator->attach($dateFormatValidator);
        if (!$dateValidator->isValid($input['dateField'])) {
            $dateFormatValidator->setFormat('Y-m-d G:i');
            if (!$dateValidator->isValid($input['dateField'])) {
                $errors[] = $digitFieldValidator->getMessages();
            }
        }

        // booleanField - does not have validator for Boolean values. Must create own validator class or use filter_var to validate
        if (null === filter_var($input['booleanField'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
            $errors[] = 'booleanField must be boolean value';
        }

        // optionField
        $optionFieldValidator = new ValidatorChain();
        $optionFieldValidator->attach(new InArray(['haystack' => ['person', 'deal']]));
        $optionFieldValidator->attach(new Callback(['callback' => function ($value) use ($input) {
            // business rule 2 is bit hackish
            return $input['optionRequiredField'] !== 'csv' || ($input['optionRequiredField'] === 'csv' && $value === 'person');
        }]));
        $optionFieldValidator->attach(new Callback(['callback' => function ($value) use ($input) {
            // business rule 3 is bit hackish
            $prerequisites = $input['optionRequiredField'] === 'csv' && $value === 'person';
            return !$prerequisites || !empty($input['digitField']);
        }]));
        if (!$optionFieldValidator->isValid($input['optionField'])) {
            $errors[] = $optionFieldValidator->getMessages();
        }

        // optionRequiredField
        $optionRequiredFieldValidator = new ValidatorChain();
        $optionRequiredFieldValidator->attach(new NotEmpty(NotEmpty::STRING));
        $optionRequiredFieldValidator->attach(new InArray(['haystack' => ['csv', 'xls']]));
        if (!$optionRequiredFieldValidator->isValid($input['optionRequiredField'])) {
            $errors[] = $optionRequiredFieldValidator->getMessages();
        }

        // arrayOfDigits - does not have validator for Array values specific validator. Must create own validator class or foreach to validate
        $digitValidator = new ValidatorChain();
        $digitValidator->attach(new Digits()); // needs extra library (zendframework/zend-filter) included to work
        $digitValidator->attach(new GreaterThan(['min' => 0, 'inclusive' => true]));
        if (is_array($input['arrayOfDigits'])) {
            foreach ($input['arrayOfDigits'] as $digit) {
                if (!$digitValidator->isValid($digit)) {
                    $errors[] = $digitValidator->getMessages();
                    break; // first failure is good enough
                }
            }
        }

        if (!empty($errors)) {
            // validation failed. in real project log error, create error payload for end user
            return false;
        }
        return true; // so phpunit can assert
    }
}
