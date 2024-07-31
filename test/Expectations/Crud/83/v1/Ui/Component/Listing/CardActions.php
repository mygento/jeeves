<?php

namespace Mygento\SampleModule\Ui\Component\Listing;

use Mygento\Base\Ui\Component\Listing\Actions;

class CardActions extends Actions
{
    protected string $route = 'sample_module';
    protected string $controller = 'card';
    protected string $key = 'card_id';
}
