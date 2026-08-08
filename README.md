# FSFPAY Shopify Payment Module (v1.0.0)

Easily integrate FSFPAY secure payment widget into your Shopify store.

## 🚀 Installation
1. Upload all files to your Shopify Custom App (Laravel backend or any PHP host).
2. Add the following lines to your `.env` file:

FSFPAY_API_USERNAME=your_api_username
FSFPAY_API_KEY=your_api_key
FSFPAY_SECRET_KEY=your_secret_key
FSFPAY_CALLBACK_URL=https://yourshop.com/fsfpay/callback

APP_LOCALE=en


3. Configure your Shopify store to redirect payment checkout to:
https://yourapp.com/fsfpay/checkout


4. The widget supports multiple languages (`en`, `es`, `ar`).
Language auto-detects from Shopify store locale or can be set manually in URL:

https://yourapp.com/fsfpay/checkout?lang=es


5. For successful payment, FSFPAY sends a POST callback to your `FSFPAY_CALLBACK_URL`.

---

## ⚙️ Security Notes
- Ensure HTTPS is enabled for your callback URL.
- Validate HMAC signature from FSFPAY response for authenticity.
- Never expose your API keys in frontend code.

---

## 📝 License
Released under the MIT License.
