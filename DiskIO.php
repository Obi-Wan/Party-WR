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
    $dirRef = new DirectoryIterator("gallery");
    if ($dirRef) {
      foreach ($dirRef as $iterDir) {
        if ($iterDir->isDir() && (! $iterDir->isDot())){
          $this->galleries[] = $iterDir->getFilename();
        }
      }
    } else {
      unset ($dirRef);
      throw new DirectoryStructureException(
                                   "Directory gallery not found, but required");
    }
    unset ($dirRef);
    sort($this->galleries);
    
    /* verify templates are in place */
    $dirRef = new DirectoryIterator("templates");
    if ($dirRef) {
      foreach ($dirRef as $iterDir) {
        if ($iterDir->isDir() && (! $iterDir->isDot())){
          $this->layouts[] = $iterDir->getFilename();
        }
      }
    } else {
      unset ($dirRef);
      throw new DirectoryStructureException(
                                 "Directory templates not found, but required");
    }
    unset ($dirRef);
    
    /* verify contents are in place */
    $dirRef = new DirectoryIterator("data");
    if ($dirRef) {
      foreach ($dirRef as $iterDir) {
        if ($iterDir->isFile() ){
          $filename = $iterDir->getFilename();
          $basename = split("\.",$filename);
          $this->dataContents[$basename[0]] = "data/$filename";
        }
      }
    } else {
      unset ($dirRef);
      throw new DirectoryStructureException(
                                 "Directory data not found, but required");
    }
    unset ($dirRef);
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
    $dirRef = new DirectoryIterator("gallery/{$gallery_year}");
    foreach ($dirRef as $iterDir) {
      if ($iterDir->isFile() ){
        $listOfPhotos[] = $iterDir->getFilename();
      }
    }
    unset ($dirRef);

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