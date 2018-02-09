<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2012 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.7.7, 2012-05-19
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/** MAX_VALUE */
define('MAX_VALUE', 1.2e308);

/** 2 / PI */
define('M_2DIVPI', 0.63661977236758134307553505349006);

/** MAX_ITERATIONS */
define('MAX_ITERATIONS', 256);

/** PRECISION */
define('PRECISION', 8.88E-016);


/**
 * PHPExcel_Calculation_Functions
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Functions
{

    /** constants */
    const COMPATIBILITY_EXCEL        = 'Excel';
    const COMPATIBILITY_GNUMERIC    = 'Gnumeric';
    const COMPATIBILITY_OPENOFFICE    = 'OpenOfficeCalc';

    const RETURNDATE_PHP_NUMERIC    = 'P';
    const RETURNDATE_PHP_OBJECT        = 'O';
    const RETURNDATE_EXCEL            = 'E';


    /**
     * Compatibility mode to use for error checking and responses
     *
     * @access	private
     * @var string
     */
    protected static $compatibilityMode    = self::COMPATIBILITY_EXCEL;

    /**
     * Data Type to use when returning date values
     *
     * @access	private
     * @var string
     */
    protected static $ReturnDateType    = self::RETURNDATE_EXCEL;

    /**
     * List of error codes
     *
     * @access	private
     * @var array
     */
    protected static $_errorCodes    = array( 'null'                => '#NULL!',
                                             'divisionbyzero'    => '#DIV/0!',
                                             'value'            => '#VALUE!',
                                             'reference'        => '#REF!',
                                             'name'                => '#NAME?',
                                             'num'                => '#NUM!',
                                             'na'                => '#N/A',
                                             'gettingdata'        => '#GETTING_DATA'
                                           );


    /**
     * Set the Compatibility Mode
     *
     * @access	public
     * @category Function Configuration
     * @param	 string		$compatibilityMode		Compatibility Mode
     *												Permitted values are:
     *													PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL			'Excel'
     *													PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC		'Gnumeric'
     *													PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE	'OpenOfficeCalc'
     * @return	 boolean	(Success or Failure)
     */
    public static function setCompatibilityMode($compatibilityMode)
    {
        if (($compatibilityMode == self::COMPATIBILITY_EXCEL) ||
            ($compatibilityMode == self::COMPATIBILITY_GNUMERIC) ||
            ($compatibilityMode == self::COMPATIBILITY_OPENOFFICE)) {
            self::$compatibilityMode = $compatibilityMode;
            return true;
        }
        return false;
    }    //	function setCompatibilityMode()


    /**
     * Return the current Compatibility Mode
     *
     * @access	public
     * @category Function Configuration
     * @return	 string		Compatibility Mode
     *							Possible Return values are:
     *								PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL			'Excel'
     *								PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC		'Gnumeric'
     *								PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE	'OpenOfficeCalc'
     */
    public static function getCompatibilityMode()
    {
        return self::$compatibilityMode;
    }    //	function getCompatibilityMode()


    /**
     * Set the Return Date Format used by functions that return a date/time (Excel, PHP Serialized Numeric or PHP Object)
     *
     * @access	public
     * @category Function Configuration
     * @param	 string	$returnDateType			Return Date Format
     *												Permitted values are:
     *													PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC		'P'
     *													PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT		'O'
     *													PHPExcel_Calculation_Functions::RETURNDATE_EXCEL			'E'
     * @return	 boolean							Success or failure
     */
    public static function setReturnDateType($returnDateType)
    {
        if (($returnDateType == self::RETURNDATE_PHP_NUMERIC) ||
            ($returnDateType == self::RETURNDATE_PHP_OBJECT) ||
            ($returnDateType == self::RETURNDATE_EXCEL)) {
            self::$ReturnDateType = $returnDateType;
            return true;
        }
        return false;
    }    //	function setReturnDateType()


    /**
     * Return the current Return Date Format for functions that return a date/time (Excel, PHP Serialized Numeric or PHP Object)
     *
     * @access	public
     * @category Function Configuration
     * @return	 string		Return Date Format
     *							Possible Return values are:
     *								PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC		'P'
     *								PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT		'O'
     *								PHPExcel_Calculation_Functions::RETURNDATE_EXCEL			'E'
     */
    public static function getReturnDateType()
    {
        return self::$ReturnDateType;
    }    //	function getReturnDateType()


    /**
     * DUMMY
     *
     * @access	public
     * @category Error Returns
     * @return	string	#Not Yet Implemented
     */
    public static function DUMMY()
    {
        return '#Not Yet Implemented';
    }    //	function DUMMY()


    /**
     * DIV0
     *
     * @access	public
     * @category Error Returns
     * @return	string	#Not Yet Implemented
     */
    public static function DIV0()
    {
        return self::$_errorCodes['divisionbyzero'];
    }    //	function DIV0()


    /**
     * NA
     *
     * Excel Function:
     *		=NA()
     *
     * Returns the error value #N/A
     *		#N/A is the error value that means "no value is available."
     *
     * @access	public
     * @category Logical Functions
     * @return	string	#N/A!
     */
    public static function NA()
    {
        return self::$_errorCodes['na'];
    }    //	function NA()


    /**
     * NaN
     *
     * Returns the error value #NUM!
     *
     * @access	public
     * @category Error Returns
     * @return	string	#NUM!
     */
    public static function NaN()
    {
        return self::$_errorCodes['num'];
    }    //	function NaN()


    /**
     * NAME
     *
     * Returns the error value #NAME?
     *
     * @access	public
     * @category Error Returns
     * @return	string	#NAME?
     */
    public static function NAME()
    {
        return self::$_errorCodes['name'];
    }    //	function NAME()


    /**
     * REF
     *
     * Returns the error value #REF!
     *
     * @access	public
     * @category Error Returns
     * @return	string	#REF!
     */
    public static function REF()
    {
        return self::$_errorCodes['reference'];
    }    //	function REF()


    /**
     * NULL
     *
     * Returns the error value #NULL!
     *
     * @access	public
     * @category Error Returns
     * @return	string	#REF!
     */
    public static function null()
    {
        return self::$_errorCodes['null'];
    }    //	function NULL()


    /**
     * VALUE
     *
     * Returns the error value #VALUE!
     *
     * @access	public
     * @category Error Returns
     * @return	string	#VALUE!
     */
    public static function VALUE()
    {
        return self::$_errorCodes['value'];
    }    //	function VALUE()


    public static function isMatrixValue($idx)
    {
        return ((substr_count($idx, '.') <= 1) || (preg_match('/\.[A-Z]/', $idx) > 0));
    }


    public static function isValue($idx)
    {
        return (substr_count($idx, '.') == 0);
    }


    public static function isCellValue($idx)
    {
        return (substr_count($idx, '.') > 1);
    }


    public static function _ifCondition($condition)
    {
        $condition    = PHPExcel_Calculation_Functions::flattenSingleValue($condition);
        if (!in_array($condition{0}, array('>', '<', '='))) {
            if (!is_numeric($condition)) {
                $condition = PHPExcel_Calculation::_wrapResult(strtoupper($condition));
            }
            return '='.$condition;
        } else {
            preg_match('/([<>=]+)(.*)/', $condition, $matches);
            list(, $operator, $operand) = $matches;
            if (!is_numeric($operand)) {
                $operand = PHPExcel_Calculation::_wrapResult(strtoupper($operand));
            }
            return $operator.$operand;
        }
    }    //	function _ifCondition()


    /**
     * ERROR_TYPE
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function ERROR_TYPE($value = '')
    {
        $value    = self::flattenSingleValue($value);

        $i = 1;
        foreach (self::$_errorCodes as $errorCode) {
            if ($value === $errorCode) {
                return $i;
            }
            ++$i;
        }
        return self::NA();
    }    //	function ERROR_TYPE()


    /**
     * IS_BLANK
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function IS_BLANK($value = null)
    {
        if (!is_null($value)) {
            $value    = self::flattenSingleValue($value);
        }

        return is_null($value);
    }    //	function IS_BLANK()


    /**
     * IS_ERR
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function IS_ERR($value = '')
    {
        $value        = self::flattenSingleValue($value);

        return self::IS_ERROR($value) && (!self::IS_NA($value));
    }    //	function IS_ERR()


    /**
     * IS_ERROR
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function IS_ERROR($value = '')
    {
        $value        = self::flattenSingleValue($value);

        if (!is_string($value)) {
            return false;
        }
        return in_array($value, array_values(self::$_errorCodes));
    }    //	function IS_ERROR()


    /**
     * IS_NA
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function IS_NA($value = '')
    {
        $value        = self::flattenSingleValue($value);

        return ($value === self::NA());
    }    //	function IS_NA()


    /**
     * IS_EVEN
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function IS_EVEN($value = null)
    {
        $value = self::flattenSingleValue($value);

        if ($value === null) {
            return self::NAME();
        }
        if ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return self::VALUE();
        }
        return ($value % 2 == 0);
    }    //	function IS_EVEN()


    /**
     * IS_ODD
     *
     * @param	mixed	$value	Value to check
     * @return	boolean
     */
    public static function IS_ODD($value = null)
    {
        $value = self::flattenSingleValue($value);

        if ($value === null) {
            return self::NAME();
        }
        if ((is_bool($value)) || ((is_string($value)) && (!is_numeric($value)))) {
            return self::VALUE();
        }
        return (abs($value) % 2 == 1);
    }    //	function IS_ODD()


    /**
     * IS_NUMBER
     *
     * @param	mixed	$value		Value to check
     * @return	boolean
     */
    public static function IS_NUMBER($value = null)
    {
        $value        = self::flattenSingleValue($value);

        if (is_string($value)) {
            return false;
        }
        return is_numeric($value);
    }    //	function IS_NUMBER()


    /**
     * IS_LOGICAL
     *
     * @param	mixed	$value		Value to check
     * @return	boolean
     */
    public static function IS_LOGICAL($value = null)
    {
        $value        = self::flattenSingleValue($value);

        return is_bool($value);
    }    //	function IS_LOGICAL()


    /**
     * IS_TEXT
     *
     * @param	mixed	$value		Value to check
     * @return	boolean
     */
    public static function IS_TEXT($value = null)
    {
        $value        = self::flattenSingleValue($value);

        return (is_string($value) && !self::IS_ERROR($value));
    }    //	function IS_TEXT()


    /**
     * IS_NONTEXT
     *
     * @param	mixed	$value		Value to check
     * @return	boolean
     */
    public static function IS_NONTEXT($value = null)
    {
        return !self::IS_TEXT($value);
    }    //	function IS_NONTEXT()


    /**
     * VERSION
     *
     * @return	string	Version information
     */
    public static function VERSION()
    {
        return 'PHPExcel 1.7.7, 2012-05-19';
    }    //	function VERSION()


    /**
     * N
     *
     * Returns a value converted to a number
     *
     * @param	value		The value you want converted
     * @return	number		N converts values listed in the following table
     *		If value is or refers to N returns
     *		A number			That number
     *		A date				The serial number of that date
     *		TRUE				1
     *		FALSE				0
     *		An error value		The error value
     *		Anything else		0
     */
    public static function N($value = null)
    {
        while (is_array($value)) {
            $value = array_shift($value);
        }

        switch (gettype($value)) {
            case 'double':
            case 'float':
            case 'integer':
                return $value;
                break;
            case 'boolean':
                return (integer) $value;
                break;
            case 'string':
                //	Errors
                if ((strlen($value) > 0) && ($value{0} == '#')) {
                    return $value;
                }
                break;
        }
        return 0;
    }    //	function N()


    /**
     * TYPE
     *
     * Returns a number that identifies the type of a value
     *
     * @param	value		The value you want tested
     * @return	number		N converts values listed in the following table
     *		If value is or refers to N returns
     *		A number			1
     *		Text				2
     *		Logical Value		4
     *		An error value		16
     *		Array or Matrix		64
     */
    public static function TYPE($value = null)
    {
        $value    = self::flattenArrayIndexed($value);
        if (is_array($value) && (count($value) > 1)) {
            $a = array_keys($value);
            $a = array_pop($a);
            //	Range of cells is an error
            if (self::isCellValue($a)) {
                return 16;
            //	Test for Matrix
            } elseif (self::isMatrixValue($a)) {
                return 64;
            }
        } elseif (empty($value)) {
            //	Empty Cell
            return 1;
        }
        $value    = self::flattenSingleValue($value);

        if (($value === null) || (is_float($value)) || (is_int($value))) {
            return 1;
        } elseif (is_bool($value)) {
            return 4;
        } elseif (is_array($value)) {
            return 64;
            break;
        } elseif (is_string($value)) {
            //	Errors
            if ((strlen($value) > 0) && ($value{0} == '#')) {
                return 16;
            }
            return 2;
        }
        return 0;
    }    //	function TYPE()


    /**
     * Convert a multi-dimensional array to a simple 1-dimensional array
     *
     * @param	array	$array	Array to be flattened
     * @return	array	Flattened array
     */
    public static function flattenArray($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = array();
        foreach ($array as $value) {
            if (is_array($value)) {
                foreach ($value as $val) {
                    if (is_array($val)) {
                        foreach ($val as $v) {
                            $arrayValues[] = $v;
                        }
                    } else {
                        $arrayValues[] = $val;
                    }
                }
            } else {
                $arrayValues[] = $value;
            }
        }

        return $arrayValues;
    }    //	function flattenArray()


    /**
     * Convert a multi-dimensional array to a simple 1-dimensional array, but retain an element of indexing
     *
     * @param	array	$array	Array to be flattened
     * @return	array	Flattened array
     */
    public static function flattenArrayIndexed($array)
    {
        if (!is_array($array)) {
            return (array) $array;
        }

        $arrayValues = array();
        foreach ($array as $k1 => $value) {
            if (is_array($value)) {
                foreach ($value as $k2 => $val) {
                    if (is_array($val)) {
                        foreach ($val as $k3 => $v) {
                            $arrayValues[$k1.'.'.$k2.'.'.$k3] = $v;
                        }
                    } else {
                        $arrayValues[$k1.'.'.$k2] = $val;
                    }
                }
            } else {
                $arrayValues[$k1] = $value;
            }
        }

        return $arrayValues;
    }    //	function flattenArrayIndexed()


    /**
     * Convert an array to a single scalar value by extracting the first element
     *
     * @param	mixed		$value		Array or scalar value
     * @return	mixed
     */
    public static function flattenSingleValue($value = '')
    {
        while (is_array($value)) {
            $value = array_pop($value);
        }

        return $value;
    }    //	function flattenSingleValue()
}    //	class PHPExcel_Calculation_Functions


//
//	There are a few mathematical functions that aren't available on all versions of PHP for all platforms
//	These functions aren't available in Windows implementations of PHP prior to version 5.3.0
//	So we test if they do exist for this version of PHP/operating platform; and if not we create them
//
if (!function_exists('acosh')) {
    function acosh($x)
    {
        return 2 * log(sqrt(($x + 1) / 2) + sqrt(($x - 1) / 2));
    }    //	function acosh()
}

if (!function_exists('asinh')) {
    function asinh($x)
    {
        return log($x + sqrt(1 + $x * $x));
    }    //	function asinh()
}

if (!function_exists('atanh')) {
    function atanh($x)
    {
        return (log(1 + $x) - log(1 - $x)) / 2;
    }    //	function atanh()
}

if (!function_exists('money_format')) {
    function money_format($format, $number)
    {
        $regex = array( '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?(?:#([0-9]+))?',
                         '(?:\.([0-9]+))?([in%])/'
                      );
        $regex = implode('', $regex);
        if (setlocale(LC_MONETARY, null) == '') {
            setlocale(LC_MONETARY, '');
        }
        $locale = localeconv();
        $number = floatval($number);
        if (!preg_match($regex, $format, $fmatch)) {
            trigger_error("No format specified or invalid format", E_USER_WARNING);
            return $number;
        }
        $flags = array( 'fillchar'    => preg_match('/\=(.)/', $fmatch[1], $match) ? $match[1] : ' ',
                        'nogroup'    => preg_match('/\^/', $fmatch[1]) > 0,
                        'usesignal'    => preg_match('/\+|\(/', $fmatch[1], $match) ? $match[0] : '+',
                        'nosimbol'    => preg_match('/\!/', $fmatch[1]) > 0,
                        'isleft'    => preg_match('/\-/', $fmatch[1]) > 0
                      );
        $width    = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
        $left    = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
        $right    = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
        $conversion = $fmatch[5];
        $positive = true;
        if ($number < 0) {
            $positive = false;
            $number *= -1;
        }
        $letter = $positive ? 'p' : 'n';
        $prefix = $suffix = $cprefix = $csuffix = $signal = '';
        if (!$positive) {
            $signal = $locale['negative_sign'];
            switch (true) {
                case $locale['n_sign_posn'] == 0 || $flags['usesignal'] == '(':
                    $prefix = '(';
                    $suffix = ')';
                    break;
                case $locale['n_sign_posn'] == 1:
                    $prefix = $signal;
                    break;
                case $locale['n_sign_posn'] == 2:
                    $suffix = $signal;
                    break;
                case $locale['n_sign_posn'] == 3:
                    $cprefix = $signal;
                    break;
                case $locale['n_sign_posn'] == 4:
                    $csuffix = $signal;
                    break;
            }
        }
        if (!$flags['nosimbol']) {
            $currency = $cprefix;
            $currency .= ($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']);
            $currency .= $csuffix;
            $currency = iconv('ISO-8859-1', 'UTF-8', $currency);
        } else {
            $currency = '';
        }
        $space = $locale["{$letter}_sep_by_space"] ? ' ' : '';

        if (!isset($locale['mon_decimal_point']) || empty($locale['mon_decimal_point'])) {
            $locale['mon_decimal_point'] = (!isset($locale['decimal_point']) || empty($locale['decimal_point'])) ?
                                            $locale['decimal_point'] :
                                            '.';
        }

        $number = number_format($number, $right, $locale['mon_decimal_point'], $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);
        $number = explode($locale['mon_decimal_point'], $number);

        $n = strlen($prefix) + strlen($currency);
        if ($left > 0 && $left > $n) {
            if ($flags['isleft']) {
                $number[0] .= str_repeat($flags['fillchar'], $left - $n);
            } else {
                $number[0] = str_repeat($flags['fillchar'], $left - $n) . $number[0];
            }
        }
        $number = implode($locale['mon_decimal_point'], $number);
        if ($locale["{$letter}_cs_precedes"]) {
            $number = $prefix . $currency . $space . $number . $suffix;
        } else {
            $number = $prefix . $number . $space . $currency . $suffix;
        }
        if ($width > 0) {
            $number = str_pad($number, $width, $flags['fillchar'], $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT);
        }
        $format = str_replace($fmatch[0], $number, $format);
        return $format;
    }    //	function money_format()
}


//
//	Strangely, PHP doesn't have a mb_str_replace multibyte function
//	As we'll only ever use this function with UTF-8 characters, we can simply "hard-code" the character set
//
if ((!function_exists('mb_str_replace')) &&
    (function_exists('mb_substr')) && (function_exists('mb_strlen')) && (function_exists('mb_strpos'))) {
    function mb_str_replace($search, $replace, $subject)
    {
        if (is_array($subject)) {
            $ret = array();
            foreach ($subject as $key => $val) {
                $ret[$key] = mb_str_replace($search, $replace, $val);
            }
            return $ret;
        }

        foreach ((array) $search as $key => $s) {
            if ($s == '') {
                continue;
            }
            $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
            $pos = mb_strpos($subject, $s, 0, 'UTF-8');
            while ($pos !== false) {
                $subject = mb_substr($subject, 0, $pos, 'UTF-8') . $r . mb_substr($subject, $pos + mb_strlen($s, 'UTF-8'), 65535, 'UTF-8');
                $pos = mb_strpos($subject, $s, $pos + mb_strlen($r, 'UTF-8'), 'UTF-8');
            }
        }
        return $subject;
    }
}
