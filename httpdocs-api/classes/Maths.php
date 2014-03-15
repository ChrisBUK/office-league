<?php
/**
* Maths functions 
*/

abstract class Maths
{
    /**
    * Find out if the number is of the power of two, for calculating cup competitions.
    * 
    * @param mixed $intNum
    */
    public static function isPowerOfTwo($intNum)
    {
        return ($intNum & ($intNum - 1)) == 0;
    }
}
?>
