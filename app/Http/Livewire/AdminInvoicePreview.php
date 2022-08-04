<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AdminInvoicePreview extends Component
{
    public $invoice;
    public $totalProductTax = 0.00;
    public $totalServiceTax = 0.00;

    public function mount($invoice){
        $this->invoice = $invoice;

        if (count($invoice->products) > 0){
            foreach ($invoice->products as $product){
                $this->totalProductTax =  $this->totalProductTax + $product->total_tax;
            }
        }

        if (count($invoice->services) > 0){
            foreach ($invoice->services as $service){
                $this->totalServiceTax =  $this->totalServiceTax + $service->total_tax;
            }
        }
    }

    public function render()
    {
        return view('livewire.admin.components.admin-invoice-preview');
    }
}
