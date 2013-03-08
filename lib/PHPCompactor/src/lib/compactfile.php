<?php
/**
 *
 * Compactor.php -- File class
 *
 * (c) 2010 Jurriaan Pruis (email@jurriaanpruis.nl)
 *
 **/
 
class CompactFile {
  private $out, $filterfunc;
  public $filesize, $compressedsize, $filename;
  
  /**
   * Creates CompactFile object
   *
   * @param string $filename
   *  The input file.
   * @param resource $out
   *  The output filepointer to which the compressed data is appended.
   */
  public function __construct($filename,$out,$filterfunc = null) {
    $this->filename = $filename;
    $this->filesize = filesize($filename);
    $this->out = $out;
    if($filterfunc == null) {
      $filterfunc = function ($in) {return $in;};
    }
    $this->filterfunc = $filterfunc;
  }
  
  /**
   * Compacts $filename.
   */
  public function compact() {
    $tokens = $this->getTokens();
    $removenext = false; // Remove next whitespace
    $start = ftell($this->out); // Position in output
    $len = count($tokens);
    for ($i=0;$len > $i;$i++) {
      $token = $tokens[$i];
      $nexttoken = ($i+1 < $len)?$tokens[$i+1]:'';
      $prevtoken = ($i-1 > -1)?$tokens[$i-1]:'';

      if (is_string($token)) {
        if(in_array($token,Compactor::$safechar)) $removenext = true;
        $this->write($token);
      } else if($token[0] == T_WHITESPACE) {
        if(!$removenext) {
          if(is_string($nexttoken)) {
            if(!in_array($nexttoken,Compactor::$safechar) && !(in_array($nexttoken,Compactor::$semisafe) && in_array($prevtoken[0],Compactor::$keyword))) {
              $this->write(' ');
            }
          } else if(!in_array($nexttoken[0],Compactor::$beforetoken)) {
            $this->write(' ');
          } 
        }
      } else if(in_array($token[0],Compactor::$aftertoken)) {
        $removenext = true;
        $this->write($token[1]);
      } else if(in_array($token[0],Compactor::$removable)) {
        $removenext = true;
      } else if(in_array($token[0],Compactor::$requires)) { // Remove require + everything until ';', maybe not safe?
        for ($i2 = $i;$len > $i2;$i2++) {
          $rtoken = &$tokens[$i2];
          if($rtoken == ';') {
            $rtoken = '';
            break;
          } else {
            $rtoken = '';
          }
        }
      } else {
        $removenext = false;
        $this->write($token[1]);
      }
    }
    $this->compressedsize = ftell($this->out) - $start;
  }
  
  private function write($string) {
    fwrite($this->out, $string);
  }
  
  private function getTokens() {
    $func = $this->filterfunc;
    return token_get_all($func(trim(file_get_contents($this->filename))));
  }
}
