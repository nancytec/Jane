<?php

namespace App\Http\Livewire;

use App\Models\Company;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyContactList extends LiveNotify
{
    use WithPagination;

    public $company;

    protected $listeners = [
        'refreshContactList' => '$refresh',
        'delete'             => 'delete'
    ];

    public $search;
    public $searchResult;

    public function updated(){
        if ($this->search){
            $this->searchResult = Contact::where('company_id', $this->company->id)->where('lastname', 'LIKE', "%{$this->search}%")->orWhere('firstname', 'LIKE', "%{$this->search}%")->get();
        }
    }

    public function mount(){
        $this->company = Company::find(Auth::user()->company_id);
    }

    public function remove($contact_id){
       return $this->confirmDelete('warning', 'Do you really want to delete', 'Press ok to continue', $contact_id);
    }

    public function delete($contact_id){
        $contact = Contact::find($contact_id);

        // Delete the contact transactions
        $contact->transactions->each->delete();

        // Delete contact itself
        $contact->delete();

        $this->emit('refreshContactList');
        return $this->emit('alert', ['type' => 'success', 'message' => 'Contact deleted']);
    }

    public function render()
    {
        if ($this->searchResult && !empty($this->search)){
            return view('livewire.company.components.company-contact-list', [
                'contacts' => $this->searchResult
            ]);
        }else {
            $this->searchResult = false;
            return view('livewire.company.components.company-contact-list', [
                'contacts' => Contact::orderBy('id', 'DESC')->where('company_id', $this->company->id)->paginate(12)
            ]);
        }
    }
}
