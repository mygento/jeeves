<?php

namespace Mygento\Jeeves\Model;

class Acl
{
    private $id;
    private $title;
    private $children;

    public function __construct(string $id, string $title, array $children = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->children = $children;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getChildren(): ?array
    {
        return $this->children;
    }

//    public function setId(string $id)
//    {
//        $this->id = $id;
//    }
//
//    public function setTitle(string $title)
//    {
//        $this->title = $title;
//    }
//
//    public function setChildren(array $children)
//    {
//        $this->children = $children;
//    }
}
