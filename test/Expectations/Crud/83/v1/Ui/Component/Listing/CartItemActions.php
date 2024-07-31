<?php

namespace Mygento\SampleModule\Ui\Component\Listing;

use Mygento\Base\Ui\Component\Listing\Actions;

class CartItemActions extends Actions
{
    /** @var string */
    protected $route = 'sample_module';

    /** @var string */
    protected $controller = 'cartitem';

    /** @var string */
    protected $key = 'cart_id';
}
