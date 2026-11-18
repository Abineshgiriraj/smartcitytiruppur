@extends('layouts/blankLayout')

@section('title', 'Invoice Proposal')

@section('content')

<style>
  .invoice-container {   background: #fff;        padding: 10px 30px;        font-size: 13px;        line-height: 1.6;        color: #000;    }
  .invoice-table th, .invoice-table td {        border: 1px solid #333;        padding: 3px;        text-align: center;    }
  .invoice-table {        width: 100%;        border-collapse: collapse;    }
  .table-total {        font-weight: bold;        background: #f0f0f0;    }
  ol li {        margin-bottom: 5px;    }
  .invoice-footer {    padding: 20px;    font-size: 14px;    margin-top: 40px;    position: relative;  }
  .invoice-footer .footer-section {    display: flex;    justify-content: space-between;    flex-wrap: wrap;  }
  .footer-left,  .footer-center,  .footer-right {    flex: 1;    min-width: 200px;    margin-bottom: 10px;  }
  .footer-left {    font-weight: bold;  }
  .footer-icon {    width: 20px;    height: 20px;    display: inline-block;    margin-right: 8px;    vertical-align: middle;  }
  .icon-phone {    background: green;    mask: url('data:image/svg+xml;utf8,<svg fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21c1.21.49 2.53.76 3.91.76a1 1 0 011 1v3.5a1 1 0 01-1 1C11.07 22.5 1.5 12.93 1.5 1a1 1 0 011-1H6a1 1 0 011 1c0 1.38.27 2.7.76 3.91a1 1 0 01-.21 1.11l-2.2 2.2z"/></svg>') no-repeat center;   mask-size: contain;
  }
  .icon-email {    background: green;    mask: url('data:image/svg+xml;utf8,<svg fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>') no-repeat center;    mask-size: contain;  }
.icon-location {    background: green;    mask: url('data:image/svg+xml;utf8,<svg fill="white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3. 87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>') no-repeat center;    mask-size: contain;  }
.stamp {    background: url('{{ asset('public/assets/img/stamp.png') }}') no-repeat center;    background-size: contain;    width: 100px;    height: 100px;    margin: 0 auto;  }
.footer-bar-bg { background-color:#fff;}
.footer-bar {    background: url('{{ asset('public/assets/img/foot-bg.png') }}') no-repeat center;    padding: 30px;        width: 85%;    background-position: right;    background-size: cover; color:#000; }
.footer-bar::after {    content: "";    width: 40px;    height: 40px;    background: linear-gradient(45deg, transparent 50%, #65b32e 50%);    margin-left: auto;  }
.invoice-header {  display: flex;    align-items: center;    justify-content: space-between;    margin-bottom: 30px;}
.invoice-header .line {    flex: 1;    height: 3px;    background-color: #4caf50; /* green */}
.invoice-header .logo-center {    padding: 0 10px;}
.invoice-header img {    height: 60px; /* adjust as needed */}
.space{ padding-left: 60px; padding-right: 60px;}
.terms-style{ margin-bottom: 0px;}
.terms-style li{ font-size: 12px; padding-bottom: 0px;}
.term-style-one{ border: 1px solid #c9c9c9;  margin: 5px; padding: 12px 5px;}
.card { box-shadow: 0 2px 6px 0 rgba(255, 255, 255, 0.12);}
@media print {
  body {    margin: 0;    padding: 0;  }
  .print-header,
  .print-footer {    position: fixed;    width: 100%;    left: 0;    z-index: 999;  }
  .print-header {    top: 0;  }
  .print-footer {    bottom: 0;  }
  .invoice-container {    padding-top: 120px;  /* Adjust based on header height */    padding-bottom: 100px; /* Adjust based on footer height */  }
  .print-header img,  .print-footer img {    width: 100%;    height: auto;  }
  /* Avoid page break inside terms or table rows */
  table, tr, td, th, .term-style-one {    page-break-inside: avoid !important;  }
  @page {    margin: 0;  }
}

</style>

<!-- Header (Fixed for print) -->
<div class="print-header">
  <img src="{{ asset('public/assets/img/head-bg.jpg') }}" alt="Ascent Urban Header" style="width: 100%;">
</div>
   
<div class="card invoice-container">
  <div class="space">
    <div class="d-flex justify-content-between">
        <div>
            <strong>Ref # AUR/WOLTERS/2024-25/0325</strong>
        </div>
        <div>
            <strong>Date:</strong> 10-07-2025
        </div>
    </div>
    <div class="mt-4">
        <strong>To,</strong><br>
        M/s. Wolters Kluwer<br>
        Ramanujan IT City, Taramani,<br>
        Chennai – 600113.
    </div>

    <p class="mt-3"><strong>Dear Sir/Madam,</strong><br>
    <strong>Sub:</strong> Proposal for Disposal of OLD IT & Other Electronic Products – Reg</p>

    <p>With reference to the subject, please refer below best offering for the Old IT and other Electronic Products for your kind reference.</p>

    <table class="invoice-table mt-3">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Product</th>
                <th>Condition</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>1</td><td class="text-start">Laptop</td><td>Working</td><td>165</td><td class="text-end">6500</td><td class="text-end">10,72,500</td></tr>
            <tr><td>2</td><td class="text-start">Power Adapter</td><td>Working</td><td>343</td><td class="text-end">10</td><td class="text-end">3,430</td></tr>
            <tr><td>3</td><td class="text-start">Laptop</td><td>Damaged</td><td>8</td><td class="text-end">500</td><td class="text-end">4,000</td></tr>
            <tr><td>4</td><td class="text-start">Monitor</td><td>Working</td><td>267</td><td class="text-end">700</td><td class="text-end">1,86,900</td></tr>
            <tr><td>5</td><td class="text-start">Headphones</td><td>Faulty</td><td>220</td><td class="text-end">10</td><td class="text-end">2,200</td></tr>
            <tr><td>6</td><td class="text-start">TV</td><td>Faulty</td><td>5</td><td class="text-end">500</td><td class="text-end">2,500</td></tr>
            <tr><td>7</td><td class="text-start">Dock</td><td>Damaged</td><td>181</td><td class="text-end">20</td><td class="text-end">3,620</td></tr>
            <tr><td>8</td><td class="text-start">Keyboard</td><td>Faulty</td><td>26</td><td class="text-end">20</td><td class="text-end">520</td></tr>
            <tr><td>9</td><td class="text-start">Mouse</td><td>Faulty</td><td>13</td><td class="text-end">10</td><td class="text-end">130</td></tr>
            <tr><td>10</td><td class="text-start">Mobile</td><td>Faulty</td><td>8</td><td class="text-end">350</td><td class="text-end">2,800</td></tr>
            <tr><td>11</td><td class="text-start">Printer</td><td>Faulty</td><td>2</td><td class="text-end">610</td><td class="text-end">1,220</td></tr>
            <tr><td>12</td><td class="text-start">Admin & General Scrap - Lot</td><td>Scrap</td><td>1 Lot</td><td class="text-end">5340</td><td class="text-end">5,340</td></tr>
            <tr class="table-total">
                <td colspan="5" class="text-end">Total Amount</td>
                <td class="text-end">12,85,160</td>
            </tr>
        </tbody>
    </table>

    <h6 class="mt-4 mb-2"><strong>Terms and Conditions: -</strong></h6>
    <div class="row term-style-one">
      <div class="col-5">
          <ul class="terms-style">
            <li>Price Validity: 30 Days.</li>
            <li>All Prices Including <strong>GST & Duty</strong> Charges.</li>
            <li>Payment will be made <strong>immediately</strong>.</li>
            <li>Will do the Data Wipping & share you the Report.</li>
        </ol>
      </div>
      <div class="col-7">
          <ul class="terms-style">
            <li>Customs & Related Documentation work will be handled by us.</li>
            <li>Transportation will be borne by us.</li>
            <li>We will share the E-waste Certification within 30 days from the date of material pickup.</li>
        </ol>
      </div>
    </div>

    <p class="mt-2 mb-1">Thanking you and looking forward to your valuable purchase order.</p>
    <p class="mt-4 mb-0"><strong>M/s Ascent Urban Recyclers Private Limited</strong></p>

</div>
</div>
<!-- Footer (Fixed for print) -->
<div class="print-footer">
  <img src="{{ asset('public/assets/img/foot-bg.jpg') }}" alt="Ascent Urban Footer" style="width: 100%;">
</div>
@endsection
