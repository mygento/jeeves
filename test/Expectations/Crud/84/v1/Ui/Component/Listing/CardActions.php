<?php

namespace Mygento\SampleModule\Ui\Component\Listing;

use Mygento\Base\Ui\Component\Listing\Actions;

class CardActions extends Actions
{
    /** @var string */
    protected $route = 'sample_module';

    /** @var string */
    protected $controller = 'card';

    /** @var string */
    protected $key = 'card_id';
}
