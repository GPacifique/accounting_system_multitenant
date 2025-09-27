<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    public $title;
    public $value;
    public $sub;

    public function __construct($title, $value, $sub = null)
    {
        $this->title = $title;
        $this->value = $value;
        $this->sub = $sub;
    }

    public function render()
    {
        return view('components.dashboard-card');
    }
}
