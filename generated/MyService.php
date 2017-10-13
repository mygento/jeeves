<?php

namespace Vendor\Project;

class MyService
{
    private $createdAt;

    private $filename;

    public function __construct(DateTime $createdAt, $filename)
    {
    }
}
