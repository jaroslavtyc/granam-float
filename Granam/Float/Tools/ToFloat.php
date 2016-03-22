<?php
namespace Granam\Float\Tools;

use Granam\Number\Tools\ToNumber;

class ToFloat
{
    /**
     * @param $value
     * @param bool $strict = true allows only explicit values, not null and empty string
     * @param bool $paranoid Throws exception if some value is lost on cast due to rounding on cast
     * @return float
     */
    public static function toFloat($value, $strict = true, $paranoid = false)
    {
        // true = 1; false = 0; null = 0
        $value = self::convertToNumber($value, $strict, $paranoid);

        return (float)$value;
    }

    private static function convertToNumber($value, $strict, $paranoid)
    {
        try {
            return ToNumber::toNumber($value, $strict, $paranoid);
        } catch (\Granam\Number\Tools\Exceptions\WrongParameterType $exception) {
            throw new Exceptions\WrongParameterType($exception->getMessage(), $exception->getCode(), $exception);
        } catch (\Granam\Number\Tools\Exceptions\ValueLostOnCast $exception) {
            throw new Exceptions\ValueLostOnCast($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
