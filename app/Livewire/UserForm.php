<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Company;
use App\Models\Department;
use App\Models\Division;
use App\Models\Level;
use App\Models\Position;
use App\Models\User;
use App\Models\UserDetail;
use DB;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class UserForm extends Component
{
    public $company;

    public $companyId;

    #[Url(keep: true)]
    public $companyCode;

    public $branch;

    public $branchId;

    #[Url('branchCode', keep: true)]
    public $branchCode;

    #[Validate('required|unique:users|regex:/^\S+$/')]
    public $name;

    #[Validate('required|email|unique:users')]
    public $email;

    public $password;

    #[Validate('same:password')]
    public $password_confirmation;

    public $newPassword = false;

    public $user;

    public $details = [
        'company_id',
        'branch_id',
        'department_id',
        'level_id',
        'position_id',
        'first_name',
        'last_name',
        'phone',
        'address',
        'gender',
        'religion',
        'last_education',
        'marriage_status',
        'place_of_birth',
        'date_of_birth',
        'ktp',
        'npwp',
        'bank_account',
    ];

    public $actionForm = 'save';

    /**
     * Initialize the component by setting the company based on the company code.
     */
    public function mount(): void
    {
        if ($this->companyCode != 'all' && ! empty($this->companyCode)) {
            self::setCompany($this->companyCode);
        }
    }

    /**
     * Set the company based on the company code
     *
     * @param  string  $companyCode  The code of the company.
     */
    #[On('setCompany')]
    public function setCompany(string $companyCode): void
    {
        $this->companyCode = $companyCode;

        if ($companyCode != 'all') {
            $this->company = Company::where('code', $companyCode)->first();
            $this->companyId = $this->company->id;
        }
    }

    /**
     * Set the branch ID for the details.
     *
     * @param  string  $branchCode  The ID of the branch.
     */
    #[On('setBranch')]
    public function setBranch(string $branchCode): void
    {
        $this->branchCode = $branchCode;

        if ($branchCode != 'all') {
            $this->branch = Branch::where('code', $branchCode)->first();
            $this->branchId = $this->branch->id;
        }
    }

    /**
     * @throws ValidationException
     */
    public function updated(string $key, mixed $value): void
    {
        if ($key == 'name') {
            $this->validateOnly($key);
        }
    }

    /**
     * Set the department ID for the details.
     *
     * @param  string  $departmentCode  The ID of the department.
     */
    #[On('setDepartment')]
    public function setDepartment(string $departmentCode): void
    {
        if ($departmentCode != 0) {
            $department = Department::where('code', $departmentCode)->first();
            if (! $department) {
                $this->dispatch('setErrorDepartment');

                return;
            }
            $this->details['department_id'] = $department->id;
        }
    }

    /**
     * Set the division ID for the details.
     *
     * @param  int  $divisionCode  The ID of the division.
     */
    #[On('setDivision')]
    public function setDivision(int $divisionCode): void
    {
        if ($divisionCode != 0) {
            $division = Division::where('code', $divisionCode)->first();

            if (! $division) {
                $this->dispatch('setErrorDivision');

                return;
            }

            $this->details['division_id'] = $division->code;
        }
    }

    /**
     * Set the level ID for the details.
     *
     * @param  string  $levelCode  The ID of the level.
     */
    #[On('setLevel')]
    public function setLevel(string $levelCode): void
    {
        if ($levelCode != 0) {
            $level = Level::where('code', $levelCode)->first();

            if (! $level) {
                $this->dispatch('setErrorLevel');

                return;
            }

            $this->details['level_id'] = $level->id;
        }
    }

    /**
     * Set the position ID for the details.
     *
     * @param  int  $positionCode  The ID of the position.
     */
    #[On('setPosition')]
    public function setPosition(int $positionCode): void
    {
        if ($positionCode != 0) {
            $position = Position::where('code', $positionCode)->first();

            if (! $position) {
                $this->dispatch('setErrorPosition');

                return;
            }

            $this->details['position_id'] = $position->id;
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
            'password' => $this->password,
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
            'company_id' => $this->companyId,
            'branch_id' => $this->branchId,
            'user_id' => $this->user->id,
        ];
    }

    /**
     * Saves the user details to the database and dispatches a 'user-created' event.
     */
    public function save(): void
    {
        if (is_null($this->companyId)) {
            $this->addError('errorMessage', 'Please Select Company First');

            return;
        }

        if (is_null($this->branchId)) {
            $this->addError('errorMessage', 'Please Select Branch First');

            return;
        }

        $this->validate();

        DB::transaction(function () {
            $this->user = User::create(self::userData());
            UserDetail::create(self::UserDetailData($this->user));
        }, 5);

        $this->dispatch('user-created', userId: $this->user->id);

        $this->reset();
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
        $this->details = $this->user->details;

        $this->actionForm = 'update';

        if (! is_null($this->user->details->company_id)) {
            $this->dispatch('selectCompany', $this->user->details->company_id);
        }

        $this->dispatch('disableFilter');
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

        $this->dispatch('user-updated', userId: $this->user->id);

        $this->reset();
    }

    /**
     * Deletes the user from the database.
     */
    #[On('delete')]
    public function destroy($name): void
    {
        $this->user = User::where('name', $name)->first();
        $this->user->delete();

        $this->dispatch('user-deleted', refreshCompanies: true);
    }

    #[On('clearUserForm')]
    public function clearUserForm(): void
    {
        $this->resetErrorBag();
        $this->reset();

        $this->dispatch('hide-form');
    }

    #[On('resetError')]
    public function resetError(): void
    {
        $this->resetErrorBag();
    }

    /**
     * Render the livewire component.
     */
    public function render(): View
    {
        return view('livewire.user-form');
    }
}
