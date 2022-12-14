<?php

namespace App\Http\Livewire;

use App\Mail\CompanyInvoiceGeneratedMail;
use App\Models\CompanyCatalogue;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\InvoiceCatalogue;
use App\Models\InvoicePaymentMethod;
use App\Models\InvoiceProduct;
use App\Models\InvoiceService;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class CompanyCreateInvoiceForm extends Component
{
    public $to;
    public $worker;

    public $invoice_number;
    public $date_issued;
    public $due_date;
    public $account_number;
    public $account_name;
    public $payment_methods = [];
    public $note;
    public $invoice_note;
    public $status;

    public $product_quantity_error;

    // Product Item selection params
    public $items;
    public $product_item;
    public $product_unit_price;
    public $product_unit_tax;
    public $product_quantity = 1;
    public $product_item_note;
    public $product_total_price = 0;
    public $product_total_price_with_tax = 0;
    // Selected Item
    public $product_selected_item;
    public $product_selected_items = [];


    // Service Item selection params
    public $service_items;
    public $service_item;
    public $service_unit_price;
    public $service_unit_tax;
    public $service_unit;
    public $billing;
    public $service_item_note;
    public $service_total_price = 0;
    public $service_total_price_with_tax = 0;

    public $cycle = 0;
    // Selected Item
    public $service_selected_item;
    public $service_selected_items = [];


    // contacts and workers
    public $contacts;
    public $workers;
    public $products;
    public $services;


    public $company;


    public function updated($field){
       $this->computeProductData();
       $this->computeServiceData();


        $this->validateOnly($field, [
            'to'                => 'required|numeric',
            'worker'            => 'required|numeric',
            'due_date'          => 'required|string|max:255',
            'payment_methods'   => 'nullable|array',
            'note'              => 'nullable|max:2000',
            'invoice_note'              => 'nullable|string|max:2000',
        ]);
    }

    public function mount(){
        $this->fetchUsersData();
        $this->company = Auth::user()->company;
    }

    public function addProductItem(){
        // Validate inputs
        $checkInput = $this->validateProductItemInputs();
        if ($checkInput['errorCode'] != 'SUCCESS'){
            return $this->emit('alert', ['type' => 'error', 'message' => $checkInput['errorCode']]);
        }

        $item = [
          'id'                      => $this->product_selected_item->id,
          'name'                    => $this->product_selected_item->name,
          'quantity'                => $this->product_quantity,
          'unit_price'              => $this->product_unit_price,
          'unit_tax'                => ($this->product_unit_tax / 100) * ($this->product_unit_price * $this->product_quantity),
          'total_price'             => $this->product_total_price,
          'total_price_with_tax'    => $this->product_total_price_with_tax,
          'note'                    => $this->product_item_note
        ];

        $productIsPresent = false;
        if (count($this->product_selected_items) > 0){
            foreach ($this->product_selected_items as $p_item){
                if ($this->product_selected_item->name == $p_item['name']){
                    $productIsPresent = true;
                }
            }
        }
        if ($productIsPresent){
            // Add to the existing product
            return $this->emit('alert', ['type' => 'error', 'message' => 'You have already selected this product']);
        }
        array_push($this->product_selected_items, $item);

        // Clear the input
        $this->product_item        = '';
        $this->product_item_note   = '';
        $this->product_quantity    = 1;
        $this->product_unit_price  = '';
        $this->product_total_price = 0;
        $this->product_total_price_with_tax = 0;
        $this->product_unit_tax    = 0.00;
    }

    public function validateProductItemInputs(){
        // Check if an item is selected
        if (!$this->product_item){
            return [
                'errorCode' => 'Please select an item'
            ];
        }

        // Check f quantity is less that one
        if (!$this->product_quantity || $this->product_quantity < 1){
            return [
                'errorCode'   => 'Please select a valid qantity'
            ];
        }

        if (!$this->product_item_note){
            return [
                'errorCode'   => 'Please add a description to the item'
            ];
        }

        return [
            'errorCode' => 'SUCCESS'
        ];
    }

    public function computeProductData(){
        // Compute item unit price
        if ($this->product_item && $this->product_quantity > 0){
            $this->product_selected_item = CompanyCatalogue::find($this->product_item);
            // Check for quantity && availability
            if ($this->product_quantity > $this->product_selected_item->quantity){
                $this->product_quantity_error = true;
                $this->product_quantity = $this->product_selected_item->quantity;
                $this->product_total_price = $this->product_selected_item->price * $this->product_quantity;
                $this->product_unit_price = $this->product_selected_item->price;
                return $this->emit('alert', ['type' => 'warning', 'message' => 'The quantity selected is more than available product']);
            }

            $this->product_quantity_error = false;
            $this->product_total_price = $this->product_selected_item->price * $this->product_quantity;

            // Compute product tax
            if ($this->product_selected_item->tax){
                $this->product_unit_tax             = $this->product_selected_item->tax->percentage;
                $this->product_total_price_with_tax =  (($this->product_selected_item->tax->percentage / 100) * ($this->product_selected_item->price * $this->product_quantity)) + $this->product_total_price;
            }else{
                $this->product_unit_tax = 0.00;
                $this->product_total_price_with_tax = $this->product_selected_item->price * $this->product_quantity;
            }

            $this->product_unit_price = $this->product_selected_item->price;
        }
    }


    public function addServiceItem(){
        // Validate inputs
        $checkInput = $this->validateServiceItemInputs();
        if ($checkInput['errorCode'] != 'SUCCESS'){
            return $this->emit('alert', ['type' => 'error', 'message' => $checkInput['errorCode']]);
        }

        $item = [
            'id'                      => $this->service_selected_item->id,
            'name'                    => $this->service_selected_item->name,
            'unit_price'              => $this->service_unit_price,
            'unit_tax'                => ($this->service_unit_tax / 100) * $this->service_unit_price,
            'total_price'             => $this->service_total_price,
            'total_price_with_tax'    => $this->service_total_price_with_tax,
            'cycle'                   => $this->cycle,
            'note'                    => $this->service_item_note
        ];

        $serviceIsPresent = false;
        if (count($this->service_selected_items) > 0){
            foreach ($this->service_selected_items as $s_item){
                if ($this->service_selected_item->name == $s_item['name']){
                    $serviceIsPresent = true;
                }
            }
        }
        if ($serviceIsPresent){
            // Add to the existing product
            return $this->emit('alert', ['type' => 'error', 'message' => 'You have already selected this service']);
        }
        array_push($this->service_selected_items, $item);

        // Clear the input
        $this->service_item        = '';
        $this->service_item_note   = '';
        $this->service_unit_price  = '';
        $this->cycle               = '';
        $this->service_total_price = 0;
    }

    public function validateServiceItemInputs(){
        // Check if an item is selected
        if (!$this->service_item){
            return [
                'errorCode' => 'Please select a service'
            ];
        }


        if (!$this->service_item_note){
            return [
                'errorCode'   => 'Please add a description to the service'
            ];
        }

        return [
            'errorCode' => 'SUCCESS'
        ];
    }

    public function computeServiceData(){
        // Compute item unit price

        if ($this->service_item){
            $this->service_selected_item = CompanyCatalogue::find($this->service_item);
            if ($this->service_selected_item->cycle){
                $this->cycle = $this->service_selected_item->cycle->title;
            }else{
                $this->cycle = "Not available";
            }
            $this->service_total_price = $this->service_selected_item->price;

            // Compute product tax
            if ($this->service_selected_item->tax){
                $this->service_unit_tax             = $this->service_selected_item->tax->percentage;
                $this->service_total_price_with_tax =  (($this->service_selected_item->tax->percentage / 100) * ($this->service_selected_item->price)) + $this->service_total_price;
            }else{
                $this->service_unit_tax = 0.00;
                $this->service_total_price_with_tax = $this->service_selected_item->price;
            }


            $this->service_unit_price = $this->service_selected_item->price;
        }
    }


    public function fetchUsersData(){
        $this->contacts  = Contact::where('company_id', Auth::user()->company_id)->get();
        $this->workers   = Worker::where('company_id', Auth::user()->company_id)->get();
        $this->products  = CompanyCatalogue::where('company_id', Auth::user()->company_id)->where('type', 'product')->get();
        $this->services  = CompanyCatalogue::where('company_id', Auth::user()->company_id)->where('type', 'service')->get();

        $this->fetchFormData();
    }

    public function fetchFormData(){
        $this->invoice_number = Str::random(4).''.sprintf("%06d", mt_rand(1, 999999999));
    }

    public function removeProduct($product_index){
        unset($this->product_selected_items[$product_index]);
        $this->product_selected_items = array_values($this->product_selected_items);
        return $this->emit('alert', ['type' => 'success', 'message' => 'Product removed']);
    }

    public function removeService($service_index){
        unset($this->service_selected_items[$service_index]);
        $this->service_selected_items = array_values($this->service_selected_items);
        return $this->emit('alert', ['type' => 'success', 'message' => 'Service removed']);
    }


    public function generateInvoice(){
        // Either of the product or service must be selected
        if (count($this->product_selected_items) < 1 && count($this->service_selected_items) < 1){
            return $this->emit('alert', ['type' => 'error', 'message' => 'You have to select at least one product or service']);
        }

        $this->validate([
            'to'                => 'required|numeric',
            'worker'            => 'required|numeric',
            'due_date'          => 'required|max:255',
            'payment_methods'   => 'nullable|array',
            'note'              => 'nullable|max:2000',
            'invoice_note'      => 'nullable|string|max:2000',
        ]);

        // Calculate total price for products and services
        $productTotalPrice = 0;
        if (count($this->product_selected_items) > 0){
            foreach ($this->product_selected_items as $p_item){
                $productTotalPrice        = $productTotalPrice + $p_item['total_price'];
            }
        }
        $serviceTotalPrice = 0;
        if (count($this->service_selected_items) > 0){
            foreach ($this->service_selected_items as $s_item){
                $serviceTotalPrice        = $serviceTotalPrice + $s_item['total_price'];
            }
        }

        // Create the invoice
        $invoice = Invoice::create([
            'invoice_code'      => $this->invoice_number,
            'company_id'        => Auth::user()->company_id,
            'contact_id'        => $this->to,
            'worker_id'         => $this->worker,
            'creator_id'        => Auth::user()->id, // User_id not worker id

            'date_issued'       => Carbon::now(),
            'due_date'          => $this->due_date,

            'products_total_price'      => $productTotalPrice,
            'services_total_price'      => $serviceTotalPrice,

            'note'                      => $this->invoice_note,

            'status'                    => $this->status,

            'signature_code'            => Str::random(40)
        ]);

        // create payment methods table
        if (count($this->payment_methods) > 0){
            foreach ($this->payment_methods as $method){
                InvoicePaymentMethod::create([
                    'invoice_id'    => $invoice->id,
                    'method'        => $method
                ]);
            }
        }

        // Create invoice products
        if (count($this->product_selected_items) > 0){
            foreach ($this->product_selected_items as $p_item) {
                $productTotalTax = 0;
                $productTotalTaxWithPrice = 0;

                $productTotalTax          = $productTotalTax + $p_item['unit_tax'];
                $productTotalTaxWithPrice = $p_item['total_price'] + $productTotalTax;


                InvoiceCatalogue::create([
                    'invoice_id'            => $invoice->id,
                    'catalogue_id'          => $p_item['id'],
                    'quantity'              => $p_item['quantity'],
                    'unit_price'            => $p_item['unit_price'],
                    'total_price'           => $p_item['total_price'],
                    'total_price_with_tax'  => $productTotalTaxWithPrice,
                    'total_tax'             => $productTotalTax,
                    'description'           => $p_item['note'],
                    'type'                  => 'product'
                ]);
            }
        }

        // Create invoice services
        if (count($this->service_selected_items) > 0){
            foreach ($this->service_selected_items as $s_item) {
                $serviceTotalTax = 0;
                $serviceTotalTaxWithPrice = 0;

                $serviceTotalTax          = $serviceTotalTax + $s_item['unit_tax'];
                $serviceTotalTaxWithPrice = $s_item['total_price'] + $serviceTotalTax;

                InvoiceCatalogue::create([
                    'invoice_id'            => $invoice->id,
                    'catalogue_id'          => $s_item['id'],
                    'unit_price'            => $s_item['unit_price'],
                    'total_price'           => $s_item['total_price'],
                    'total_price_with_tax'  => $serviceTotalTaxWithPrice, // Total price + total tax
                    'total_tax'             => $serviceTotalTax,
                    'description'           => $s_item['note'],
                    'type'                  => 'service'
                ]);
            }
        }

        // Mail the contact if send is selected
        if ($this->status == 'send'){
            $contact = Contact::find($this->to);

            $data = [
                'subject'   => 'INVOICE NOTICE',
                'invoice'   => $invoice,
                'name'      => $contact->user->firstname,
                'due_date'  => $invoice->due_data
            ];

            Mail::to($contact->email)->send(new CompanyInvoiceGeneratedMail($data));
        }

        $this->resetExcept([
            'workers',
            'products',
            'services',
            'contacts',
            'company'
        ]);

        $this->invoice_number = Str::random(4).''.sprintf("%06d", mt_rand(1, 999999999));;
        return $this->emit('alert', ['type' => 'success', 'message' => 'Invoice generated']);
    }




    public function render()
    {
        return view('livewire.company.components.company-create-invoice-form');
    }
}
