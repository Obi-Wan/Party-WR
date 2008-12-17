<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of directory_structure_exception
 *
 * @author ben
 */
class DirectoryStructureException extends Exception {
  public function __construct($message = "Generic DirectoryStructureException",
                              $code = null) {
    parent::__construct($message,$code);
  }
}
?>
