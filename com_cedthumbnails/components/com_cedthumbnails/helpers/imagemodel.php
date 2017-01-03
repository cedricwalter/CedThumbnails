<?php

/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 5/30/2015
 * Time: 1:50 PM
 */
class imageModel
{

    var $url = "";
    var $filename = "";
    var $filePath = "";
    var $extension = "";

    function __construct($url, $filename, $filePath, $extension)
    {
        $this->url = $url;
        $this->filename = $filename;
        $this->filePath = $filePath;
        $this->extension = $extension;
    }




}