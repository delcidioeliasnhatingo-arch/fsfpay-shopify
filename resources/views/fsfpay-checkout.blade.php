<!doctype html>
<html lang="{{ str_replace('_','-', $lang) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>FSFPAY Checkout</title>
  <style>body{font-family:Arial, sans-serif;padding:20px}</style>
</head>
<body>
  <h1>FSFPAY</h1>

  <form id="pay-form">
    <label>Amount: <input name="amount" type="number" step="0.01" value="10.00" required></label>
    <input type="hidden" name="order_id" value="{{ 'order_'.uniqid() }}">
    <input type="hidden" name="lang" value="{{ $lang }}">
    <button type="submit">Pay</button>
  </form>

  <div id="iframe-wrap" style="margin-top:20px; display:none;">
    <iframe id="fsfpay-iframe" src="" style="width:100%; height:700px; border:1px solid #ccc;"></iframe>
  </div>

<script>
document.getElementById('pay-form').addEventListener('submit', async function(e){
  e.preventDefault();
  const form = e.target;
  const body = {
    amount: form.amount.value,
    order_id: form.order_id.value,
    lang: form.lang.value
  };

  const res = await fetch('/fsfpay/create', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(body)
  });

  const json = await res.json();
  if (!res.ok) {
    alert(json.error || 'Payment init error');
    return;
  }

  document.getElementById('fsfpay-iframe').src = json.iframe_src;
  document.getElementById('iframe-wrap').style.display = 'block';
});
</script>
</body>
</html>
