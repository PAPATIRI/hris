<?php

namespace App\Livewire;

use App\Models\Division;
use App\Models\Level;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FormLevelOption extends Component
{
    public $levelCode;

    public $levels;

    /**
     * Update the level ID and dispatch the 'setLevel' event with the new ID.
     *
     * @param  string  $levelCode  The new level ID.
     */
    public function updatedLevelCode(string $levelCode): void
    {
        $this->dispatch('setLevel', $levelCode);
        $this->dispatch('getPosition', $levelCode);
    }

    /**
     * Retrieves the levels associated with a given division code.
     *
     * @param  string  $divisionCode  The code of the division.
     */
    #[On('get-level')]
    public function getLevel(string $divisionCode): void
    {
        $this->reset([
            'levels',
            'option',
        ]);

        $division = Division::where('code', $divisionCode)->first();

        if ($division == null) {
            return;
        }

        $levels = Level::where('division_id', $division->id);

        if ($levels->count() > 0) {
            $this->levels = $levels->get();
        }
    }

    /**
     * Render the view for the form level option.
     *
     * @return View The rendered view.
     */
    public function render(): View
    {
        return view('livewire.form-level-option');
    }
}
