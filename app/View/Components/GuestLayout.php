<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public $title;
    public $description;
    public $ogImage;

    /**
     * Create a new component instance.
     */
    public function __construct($title = null, $description = null, $ogImage = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->ogImage = $ogImage;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
