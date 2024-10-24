<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\Models\WorkingCalendar;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportWorkingCalendar extends Component
{
    use WithFileUploads;

    public $workingCalendar;

    public $company;

    public $companyId;

    public $companyCode;

    public $companyName;

    public $branch;

    public $branchId;

    public $branchCode;

    public $branchName;

    public $import;

    public function importWorkingCalendar(): void
    {
        $this->validate([
            'import' => 'required|mimes:csv,xlsx,xls',
        ]);

        $this->import->store(path: 'workingCalendars');

        $this->import = $this->import->path();

        SimpleExcelReader::create($this->import)->getRows()
            ->each(function (array $rowProperties) {

                $this->company = Company::where('code', $rowProperties['comp_code'])->first();

                if (! $this->company) {
                    return;
                }

                $this->companyId = $this->company->id;
                $this->companyCode = $this->company->code;
                $this->companyName = $this->company->name;

                $this->branch = Branch::where('code', $rowProperties['branch_code'])->first();

                if (! $this->branch) {
                    return;
                }

                $this->companyId = $this->company->id;
                $this->companyCode = $this->company->code;
                $this->companyName = $this->company->name;

                WorkingCalendar::create([
                    'company_id' => $this->companyId,
                    'company_code' => $this->companyCode,
                    'company_name' => $this->companyName,
                    'branch_id' => $this->branchId,
                    'branch_code' => $this->branchCode,
                    'branch_name' => $this->branchName,
                    'date' => $rowProperties['date'],
                    'type' => $rowProperties['type'],
                    'description' => $rowProperties['description'],
                ]);
            });

        $this->dispatch('refresh');
    }

    public function render(): View
    {
        return view('livewire.import-working-calendar');
    }
}