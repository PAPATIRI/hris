<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\SimpleExcel\SimpleExcelReader;

class ImportDepartment extends Component
{
    use WithFileUploads;

    public $department;

    public $company;

    public $companyCode;

    public $branch;

    public $branchCode;

    public $import;

    /**
     * Handle the import of a department.
     */
    public function importDepartment(): void
    {
        $this->validate([
            'import' => 'required|mimes:csv,xlsx,xls',
        ]);

        $this->import->store(path: 'departments');

        $this->import = $this->import->path();

        SimpleExcelReader::create($this->import)->getRows()
            ->each(function (array $rowProperties) {

                $this->company = Company::where('code', $rowProperties['company_code'])->first();

                if (! $this->company) {
                    return;
                }

                $this->companyCode = $this->company->code;

                $this->branch = Branch::where('code', $rowProperties['branch_code'])->first();

                if (! $this->branch) {
                    return;
                }

                $this->branchCode = $this->branch->code;

                $this->department = Department::firstOrNew([
                    'code' => $rowProperties['code'],
                ]);

                $this->department->company_id = $this->company->id;
                $this->department->company_code = $this->companyCode;
                $this->department->company_name = $this->company->name;

                $this->department->branch_id = $this->branch->id;
                $this->department->branch_code = $this->branchCode;
                $this->department->branch_name = $this->branch->name;

                $this->department->code = $rowProperties['code'];
                $this->department->name = $rowProperties['name'];
                $this->department->description = $rowProperties['description'];
                $this->department->save();
            });

        $this->dispatch('refresh');
    }

    public function render(): View
    {
        return view('livewire.import-department');
    }
}
