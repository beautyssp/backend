<style>
    * {
        font-family: 'sans-serif';
        letter-spacing: .8;
    }

    .watermark img {
        position: fixed;
        bottom: 500;
        left: 0px;
        
        width: 700px;
        opacity: .05;
        
        z-index: -1000;
        transform: rotate(-45deg);
    }

    .body {
        z-index: 2;
    }

    table.header {
        width: 100%;
    }

    table.header h1 {
        font-size: 24px;
    }

    table.header table.data td {
        font-size: 14px;
        padding-right: 15px;
    }

    table.dataClient {
        margin-top: 30px;
    }

    table.products {
        border: 0;
        font-size: 12px;
    }

    table.products tr:first-child {
        width: 100%;
        background-color: black;
        color: white;
    }

    table.products tr th,
    table.products tr td {
        text-align: initial;
        padding: 5px 15px;
    }

    table.products tr:first-child>th.product {
        width: 300px;
    }

    table.products tr:first-child>th.quantity {
        width: 50px;
    }

    table.products tr:first-child>th.price {
        width: 40px;
    }

    table.products tr:nth-child(2n+3) {
        background-color: #ffe3bd;
    }

    table.detail {
        margin-top: 15px;
    }

    table.detail .boxDesc {
        font-size: 12px;
        margin-top: 15px;
        width: 400px;
        padding: 10px 20px;
        background-color: #f0f0f0;
    }

    .footer {
        margin-top: 20px;
        font-size: 12px;
        text-align: center;
        background-color: black;
        color: white;
        padding: 5px 0;
    }
</style>
<div class="watermark">
    <img src="https://api.beautyssp.com/assets/img/logo.png">
</div>
<div class="body">
    <table class="header">
        <tr>
            <td>
                <div>
                    <h1 style="margin-bottom: 10px;">FACTURA DE VENTA</h1>
                    <table class="data">
                        <tr>
                            <td>ID. Factura:</td>
                            <td>#{{ $id }}</td>
                        </tr>
                        <tr>
                            <td>Fecha compra:</td>
                            <td>{{ $date }}</td>
                        </tr>
                        <tr>
                            <td>Fecha impresión:</td>
                            <td>{{ $now }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <img style="float: right;" width="150" src="https://api.beautyssp.com/assets/img/logo.png" alt="">
                <div style="text-align: right">
                    <div style="margin-top:60px;font-size: 14px;line-height:.3">
                        <p>Dirección: av 30 # 1-05</p>
                        <p>+57 312776659</p>
                        <p>danielcaro@gmail.com</p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <table class="dataClient">
        <tr>
            <td>
                <div>
                    <strong>Cliente:</strong>
                </div>
                <div style="font-size: 14px;line-height:.3">
                    <p>{{ $client['name'] }} {{ $client['lastname'] }}</p>
                    <p>Tipo cliente <strong>{{ $client['type_person'] }}</strong></p>
                    <p>CC <strong>{{ $client['number_document'] }}</strong></p>
                    <p>{{ $client['email'] }}</p>
                </div>
            </td>
        </tr>
    </table>
    <table class="products">
        <tr>
            <th class="product">Nombre producto</th>
            <th class="quantity" style="text-align: center;">Cantidad</th>
            <th class="price" style="text-align: center;">Valor (unidad)</th>
            <th class="price" style="text-align: center;">Descuento</th>
            <th class="price" style="text-align: center;">Total</th>
        </tr>
        @foreach ($products as $productSold)
            <tr>
                <td>{{ $productSold['product']['name'] }}</td>
                <td style="text-align:center">{{ $productSold['quantity'] }}</td>
                <td style="text-align:center">${{ $productSold['product']['price'] }}</td>
                <td style="text-align:center">${{ $productSold['discount'] }}</td>
                <td style="text-align:center">${{ $productSold['total'] }}</td>
            </tr>
        @endforeach
    </table>
    <table class="detail">
        <tr>
            <td>
                <label>Nota:</label>
                <div class="boxDesc">
                    {{ $observations == 'null'? 'Sin observaciones' : $observations  }}
                </div>
            </td>
            <td style="padding: 0 0 0 30px;">
                <table style="font-size: 14px;line-height:.9;text-align:right;">
                    <tr>
                        <td style="width: 110px;">Total Bruto</td>
                        <td style="width: 100px;">${{ $totalBruto }}</td>
                    </tr>
                    <tr>
                        <td>Descuentos</td>
                        <td>${{ $discounts }}</td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td>${{ $subtotal }}</td>
                    </tr>
                    <tr>
                        <td>IVA</td>
                        <td>0%</td>
                    </tr>
                    <tr>
                        <td style="font-size: 15px;font-weight:bold;">Total Neto</td>
                        <td style="font-size: 15px;font-weight:bold;">${{ $total }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <div class="footer">
        Gracias por tu compra!
    </div>
    <div style="margin-top: 5px;">
        <span style="font-size: 12px;">Copyright © sistema contable {{$year}}</span>
    </div>
</div>