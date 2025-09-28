<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Admin extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('layouts.admin');
    }
}
