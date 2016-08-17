<?php

namespace David\BlogBundle\Entity;

class Articles
{
    public $id;
    public $title;
    public $content;

    function __construct($id, $title, $content)
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
    }

    function getId()
    {
        return $this->id;
    }

    function getTitle()
    {
        return $this->title;
    }

    function getContent()
    {
        return $this->content;
    }



}

?>