<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\CompanyCatalogue;
use App\Models\Setting;
use App\Traits\FileManager;
use Livewire\Component;
use Livewire\WithPagination;

class AdminCompanyCatalogueList extends LiveNotify
{
    use WithPagination;
    use FileManager;

    protected $listeners = [
        'refreshCompanyCatalogueList' => '$refresh',
        'delete'                      => 'delete'
    ];
    public $settings;
    public $company;

    public $search;
    public $searchResult;

    public $order;

    public $categories;
    public $category;

    public function mount($company){
        $this->settings = Setting::first();
        $this->company = $company;
        $this->categories   =   Category::where('company_id', $this->company->id)->get();
    }

    public function updated(){
        if ($this->search){
            if ($this->order){
                if ($this->category){
                    $this->searchResult = CompanyCatalogue::where('company_id', $this->company->id)
                        ->orderBy('price', $this->order)
                        ->where('name', 'LIKE', "%{$this->search}%")
                        ->where('category', $this->category)->get();
                }else{
                    $this->searchResult = CompanyCatalogue::where('company_id', $this->company->id)->orderBy('price', $this->order)->where('name', 'LIKE', "%{$this->search}%")->get();
                }
            }else{
                if ($this->category){

                    $this->searchResult = CompanyCatalogue::where('company_id', $this->company->id)->where('name', 'LIKE', "%{$this->search}%")->where('category', $this->category)->get();
                }else{
                    $this->searchResult = CompanyCatalogue::where('company_id', $this->company->id)->where('name', 'LIKE', "%{$this->search}%")->get();
                }
            }
        }

    }

    public function clearFilter(){
        $this->category = null;
        $this->search = null;
        $this->searchResult = null;
        $this->order    = null;
    }

    public function setOrder($order='ASC'){
        $this->order = $order;
    }

    public function delete($product_id){
        $catalogue =  CompanyCatalogue::find($product_id);


        if (count($catalogue->images) > 0){
            foreach ($catalogue->images as $image){
                // Delete product image
                $this->deleteCatalogueImage($image->image);
                $image->delete();
            }
        }

        $catalogue->delete();

        $this->emit('refreshCatalogueList');
        return $this->emit('alert', ['type' => 'success', 'message' => 'Product removed']);
    }

    public function remove($product_id){
       return $this->confirmDelete('warning', 'Do you really want to delete', 'Press ok to continue', $product_id);
    }

    public function render()
    {
        if ($this->searchResult && !empty($this->search)){
            return view('livewire.admin.components.admin-company-catalogue-list', [
                'catalogues' => $this->searchResult
            ]);
        }else {
            $this->searchResult = false;
            if ($this->category){
                if ($this->order){
                    return view('livewire.admin.components.admin-company-catalogue-list', [
                        'catalogues' => CompanyCatalogue::where('company_id', $this->company->id)->where('category', $this->category)->orderBy('price', $this->order)->paginate(12)
                    ]);
                }else{
                    return view('livewire.admin.components.admin-company-catalogue-list', [
                        'catalogues' => CompanyCatalogue::where('company_id', $this->company->id)->where('category', $this->category)->paginate(12)
                    ]);
                }
            }else{
                return view('livewire.admin.components.admin-company-catalogue-list', [
                    'catalogues' => CompanyCatalogue::where('company_id', $this->company->id)->paginate(12)
                ]);
            }
        }

    }
}
