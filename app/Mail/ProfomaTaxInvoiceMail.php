<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PDF; // Add this to use DomPDF

class ProfomaTaxInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $clients;
    public $sales;
    public $total_discounts;
    public $vat_charges;
    public $grand_total_amount;
    public $labours;
    public $total_labours;
    public $total_sales;
    public $id;
    public $products;
    public $client_name;
    public $vat_calculations;
    public $vehicle;
    public $settings;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public function __construct($clients, $sales, $total_discounts, $vat_charges, $tax, $grand_total_amount, $labours, $total_labours, $total_sales, $id, $products, $client_name, $vat_calculations, $vehicle, $settings, $fname, $lname, $place)
    {
        $this->clients = $clients;
        $this->sales = $sales;
        $this->total_discounts = $total_discounts;
        $this->vat_charges = $vat_charges;
        $this->grand_total_amount = $grand_total_amount;
        $this->labours = $labours;
        $this->total_labours = $total_labours;
        $this->total_sales = $total_sales;
        $this->id = $id;
        $this->products = $products;
        $this->client_name = $client_name;
        $this->vat_calculations = $vat_calculations;
        $this->vehicle = $vehicle;
        $this->settings = $settings;
        $this->tax = $tax;
        $this->fname = $fname;
        $this->lname = $lname;
        $this->place = $place;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Generate the PDF
        $pdf = \PDF::loadView('estimations.invoice-pdf', [
            'sales' => $this->sales,
            'total_discounts' => $this->total_discounts,
            'vat_charges' => $this->vat_charges,
            'grand_total_amount' => $this->grand_total_amount,
            'labours' => $this->labours,
            'total_labours' => $this->total_labours,
            'total_sales' => $this->total_sales,
            'id' => $this->id,
            'products' => $this->products,
            'client_name' => $this->client_name,
            'vat_calculations' => $this->vat_calculations,
            'vehicle' => $this->vehicle,
            'settings' => $this->settings,
        ]);

        $pdfContent = $pdf->output();
        

        // Attach the PDF and send the email
        return $this->from('nduvinitabora@hotmail.com', 'Nduvini AutoWorks Limited')
                    ->subject('Tax Invoice')
                    ->view('tax-mail') // your email view
                    ->attachData($pdfContent, 'Tax-Invoice.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
