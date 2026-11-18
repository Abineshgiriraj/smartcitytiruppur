@extends('layouts/contentNavbarLayout')

@section('title', 'Vertical Layouts - Authentication')

@section('content')
<style>
    /* Include your CSS styling */
    body {
        background: #f2f4f7;
        color: #28333b;
        font-family: 'DM Sans', sans-serif;
        font-size: 1em;
        padding: 0px 25px;
    }

    a {
        color: #28333b;
        text-decoration: none;
        /* border-bottom: 2px solid rgba(64, 179, 255, 0.5); */
        opacity: 0.75;
        transition: all 0.5s ease;
    }

    a:hover {
        /* border-bottom: 2px solid rgba(64, 179, 255, 1); */
        opacity: 1;
    }

    .field {
        margin-bottom: 25px;
    }

    .field.full {
        width: 100%;
    }

    .field.half {
        width: calc(50% - 12px);
    }

    label {
        display: block;
        text-transform: uppercase;
        font-size: 12px;
        margin-bottom: 8px;
    }

    input {
        padding: 12px;
        border-radius: 6px;
        border: 2px solid #e8ebed;
        display: block;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
    }

    input::placeholder {
        color: #e8ebed !important;
    }

    .flex {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        align-items: center;
    }

    .flex.justify-space-between {
        justify-content: space-between;
    }

    .card {
        padding: 50px;
        margin: 50px auto;
        max-width: 850px;
        background: #ffffff;
        border-radius: 6px;
        box-sizing: border-box;
        box-shadow: 0px 24px 60px -1px rgba(37, 44, 54, 0.14);
    }

    .container {
        max-width: 700px;
        margin: 0 auto;
    }

    .card-title {
        margin-bottom: 50px;
    }

    .card-title h2 {
        margin: 0;
    }

    .card-body .payment-type,
    .card-body .payment-info {
        margin-bottom: 25px;
    }

    .payment-type h4 {
        margin: 0;
    }

    .types {
        margin: 25px 0;
    }

    .type {
        width: 30%;
        position: relative;
        background: #f2f4f7;
        border: 2px solid #e8ebed;
        padding: 17px;
        box-sizing: border-box;
        border-radius: 6px;
        cursor: pointer;
        text-align: center;
        transition: all 0.5s ease;
    }

    #icons {
        font-size: 56px;
    }

    .type:hover {
        border-color: #28333b;
    }

    .type:hover .logo,
    .type:hover p {
        color: #28333b;
    }

    .type.selected {
        border-color: #40b3ff;
        background: rgba(64, 179, 255, 0.1);
    }

    .type.selected .logo {
        color: #40b3ff;
    }

    .type.selected p {
        color: #28333b;
    }

    .type.selected::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        height: 40px;
        width: 40px;
        top: -21px;
        right: -21px;
        background: #fff;
        /* border: 2px solid #40b3ff; */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo {
        /* font-size: 48px; */
        color: #8a959c;
        transition: all 0.5s ease;
    }

    p {
        margin-bottom: 0;
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #8a959c;
    }

    .payment-info .column {
        width: calc(50% - 25px);
    }

    .payment-info .title {
        display: flex;
        flex-direction: row;
        align-items: center;
    }

    .payment-info .num {
        height: 24px;
        width: 24px;
        border-radius: 50%;
        border: 2px solid #40b3ff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        margin-right: 12px;
        font-size: 12px;
    }

    footer {
        margin: 50px auto;
        max-width: 850px;
        text-align: center;
    }

    .button {
        text-transform: uppercase;
        font-weight: 600;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 15px 25px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.5s ease;
        background: transparent;
        border: 2px solid transparent;
    }

    .button-primary {
        background: #40b3ff;
        color: #fff;
    }

    .button-primary:hover {
        background: #218fd9;
    }

    .button-secondary {
        background: transparent;
        /* border-color: #e8ebed; */
        color: #8a959c;
    }

    .button-secondary:hover {
        border-color: #28333b;
        color: #28333b;
    }
</style>

<article class="card">
    <div class="container">
        <div class="card-title">
            <h2>Payment</h2>
        </div>
        <div class="card-body">
            <div class="payment-type">
                <h4>Choose payment method below</h4>
                <div class="types flex justify-space-between">
                    <div class="type selected">
                        <div class="logo">
                            <!-- <i class="far fa-credit-card"></i> -->
                            <i id="icons" class='bx bx-credit-card'></i>
                        </div>
                        <div class="text">
                            <p>Pay with Credit Card</p>
                        </div>
                    </div>
                    <div class="type">
                        <div class="logo">
                            <i id="icons" class='bx bxl-paypal'></i>
                        </div>
                        <div class="text">
                            <p>Pay with PayPal</p>
                        </div>
                    </div>
                    <div class="type">
                        <div class="logo">
                            <!-- <i class="fab fa-amazon"></i> -->
                            <i id="icons" class='bx bxl-amazon'></i>
                        </div>
                        <div class="text">
                            <p>Pay with Amazon</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="payment-info flex justify-space-between">
                <div class="column billing">
                    <div class="title">
                        <div class="num">1</div>
                        <h4>Billing Info</h4>
                    </div>
                    <div class="field full">
                        <label for="name">Full Name</label>
                        <input id="name" type="text" placeholder="Full Name">
                    </div>
                    <div class="field full">
                        <label for="address">Billing Address</label>
                        <input id="address" type="text" placeholder="Billing Address">
                    </div>
                    <div class="flex justify-space-between">
                        <div class="field half">
                            <label for="city">City</label>
                            <input id="city" type="text" placeholder="City">
                        </div>
                        <div class="field half">
                            <label for="state">State</label>
                            <input id="state" type="text" placeholder="State">
                        </div>
                    </div>
                    <div class="field full">
                        <label for="zip">Zip</label>
                        <input id="zip" type="text" placeholder="Zip">
                    </div>
                </div>
                <div class="column shipping">
                    <div class="title">
                        <div class="num">2</div>
                        <h4>Credit Card Info</h4>
                    </div>
                    <div class="field full">
                        <label for="card-name">Cardholder Name</label>
                        <input id="card-name" type="text" placeholder="Full Name">
                    </div>
                    <div class="field full">
                        <label for="card-number">Card Number</label>
                        <input id="card-number" type="text" placeholder="1234-5678-9012-3456">
                    </div>
                    <div class="flex justify-space-between">
                        <div class="field half">
                            <label for="exp-month">Exp. Month</label>
                            <input id="exp-month" type="text" placeholder="12">
                        </div>
                        <div class="field half">
                            <label for="exp-year">Exp. Year</label>
                            <input id="exp-year" type="text" placeholder="19">
                        </div>
                    </div>
                    <div class="field full">
                        <label for="cvc">CVC Number</label>
                        <input id="cvc" type="text" placeholder="468">
                    </div>
                </div>
            </div>
        </div>
        <div style="float: right;">
            <div style="margin-top: -18px;">
                <button type="button" class="button button-primary" onclick="window.location.href='{{ route('pass_entry') }}'">Proceed</button>
            </div>
        </div>
    </div>
</article>

@endsection