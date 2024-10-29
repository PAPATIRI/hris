<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Level;
use App\Models\Position;
use App\Models\SubDivision;
use App\Models\User;
use App\Models\UserDetail;
use DB;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class UserForm extends Component
{
    public $company;

    #[Url(keep: true)]
    public $companyCode;

    public $branch;

    #[Url(keep: true)]
    public $branchCode;

    public $department;

    #[Url(keep: true)]
    public $departmentCode;

    public $division;

    #[Url(keep: true)]
    public $divisionCode;

    public $subDivision;

    #[Url(keep: true)]
    public $subDivisionCode;

    public $level;

    #[Url(keep: true)]
    public $levelCode;

    public $position;

    #[Url(keep: true)]
    public $positionCode;


    #[Validate('required|unique:users|regex:/^\S+$/')]
    public $name;

    #[Validate('required|email|unique:users')]
    public $email;

    public $password;

    public $password_confirmation;

    public $newPassword = false;

    public $details = [
        'department_id' => null,
        'division_id' => null,
        'sub_division_id' => null,
        'position_id' => null,
        'level_id' => null,
        'nik' => null,
        'first_name' => null,
        'last_name' => null,
        'phone' => null,
        'address' => null,
        'gender' => null,
        'religion' => null,
        'last_education' => null,
        'marriage_status' => null,
        'place_of_birth' => null,
        'date_of_birth' => null,
        'ktp' => null,
        'npwp' => null,
        'bank_account' => null,
        'date_in' => null,
        'date_out' => null,
        'note' => null,
    ];

    public $user;

    public $actionForm = 'save';

    /**
     * Mount the component
     */
    public function mount(): void
    {
        if ($this->companyCode != '') {
            $this->company = Company::where('code', $this->companyCode)->first();
        }

        if ($this->branchCode != '') {
            $this->branch = Branch::where('code', $this->branchCode)->first();
        }
    }

    /**
     * Set the details for the user to be created.
     *
     * @param array $details  The details of the user.
     */
    #[On('set-detail')]
    public function setDetail(array $details): void
    {
        foreach ($details as $key => $value) {
            $this->details[$key] = $value;
        }
    }

    #[On('set-company')]
    public function setCompany(string $companyCode): void
    {
        if ($companyCode != '') {
            $this->company = Company::where('code', $companyCode)->first();
            $this->details['company_id'] = $this->company->id;
            $this->details['company_code'] = $this->company->code;
            $this->details['company_name'] = $this->company->name;
        }
    }

    #[On('set-branch')]
    public function setBranch(string $branchCode): void
    {
        if ($branchCode != '') {
            $this->branch = Branch::where('code', $branchCode)->first();
            $this->details['branch_id'] = $this->branch->id;
            $this->details['branch_code'] = $this->branch->code;
            $this->details['branch_name'] = $this->branch->name;
        }
    }

    /**
     * Set the department ID for the details.
     *
     * @param  string  $departmentCode  The ID of the department.
     */
    #[On('set-department')]
    public function setDepartment(string $departmentCode): void
    {
        if ($departmentCode != '') {
            $this->department = Department::where('code', $departmentCode)->first();
            $this->details['department_id'] = $this->department->id;
            $this->details['department_code'] = $this->department->code;
            $this->details['department_name'] = $this->department->name;
        }
    }

    /**
     * Set the division ID for the details.
     *
     * @param  string  $divisionCode  The ID of the division.
     */
    #[On('set-division')]
    public function setDivision(string $divisionCode): void
    {
        if ($divisionCode != '') {
            $this->division = Division::where('code', $divisionCode)->first();
            $this->details['division_id'] = $this->division->id;
            $this->details['division_code'] = $this->division->code;
            $this->details['division_name'] = $this->division->name;
        }
    }

    /**
     * Set the subdivision ID for the details.
     *
     * @param  string  $subDivisionCode  The ID of the subdivision.
     */
    #[On('set-sub-division')]
    public function setSubDivision(string $subDivisionCode): void
    {
        if ($subDivisionCode != '') {
            $this->subDivision = SubDivision::where('code', $subDivisionCode)->first();
            $this->details['sub_division_id'] = $this->subDivision->id;
            $this->details['sub_division_code'] = $this->subDivision->code;
            $this->details['sub_division_name'] = $this->subDivision->name;
        }
    }

    /**
     * Set the level ID for the details.
     *
     * @param  string  $levelCode  The ID of the level.
     */
    #[On('set-level')]
    public function setLevel(string $levelCode): void
    {
        if ($levelCode != '') {
            $this->level = Level::where('code', $levelCode)->first();
            $this->details['level_id'] = $this->level->id;
            $this->details['level_code'] = $this->level->code;
            $this->details['level_name'] = $this->level->name;
        }
    }

    /**
     * Set the position ID for the details.
     *
     * @param  string  $positionCode  The ID of the position.
     */
    #[On('set-position')]
    public function setPosition(string $positionCode): void
    {
        if ($positionCode != '') {
            $this->position = Position::where('code', $positionCode)->first();
            $this->details['position_id'] = $this->position->id;
            $this->details['position_code'] = $this->position->code;
            $this->details['position_name'] = $this->position->name;
        }
    }

    /**
     * The default data for the form.
     */
    public function userData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password ?? 'beautyworld',
        ];
    }

    /**
     * Returns an array containing the company ID, branch ID, and user ID of the given user.
     *
     * @param  User  $user  The user object to retrieve the details from.
     * @return array An array with keys 'company_id', 'branch_id', and 'user_id', each containing the respective ID.
     */
    public function userDetailData(User $user): array
    {
        return [
            'company_id' => $this->company->id,
            'branch_id' => $this->branch->id,
            'company_code' => $this->company->code,
            'company_name' => $this->company->name,
            'branch_code' => $this->branch->code,
            'branch_name' => $this->branch->name,
            'user_id' => $this->user->id,
            'department_id' => $this->details['department_id'],
            'division_id' => $this->details['division_id'],
            'sub_division_id' => $this->details['sub_division_id'],
            'position_id' => $this->details['position_id'],
            'level_id' => $this->details['level_id'],
            'nik' => $this->details['nik'],
            'first_name' => $this->details['first_name'],
            'last_name' => $this->details['last_name'],
            'phone' => $this->details['phone'],
            'address' => $this->details['address'],
            'gender' => $this->details['gender'],
            'religion' => $this->details['religion'],
            'last_education' => $this->details['last_education'],
            'marriage_status' => $this->details['marriage_status'],
            'place_of_birth' => $this->details['place_of_birth'],
            'date_of_birth' => $this->details['date_of_birth'],
            'ktp' => $this->details['ktp'],
            'npwp' => $this->details['npwp'],
            'bank_account' => $this->details['bank_account'],
            'date_in' => $this->details['date_in'],
            'date_out' => $this->details['date_out'],
            'note' => $this->details['note'],
        ];
    }

    /**
     * Saves the user details to the database and dispatches a 'user-created' event.
     */
    public function save(): void
    {
        if (! $this->company) {
            $this->addError('errorMessage', 'Please Select Company First');

            return;
        }

        if (! $this->branch) {
            $this->addError('errorMessage', 'Please Select Branch First');

            return;
        }

        $this->validate();

        DB::transaction(function () {
            $this->user = User::create(self::userData());
            UserDetail::create(self::UserDetailData($this->user));
        }, 5);

        $this->dispatch('hide-form');
        $this->dispatch('refresh');
    }

    /**
     * Edit the user details.
     */
    #[On('edit')]
    public function edit($name): void
    {
        $this->user = User::where('name', $name)->first();
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->details = $this->user->details->toArray();
        
        $this->company = Company::where('code', $this->details['company_code'])->first();
        $this->companyCode = $this->company->code;
        $this->dispatch('set-company', $this->companyCode);

        $this->branch = Branch::where('code', $this->details['branch_code'])->first();
        $this->branchCode = $this->branch->code;
        $this->dispatch('set-branch', $this->branchCode);

        $this->department = Department::find($this->details['department_id']);
        $this->departmentCode = $this->department->code;
        $this->dispatch('set-department', $this->departmentCode);

        $this->division = Division::find($this->details['division_id']);
        $this->divisionCode = $this->division->code;
        $this->dispatch('set-division', $this->divisionCode);

        $this->subDivision = SubDivision::find($this->details['sub_division_id']);
        $this->subDivisionCode = $this->subDivision->code;
        $this->dispatch('set-sub-division', $this->subDivisionCode);

        $this->position = Position::find($this->details['position_id']);
        $this->positionCode = $this->position->code;
        $this->dispatch('set-position', $this->positionCode);

        $this->level = Level::find($this->details['level_id']);
        $this->levelCode = $this->level->code;
        $this->dispatch('set-level', $this->levelCode);

        $this->actionForm = 'update';
        $this->dispatch('show-form');
    }

    /**
     * Updates the user details in the database.
     */
    public function update(): void
    {
        DB::transaction(function () {
            $userData = $this->userData();
            unset($userData['password']);
            if ($this->newPassword) {
                $this->validateOnly('password_confirmation');
                $userData['password'] = bcrypt($this->password);
            }

            $this->user->update($userData);
        }, 5);


        $this->dispatch('hide-form');
        $this->dispatch('refresh');
    }

    /**
     * Deletes the user from the database.
     */
    #[On('delete')]
    public function destroy($name): void
    {
        $this->user = User::where('name', $name)->first();
        $this->user->name = $this->user->name . time(). '-deleted';
        $this->user->delete();

        $this->dispatch('refresh');
    }

    #[On('clear-form')]
    public function clearForm(): void
    {
        $this->resetErrorBag();
        $this->reset();
    }

    /**
     * Render the livewire component.
     */
    public function render(): View
    {
        return view('livewire.user-form');
    }
}