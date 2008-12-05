<?php

define("DISK_IO_CLASS","1");

/** Class that manages all the interaction with the disk (and filesystem structure).
 */
class DiskIO {
  
  protected $dataContents;
  protected $galleries;
  protected $layouts;
  
  /** Constructor that scans for needed dirs and files.
   */
  function __construct () {
    /* check the sane dir structure */
    
    /* first of all find all the galleries */
    if ($handle = opendir("gallery")) {
      //eseguo un loop per individuare tutti i file della directory
      while (false !== ($file = readdir($handle))) { 
        if (!in_array($file,array(".",".."))) {
          $this->galleries[] = $file;
        }
      }
      closedir($handle);
    }
    sort($this->galleries);
    
    /* verify templates are in place */
    if ($handle = opendir("templates")) {
      //eseguo un loop per individuare tutti i file della directory
      while (false !== ($file = readdir($handle))) { 
        if (!in_array($file,array(".",".."))) {
          $this->layouts[] = $file;
        }
      }
      closedir($handle);
    }
    
    /* verify contents are in place */
    if ($handle = opendir("data")) {
      //eseguo un loop per individuare tutti i file della directory
      while (false !== ($file = readdir($handle))) { 
        if (! (in_array($file,array(".","..")) || 0 ) ) {
          //print "$file ";
          $basename = split("\.",$file);  /* sono regular expression!!! */
          //$this->dataContents[(split(".",$file))[0]] = "data/$file";
          $this->dataContents[$basename[0]] = "data/$file";
        }
      }
      closedir($handle);
    }
  }
  
  /** Retrieves the list of templates avaible
   * 
   * @return List of avaible templates
   */
  function getTemplates () {
    return $this->layouts;
  }
  
  /** Retrieves the list of Galleries avaible
   * 
   * @return List of avaible galleries
   */
  function getGalleries () {
    return $this->galleries;
  }
  
  /** Retrieves the list of photos for the selected gallery
   * 
   * @param $gallery_year Selected year
   * @return List of photos
   */
  function getPhotosOfGallery ( $gallery_year ) {
    /* We open the dir and scan the files. */
    if ($handle = opendir("gallery/$gallery_year")) {
      while (false !== ($file = readdir($handle))) { 
        /* all the other files are ok */
        if (!in_array($file,array(".","..","thumbs"))) {
          $listOfPhotos[] = $file;
        }
      }
      closedir($handle);
    }
    return $listOfPhotos;
  }
  
  /** Retrieves the selected container
   * 
   * @param $rawContents Selected container
   * @return parsed XML content of selected container
   */
  function getRawContents ( $rawContents ) {
    $allContents = file_get_contents($this->dataContents[ $rawContents ]);
    return new SimpleXMLElement($allContents);
  }
}

?>