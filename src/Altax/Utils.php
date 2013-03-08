<?php
/**
 * Altax_Utils
 *
 * @author kohkimakimoto <kohki.makimoto@gmail.com>
 * @version $Revision$
 */
class Altax_Utils
{
  /**
   * Gets largest length of the array.
   * @param unknown $array
   */
  public static function arrayKeyLargestLength($array)
  {
    $ret = 0;
    $keys = array_keys($array);
    foreach ($keys as $key) {
      if (strlen($key) > $ret) {
        $ret = strlen($key);
      }
    }
    return $ret;
  }

  /*
  The Following Methods are copied from symfony web application framework version 1.4. (http://symfony.com/).
  */

  /*
  Copyright (c) 2004-2010 Fabien Potencier

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is furnished
  to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all
  copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
  */

  /**
   * Returns a camelized string from a lower case and underscored string by replaceing slash with
   * double-colon and upper-casing each letter preceded by an underscore.
   *
   * @param  string $lower_case_and_underscored_word  String to camelize.
   *
   * @return string Camelized string.
   */
  public static function camelize($lower_case_and_underscored_word)
  {
  	$tmp = $lower_case_and_underscored_word;
  	$tmp = self::pregtr($tmp, array('#/(.?)#e'    => "'::'.strtoupper('\\1')",
  			'/(^|_|-)+(.)/e' => "strtoupper('\\2')"));

  	return $tmp;
  }

  /**
   * Returns an underscore-syntaxed version or the CamelCased string.
   *
   * @param  string $camel_cased_word  String to underscore.
   *
   * @return string Underscored string.
   */
  public static function underscore($camel_cased_word)
  {
  	$tmp = $camel_cased_word;
  	$tmp = str_replace('::', '/', $tmp);
  	$tmp = self::pregtr($tmp, array('/([A-Z]+)([A-Z][a-z])/' => '\\1_\\2',
  			'/([a-z\d])([A-Z])/'     => '\\1_\\2'));

  	return strtolower($tmp);
  }

  /**
   * Returns subject replaced with regular expression matchs
   *
   * @param mixed $search        subject to search
   * @param array $replacePairs  array of search => replace pairs
   */
  public static function pregtr($search, $replacePairs)
  {
  	return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
  }

}