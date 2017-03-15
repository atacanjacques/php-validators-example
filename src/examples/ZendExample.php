<?php

namespace ValidatorsExample\examples;

use ValidatorsExample\examples\support\BaseExample;
use ValidatorsExample\examples\support\ValidationException;
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
    public function exampleValidation(array $input = [])
    {
        $userService = $this->userService;
        $errors = [];

        // digitField
        $digitField = new ValidatorChain();
        $digitField->attach(new Digits()); // needs extra library (zendframework/zend-filter) included to work
        $digitField->attach(new GreaterThan(['min' => 0, 'inclusive' => true]));
        if (isset($input['digitField']) && !$digitField->isValid($input['digitField'])) {
            $errors['digitField'] = $digitField->getMessages();
        }

        // usernameField
        $username = new ValidatorChain();
        $username->attach(new Regex(['pattern' => '/^[a-zA-Z0-9-_]+$/'])); // can be replace with 'new Zend\I18n\Validator\Alnum()' when 'zend-i18n' library is included
        $username->attach(new StringLength(['min' => 4, 'max' => 255]));
        $username->attach(new Callback(['callback' => function ($input) use ($userService) {
            // business rule 1
            return $userService->isUserExists($input);
        }]));
        if (isset($input['usernameField']) && !$username->isValid($input['usernameField'])) {
            $errors['usernameField'] = $username->getMessages();
        }

        // dateField
        $date = new ValidatorChain();
        $date->attach(new NotEmpty(NotEmpty::STRING));
        $dateFormat = new Date(['format' => 'Y-m-d G:i:s']);
        $date->attach($dateFormat);
        if (!$date->isValid($input['dateField'] ?? null)) {
            $dateFormat->setFormat('Y-m-d G:i');
            if (!$date->isValid($input['dateField'] ?? null)) {
                $errors['dateField'] = $date->getMessages();
            }
        }

        // booleanField - does not have validator for Boolean values. Must create own validator class or use filter_var to validate
        if (isset($input['booleanField']) && null === filter_var($input['booleanField'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
            $errors[] = 'booleanField must be boolean value';
        }

        // optionField
        $optionField = new ValidatorChain();
        $optionField->attach(new InArray(['haystack' => ['person', 'deal']]));
        $optionField->attach(new Callback(['callback' => function ($value) use ($input) {
            // business rule 2 is bit hackish
            return $input['optionRequiredField'] !== 'csv' || ($input['optionRequiredField'] === 'csv' && $value === 'person');
        }]));
        $optionField->attach(new Callback(['callback' => function ($value) use ($input) {
            // business rule 3 is bit hackish
            $prerequisites = $input['optionRequiredField'] === 'csv' && $value === 'person';
            return !$prerequisites || !empty($input['digitField']);
        }]));
        if (isset($input['optionField']) && !$optionField->isValid($input['optionField'])) {
            $errors['optionField'] = $optionField->getMessages();
        }

        // optionRequiredField
        $optionRequiredField = new ValidatorChain();
        $optionRequiredField->attach(new NotEmpty(NotEmpty::STRING));
        $optionRequiredField->attach(new InArray(['haystack' => ['csv', 'xls']]));
        if (!$optionRequiredField->isValid($input['optionRequiredField'] ?? null)) {
            $errors['optionRequiredField'] = $optionRequiredField->getMessages();
        }

        // arrayOfDigits - does not have validator for Array values specific validator. Must create own validator class or foreach to validate
        $digitValidator = new ValidatorChain();
        $digitValidator->attach(new Digits()); // needs extra library (zendframework/zend-filter) included to work
        $digitValidator->attach(new GreaterThan(['min' => 0, 'inclusive' => true]));
        if (isset($input['arrayOfDigits']) && is_array($input['arrayOfDigits'])) {
            foreach ($input['arrayOfDigits'] as $digit) {
                if (!$digitValidator->isValid($digit)) {
                    $errors['arrayOfDigits'] = $digitValidator->getMessages();
                    break; // first failure is good enough
                }
            }
        }

        // arrayOfObjects - does not have validator for Array values specific validator. Must create own validator class or foreach to validate
        $digitValidator2 = new ValidatorChain();
        $digitValidator2->attach(new Digits());
        $digitValidator2->attach(new GreaterThan(['min' => 0, 'inclusive' => true]));

        $optionRequiredField2 = new ValidatorChain();
        $optionRequiredField2->attach(new NotEmpty(NotEmpty::STRING));
        $optionRequiredField2->attach(new InArray(['haystack' => ['csv', 'xls']]));
        if (isset($input['arrayOfObjects']) && is_array($input['arrayOfObjects'])) {
            foreach ($input['arrayOfObjects'] as $object) {
                if (!$optionRequiredField2->isValid($object['optionRequiredField'] ?? null)) {
                    $errors['optionRequiredField'] = $optionRequiredField2->getMessages();
                    break; // first failure is good enough
                }

                if (!$digitValidator2->isValid($object['digitField'] ?? null)) {
                    $errors['arrayOfDigits'] = $digitValidator2->getMessages();
                    break; // first failure is good enough
                }
            }
        }

        if (!empty($errors)) {
            // validation failed. in real project log error, create error payload for end user
            print_r($errors);
            throw new ValidationException($errors);
        }
        return true; // so phpunit can assert
    }
}
