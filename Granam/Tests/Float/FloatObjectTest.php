<?php
namespace Granam\Tests\Float;

use Granam\Float\FloatObject;

class FloatObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_can_create_float_object($strict, $paranoid)
    {
        $floatObject = new FloatObject($float = 123.456, $strict, $paranoid);
        self::assertNotNull($floatObject);
        self::assertInstanceOf('Granam\Float\FloatInterface', $floatObject);
        self::assertSame($float, $floatObject->getValue());
    }

    public function provideStrictnessAndParanoia()
    {
        return [
            [false, false],
            [false, true],
            [true, false],
            [true, true],
        ];
    }

    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_can_use_float_object_as_string($strict, $paranoid)
    {
        $floatObject = new FloatObject($float = 123.456, $strict, $paranoid);
        self::assertSame((string)$float, (string)$floatObject);
    }

    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_can_use_integer_value($strict, $paranoid)
    {
        $floatObject = new FloatObject($integer = 123, $strict, $paranoid);
        self::assertSame((float)$integer, $floatObject->getValue());
    }

    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_can_use_false_as_float_zero($strict, $paranoid)
    {
        $floatObject = new FloatObject(false, $strict, $paranoid);
        self::assertSame(0.0, $floatObject->getValue());
        self::assertSame((float)false, $floatObject->getValue());
    }

    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_can_use_true_as_float_one($strict, $paranoid)
    {
        $floatObject = new FloatObject(true, $strict, $paranoid);
        self::assertSame(1.0, $floatObject->getValue());
        self::assertSame((float)true, $floatObject->getValue());
    }

    /**
     * @test
     * @dataProvider provideParanoia
     * @param bool $paranoid
     */
    public function I_can_use_null_and_empty_string_as_float_zero_if_not_strict($paranoid)
    {
        $fromNull = new FloatObject(null, false /* not strict */, $paranoid);
        self::assertSame(0.0, $fromNull->getValue());
        self::assertSame((float)null, $fromNull->getValue());
        $fromEmptyString = new FloatObject('', false /* not strict */, $paranoid);
        self::assertEquals($fromNull, $fromEmptyString);
        $fromWhiteCharacters = new FloatObject("\n\t  \r", false /* not strict */, $paranoid);
        self::assertEquals($fromNull, $fromWhiteCharacters);
    }

    public function provideParanoia()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @test
     * @expectedException \Granam\Float\Tools\Exceptions\WrongParameterType
     * @dataProvider provideNonNumericNonBoolean
     * @param $value
     */
    public function I_can_not_use_non_numeric_non_boolean_by_default($value)
    {
        new FloatObject($value);
    }

    public function provideNonNumericNonBoolean()
    {
        return [
            [null],
            [''],
            ["  \n\t  \r"],
            ['one']
        ];
    }

    /**
     * @test
     * @expectedException \Granam\Float\Exceptions\WrongParameterType
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_cannot_use_array($strict, $paranoid)
    {
        new FloatObject([], $strict, $paranoid);
    }

    /**
     * @test
     * @expectedException \Granam\Float\Exceptions\WrongParameterType
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_cannot_use_resource($strict, $paranoid)
    {
        new FloatObject(tmpfile(), $strict, $paranoid);
    }

    /**
     * @test
     * @expectedException \Granam\Float\Exceptions\WrongParameterType
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_cannot_use_object($strict, $paranoid)
    {
        new FloatObject(new \stdClass(), $strict, $paranoid);
    }

    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_can_use_object_with_to_string($strict, $paranoid)
    {
        $floatObject = new FloatObject(new TestWithToString($float = 123.456), $strict, $paranoid);
        self::assertSame($float, $floatObject->getValue());
        $stringFloatObject = new FloatObject(new TestWithToString($stringFloat = '987.654'), $strict, $paranoid);
        self::assertSame((float)$stringFloat, $stringFloatObject->getValue());
    }

    /**
     * @test
     */
    public function I_get_to_string_object_without_number_as_float_zero_if_not_strict()
    {
        $float = new FloatObject(new TestWithToString($string = 'non-float'), false /* not strict */);
        self::assertSame(0.0, $float->getValue());
        self::assertSame((float)$string, $float->getValue());
    }

    /**
     * @test
     * @dataProvider provideStrictnessAndParanoia
     * @param bool $strict
     * @param bool $paranoid
     */
    public function I_get_value_without_wrapping_trash($strict, $paranoid)
    {
        $withWrappingZeroes = new FloatObject($zeroWrappedNumber = '0000123456.789000', $strict, $paranoid);
        self::assertSame(123456.789, $withWrappingZeroes->getValue());
        self::assertSame((float)$zeroWrappedNumber, $withWrappingZeroes->getValue());
        $integerLike = new FloatObject($integerLikeNumber = '0000123456.0000', $strict, $paranoid);
        self::assertSame(123456.0, $integerLike->getValue());
        self::assertSame((float)$integerLikeNumber, $integerLike->getValue());
    }

    /**
     * @test
     */
    public function I_get_number_cleared_of_leading_non_number_trash_if_not_strict()
    {
        $trashAround = new FloatObject($trashWrappedNumber = '   123456.0051500  foo bar 12565.04181 ', false /* not strict */);
        self::assertSame(123456.00515, $trashAround->getValue());
        self::assertSame((float)$trashWrappedNumber, $trashAround->getValue());
    }

    /**
     * @test
     * @expectedException \Granam\Float\Tools\Exceptions\WrongParameterType
     */
    public function I_can_not_use_number_with_leading_non_number_trash_by_default()
    {
        new FloatObject($trashWrappedNumber = '   123456.0051500  foo bar 12565.04181 ');
    }

    /**
     * @test
     */
    public function Rounding_is_done_silently_by_default()
    {
        $float = new FloatObject($withTooLongDecimal = '123456.999999999999999999999999999999999999');
        self::assertSame(123457.0, $float->getValue());
        self::assertSame((float)$withTooLongDecimal, $float->getValue());
        $float = new FloatObject($withTooLongInteger = '222222222222222222222222222222222222222222.123');
        self::assertSame(2.2222222222222224E+41, $float->getValue());
        self::assertSame((float)$withTooLongInteger, $float->getValue());
    }

    /**
     * @test
     * @expectedException \Granam\Float\Tools\Exceptions\ValueLostOnCast
     * @dataProvider provideStrictness
     * @param bool $strict
     */
    public function I_can_force_exception_in_case_of_rounding($strict)
    {
        try {
            $floatObject = new FloatObject($floatValue = '123456.999', $strict, true /* paranoid */);
            self::assertSame((float)$floatValue, $floatObject->getValue());
        } catch (\Exception $exception) {
            self::fail('Unexpected any exception here: ' . $exception->getMessage());
        }
        new FloatObject('123456.999999999999999999999999999999999999', $strict, true /* paranoid */);
        self::fail('Rounding has not been detected');
    }

    public function provideStrictness()
    {
        return [
            [true],
            [false],
        ];
    }

}

/** inner */
class TestWithToString
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}