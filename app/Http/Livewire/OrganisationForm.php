<?php

namespace App\Http\Livewire;

use App\User;
use App\UserOrganisation;
use Hash;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Organisation;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;

class OrganisationForm extends Component
{
    use WithFileUploads;

    public $org, $address, $website, $phone, $contact_project, $contact_finance;
    public $searchTerm;
    public $currentPage = 1;
    public $edit, $partners;
    public $firstname = [], $partner_email = [];
    public $partner;
    public $roles = [];
    public $new_roles = [];
    public $linkuseractive;
    public $users, $luser;
    public $logo, $filename;

    protected $rules = [
        'org' => 'required',
        'address' => 'required',
        //'website' => 'required',
        //'phone' => 'required',
        //'contact_project' => 'required',
        //'contact_finance' => 'required',
    ];

    public function mount()
    {
        $this->roles = Role::pluck('name')->all();
        $this->users = User::all();
        $this->linkuseractive = false;
    }


    public function editOrg($id)
    {
        $this->edit = Organisation::find($id);
        $this->org = $this->edit->org;
        $this->address = $this->edit->address;
        $this->website = $this->edit->website;
        $this->phone = $this->edit->phone;
        $this->contact_project = $this->edit->contact_project;
        $this->contact_finance = $this->edit->contact_finance;

        $this->firstname = [];
        $this->partner_email = [];
        $this->partners = $this->edit->users()->get();
        foreach ($this->partners as $this->partner) {
            $this->firstname[] = $this->partner->name;
            $this->partner_email[] = $this->partner->email;
        }

    }

    public function partner($id, $index)
    {
        $this->validate([
            'partner_email.*' => 'required|email',
        ]);
        //Update user
        $user = User::find($id);

        $user->name = $this->firstname[$index];
        $user->email = $this->partner_email[$index];
        $user->save();

        //Update user roles
        if($this->new_roles[$index] ?? '') {
            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user->assignRole($this->new_roles[$index]);
        }

        //Refresh user roles
        $this->partners = $this->edit->users()->get();
        session()->flash('message', 'Partner updated Successfully.');
        return back()->withInput();
    }

    public function remove($id)
    {
        $partner = UserOrganisation::where('user_id', $id)->delete();
        //Refresh
        $this->firstname = [];
        $this->partner_email = [];
        $this->partners = $this->edit->users()->get();
        foreach ($this->partners as $this->partner) {
            $this->firstname[] = $this->partner->name;
            $this->partner_email[] = $this->partner->email;
        }
        session()->flash('message', 'Partner updated Successfully.');
        return back()->withInput();
    }
    public function linkuser()
    {

        $this->linkuseractive = true;

    }

    public function updatedluser()
    {
        $org = Organisation::where('org', $this->org)->first();
        UserOrganisation::create([
            'user_id' => $this->luser,
            'organisation_id' => $org->id
        ]);
        $this->linkuseractive = false;

        //Refresh
        $this->firstname = [];
        $this->partner_email = [];
        $this->partners = $this->edit->users()->get();
        foreach ($this->partners as $this->partner) {
            $this->firstname[] = $this->partner->name;
            $this->partner_email[] = $this->partner->email;
        }
        session()->flash('message', 'User linked Successfully.');

    }
    public function newOrg()
    {
        $this->org = '';
        $this->address = '';
        $this->website = '';
        $this->phone = '';
        $this->contact_project = '';
        $this->contact_finance = '';
    }

    public function adduser()
    {
        $org = Organisation::where('org', $this->org)->first();
        //Create user
        $salt = Str::random(8);

        $user = User::create([
            'name' => '',
            'email' => Hash::make($salt),
            'password' => Hash::make($salt),
        ]);
        UserOrganisation::create([
            'user_id' => $user->id,
            'organisation_id' => $org->id,
        ]);
        //Refresh partners
        $this->firstname = [];
        $this->partner_email = [];
        $this->partners = $this->edit->users()->get();
        foreach ($this->partners as $this->partner) {
            $this->firstname[] = $this->partner->name;
            $this->partner_email[] = $this->partner->email;
        }

        session()->flash('message', 'Partner created Successfully.');
        return back()->withInput();
    }

    public function saveOrg() {

        $validatedData = $this->validate();
        $this->filename = $this->logo->store('/storage/logo', 'public');
        Organisation::updateOrCreate(['org' => $this->org],[
            'address' => $this->address,
            'website' => $this->website,
            'phone' => $this->phone,
            'contact_project' => $this->contact_project,
            'contact_finance' => $this->contact_finance,
            'logo' =>  $this->filename,
        ]);

    }

    public function submit()
    {

    }

    public function setPage($url)
    {
        $this->currentPage = explode('page=', $url)[1];
        Paginator::currentPageResolver(function () {
            return $this->currentPage;
        });
    }

    public function render()
    {
        $searchTerm = '%'.$this->searchTerm.'%';
        $organisations = Organisation::where('org', 'like', $searchTerm)->paginate(5);

        return view('livewire.organisation-form', ['organisations' => $organisations]);
    }
}
