<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class WorkingDayPage extends Component
{
    public $moduleLabel = 'Working Day';

    /**
     * Renders the view for the brand page.
     *
     * @return View The rendered view.
     */
    public function render(): View
    {
        return view('livewire.working-day-page')
            ->layout('components.layouts.dashboard');
    }
}
