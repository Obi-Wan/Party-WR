<?php

define("DISK_IO_CLASS","1");

/** Class that manages all the interaction with the disk (and filesystem structure).
 */
class DiskIO {
  
  protected $dataContents;
  protected $galleries;
  protected $layouts;
  protected $relativePath;
  
  /** Constructor that scans for needed dirs and files.
   */
  function __construct ($relativePath = ".") {
    $this->relativePath = $relativePath;
    /* check the sane dir structure */

    /* first of all find all the galleries */
    $dirRef = new DirectoryIterator("{$relativePath}/gallery");
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
    $dirRef = new DirectoryIterator("{$relativePath}/templates");
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
    $dirRef = new DirectoryIterator("{$relativePath}/data");
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
   * @param $localPath Selected path
   * @return List of photos
   */
  function getPhotosOfGallery ( $gallery_year ) {
    /* We open the dir and scan the files. */
    $dirRef = new DirectoryIterator("{$this->relativePath}/gallery/{$gallery_year}");
    foreach ($dirRef as $iterDir) {
      if ($iterDir->isFile() ){
        $listOfPhotos[] = $iterDir->getFilename();
      }
    }
    unset ($dirRef);

    return $listOfPhotos;
  }

  /** Retrieves the list of photos for the selected gallery with path
   *
   * @param $gallery_year Selected year
   * @param $localPath Selected path
   * @param $includeLocalPath
   * @return List of photos
   */
  function getPhotosOfGalleryWithPath ( $gallery_year, $localPath = "." ,
                                $includeLocalPath = true )
  {
    /* We open the dir and scan the files. */
    $dirRef = new DirectoryIterator("{$localPath}/gallery/{$gallery_year}");
    foreach ($dirRef as $iterDir) {
      if ($iterDir->isFile() ){
        $listOfPhotos[] = ($includeLocalPath ? "{$localPath}/" : "") .
                  "/gallery/{$iterDir->getFilename()}";
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