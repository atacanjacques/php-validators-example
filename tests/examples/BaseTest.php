<?php

namespace tests\examples;


use PHPUnit\Framework\TestCase;

abstract class BaseTest extends TestCase
{
    const INPUT_OK = [
        'digitField' => 1,
        'usernameField' => 'long_valid-username',
        'dateField' => '2017-03-05 11:12:13',
        'booleanField' => 'true',
        'optionField' => 'person',
        'optionRequiredField' => 'csv',
        'arrayOfDigits' => [123, 234, 345]
    ];
    const INPUT_WITHOUT_OPTIONALS_OK = [
        'dateField' => '2017-03-05 11:12',
        'optionRequiredField' => 'xls'
    ];

    public abstract function runValidation(array $input = []);

    public function testShouldPass()
    {
        $this->assertTrue($this->runValidation(static::INPUT_OK));
    }

    public function testShouldFailForDigitField()
    {
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['digitField' => 'a']))); // not a number
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['digitField' => -1]))); // not positive
    }

    public function testShouldFailForUsernameField()
    {
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['usernameField' => 'a']))); // too short
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['usernameField' => "aa\x01aaaaaaaaaaaaaaaa"]))); // invalid characters
    }

    public function testShouldFailForDateField()
    {
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['dateField' => '2017-03-05T11:12:13']))); // not 'YYYY-MM-DD HH:MI:SS' format
    }

    public function testOptionField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionField' => 'person'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionField' => 'deal', 'optionRequiredField' => 'xls'])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['optionField' => 'person1']))); // not in ['person', 'deal']
    }

    public function testOptionRequiredField()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => 'xls'])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => 'csv'])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => 'exe']))); // not in ['csv', 'xls']
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['optionRequiredField' => null]))); // required, is missing
    }

    public function testArrayOfDigits()
    {
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => []])));
        $this->assertTrue($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => [1]])));

        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => ['a']]))); // array value not digit
        $this->assertFalse($this->runValidation(array_merge(static::INPUT_OK, ['arrayOfDigits' => [-1]]))); // array value not positive
    }

    public function testShouldNotPassBusiness2Rule()
    {
        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['optionField' => 'deal']);

        $this->assertFalse($this->runValidation($optionFieldNotCorrect));
    }

    public function testShouldNotPassBusiness3Rule()
    {
        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['digitField' => null]);

        $this->assertFalse($this->runValidation($optionFieldNotCorrect));
    }

    public function testShouldNotPassBusiness1Rule()
    {
        $optionFieldNotCorrect = array_merge(static::INPUT_OK, ['usernameField' => 'short']);

        $this->assertFalse($this->runValidation($optionFieldNotCorrect));
    }

    public function testShouldValidateAsTrue()
    {
        $this->assertTrue($this->runValidation(static::INPUT_OK));
    }


}