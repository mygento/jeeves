<?php

namespace Mygento\SampleModule\Ui\Component\Listing;

use Mygento\Base\Ui\Component\Listing\Actions;

class CartItemActions extends Actions
{
    protected string $route = 'sample_module';
    protected string $controller = 'cartitem';
    protected string $key = 'cart_id';
}
