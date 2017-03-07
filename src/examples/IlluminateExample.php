<?php

namespace ValidatorsExample\examples;

use DateTime;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Validation\Factory;
use InvalidArgumentException;

class IlluminateExample extends BaseExample
{
    public function exampleAssertion(array $input = [])
    {
        $validator = $this->getValidator()->make($input, [
            'digitField' => 'integer|min:0',
            'usernameField' => 'nullable|string|alpha_dash|min:4|max:255|custom_username_checker',
            'dateField' => 'required|custom_date_format:Y-m-d G:i:s,Y-m-d G:i', // native 'date_format' validator does not support multiple formats
            'booleanField' => 'custom_boolean',
            'optionField' => 'in:person,deal',
            'optionRequiredField' => 'required|in:csv,xls',
            'arrayOfDigits' => 'array',
            'arrayOfDigits.*' => 'integer|min:0', // array contents validation
        ]);

        // business rule 2
        $validator->sometimes('optionField', 'required|in:person', function ($input) {
            return $input->optionRequiredField === 'csv';
        });

        // business rule 3
        $validator->sometimes('digitField', 'required', function ($input) {
            return $input->optionRequiredField === 'csv' && $input->optionField === 'person';
        });

        if ($validator->fails()) {
            print_r($validator->errors());
            return false;
        }
        return true;
    }

    /**
     * Create validator and add custom rules
     */
    private function getValidator()
    {
        $userService = $this->userService;
        $factory = new Factory(new WorkAroundTranslator(), null);

        // business rule 1
        $factory->extend('custom_username_checker', function ($attribute, $value, $parameters, $validator) use ($userService) {
            return $userService->isUserExists($value);
        });

        // "boolean" validator does not understand "true"/"false" values in string form. so add own custom validator.
        $factory->extend('custom_boolean', function ($attribute, $value, $parameters, $validator) {
            return null !== filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        });

        // native 'date_format' validator does not support multiple formats so add custom one
        $factory->extend('custom_date_format', function ($attribute, $value, $parameters, $validator) {
            if (count($parameters) < 1) {
                throw new InvalidArgumentException("Validation rule 'custom_date_format' requires at least 1 parameters.");
            }

            if (!is_string($value) && !is_numeric($value)) {
                return false;
            }

            foreach ($parameters as $dateFormats) {
                $date = DateTime::createFromFormat($dateFormats, $value);
                if ($date && $date->format($dateFormats) == $value) {
                    return true;
                }
            }
            return false;
        });

        return $factory;
    }
}

/**
 * Dirty workaround for validator factory constructor
 */
class WorkAroundTranslator implements Translator
{
    public function trans($key, array $replace = [], $locale = null)
    {
    }

    public function transChoice($key, $number, array $replace = [], $locale = null)
    {
    }

    public function getLocale()
    {
    }

    public function setLocale($locale)
    {
    }
}