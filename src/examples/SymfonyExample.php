<?php

namespace ValidatorsExample\examples;

use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Validation;

class SymfonyExample extends BaseExample
{
    public function exampleAssertion(array $input = [])
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        // business rule 1
        $businessRule1 = function ($object, ExecutionContextInterface $context, $payload) {
            if (!$this->userService->isUserExists($object)) {
                $context->buildViolation('This username exists!')
                    ->atPath('usernameField')
                    ->addViolation();
            }
        };

        // business rule 2
        $businessRule2 = function ($object, ExecutionContextInterface $context, $payload) {
            if ('person' !== $object && $context->getRoot()['optionRequiredField'] === 'csv') {
                $context->buildViolation('if \'optionRequiredField\' value is \'csv\', optionField value can be only \'person\'')
                    ->atPath('optionField')
                    ->addViolation();
            }
        };

        // business rule 3
        $businessRule3 = function ($object, ExecutionContextInterface $context, $payload) {
            if (empty($object) && $context->getRoot()['optionRequiredField'] === 'csv' && $context->getRoot()['optionField'] === 'person') {
                $context->buildViolation('digitField is required if \'optionRequiredField\' = \'csv\' and \'optionField\' = \'person\', otherwise optional')
                    ->atPath('digitField')
                    ->addViolation();
            }
        };

        // custom multi date format validator as default 'DateFormat' supports only single format and there is no way to have 'one of'
        $multiDateFormatValidator = function ($object, ExecutionContextInterface $context, $payload) {
            $dateFormats = ['Y-m-d G:i:s', 'Y-m-d G:i'];
            foreach ($dateFormats as $format) {
                $date = \DateTime::createFromFormat($format, $object);
                if ($date && $date->format($format) === $object) {
                    return;
                }
            }
            $context->buildViolation('dateField must have one of the following formats ' . implode(',', $dateFormats))
                ->atPath('dateField')
                ->addViolation();
        };

        if (!isset($input['digitField'])) {
            // fields must exist in array as keys even with optional() validator. 'testDigitField' unsets field so re-add it to pass
            $input['digitField'] = null;
        }

        $collectionConstraint = new Collection([
            'digitField' => [
                new Optional(),
                new GreaterThan(['value' => 0]),
                new Callback(['callback' => $businessRule3])
            ],
            'usernameField' => [
                new Optional(),
                new Regex(['pattern' => '/^[a-zA-Z0-9-_]+$/']),
                new Length(['min' => 4, 'max' => 255]),
                new Callback(['callback' => $businessRule1])
            ],
            'dateField' => [
                new Optional(),
//                new DateTime(['format' => 'Y-m-d G:i:s']), // we only validate one pattern WRONG!!!
                new Callback(['callback' => $multiDateFormatValidator])
            ],
            'booleanField' => [
                new Optional(),
                new Choice(['choices' => [true, false, 'true', 'false', 0, 1, '0', '1']]), // does not have 'is boolean value' validator
            ],
            'optionField' => [
                new Optional(),
                new Choice(['choices' => ['person', 'deal']]),
                new Callback(['callback' => $businessRule2])
            ],
            'optionRequiredField' => [
                new Required(),
                new NotNull(),
                new Choice(['choices' => ['csv', 'xls']]),
            ],
            'arrayOfDigits' => [
                new All(['constraints' => [
                        new Optional(),
                        new GreaterThan(['value' => 0])
                    ]]
                )
            ],
        ]);

        $errors = $validator->validate($input, $collectionConstraint);
        if (count($errors) > 0) {
            print_r($errors);
            return false;
        }
        return true;
    }
}