<?php
class Altax_UtilsTest extends \PHPUnit_Framework_TestCase
{
  public function testArrayKeyLargestLength()
  {
    $tArray = array(
      'aaa' => "",
      'aaabbb' => "",
      'aaacceee' => "",
      'aaeea' => "",
    );
    $this->assertEquals(8, Altax_Utils::arrayKeyLargestLength($tArray));

    $tArray = array(
        'gewaof' => "",
        'geweeeeeeaofgewaof' => "",
        'efwaaq3dececa' => "",
        '1' => "",
    );
    $this->assertEquals(18, Altax_Utils::arrayKeyLargestLength($tArray));
  }




}