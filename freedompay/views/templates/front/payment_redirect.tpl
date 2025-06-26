<!DOCTYPE html>
<html>
<head>
    <title>{l s='Redirecting to payment gateway' mod='freedompay'}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="noindex, nofollow">
    <script type="text/javascript">
        function submitForm() {
            document.getElementById('freedompay_form').submit();
        }
        window.onload = submitForm;
    </script>
    <style>
        body {
            background: #f8f9fa;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .payment-container {
            max-width: 600px;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .loader {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        p {
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .manual-link {
            margin-top: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 14px;
        }
        .manual-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .debug-info {
            margin-top: 20px;
            padding: 15px;
            background: #f1f8ff;
            border-radius: 5px;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <h1>{l s='Redirecting to payment gateway' mod='freedompay'}</h1>
        <p>{l s='Please wait, you will be redirected to the payment page...' mod='freedompay'}</p>
        <div class="loader"></div>
        
        <form id="freedompay_form" action="{$payment_url}" method="post">
            {foreach from=$payment_data key=name item=value}
                <input type="hidden" name="{$name}" value="{$value}">
            {/foreach}
        </form>
        
        <div class="manual-link">
            {l s='If redirection does not happen automatically,' mod='freedompay'}
            <a href="#" onclick="document.getElementById('freedompay_form').submit(); return false;">
                {l s='click here' mod='freedompay'}
            </a>
        </div>
        
        <div class="debug-info">
            <strong>{l s='Debug Information' mod='freedompay'}</strong><br>
            {l s='Payment URL:' mod='freedompay'} {$payment_url}<br>
            {l s='Form Data:' mod='freedompay'}<br>
            <pre>{$payment_data|print_r:true}</pre>
        </div>
    </div>
</body>
</html>