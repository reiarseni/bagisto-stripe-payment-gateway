# Bagisto Stripe Payment Gateway

Bagisto Stripe Payment Gateway is a Laravel module designed for Bagisto ecommerce. It enables secure card payments via Stripe and provides an intuitive admin interface for seamless integration and management of your payment gateway.

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/) 
[![Bagisto](https://img.shields.io/badge/Bagisto-v2.*-informational.svg)](https://bagisto.com/) 
[![Composer](https://img.shields.io/badge/Composer-1.6.5%2B-orange.svg)](https://getcomposer.org/)

---

## 1. Introduction

Install this package now to receive secure payments in your online store. Stripe offers an easy and secure payment gateway  for Bagisto stores.

---

## 2. Requirements

- **PHP**: 8.1 or higher.
- **Bagisto**: v2.*
- **Composer**: 1.6.5 or higher.

---

## 3. Installation

### Installation without Composer

1. **Unzip the Extension:**
   - Unzip the respective extension zip and merge the **packages** and **storage** folders into your project root directory.

2. **Register the Service Provider:**
   - Open `config/app.php` and add the following line under `'providers'`:
     ```php
     Webkul\Stripe\Providers\StripeServiceProvider::class,
     ```

3. **Configure PSR-4 Autoloading:**
   - Open `composer.json` and add the following line under `'psr-4'`:
     ```json
     "Webkul\\Stripe\\": "packages/Webkul/Stripe/src"
     ```

4. **Complete the Setup:**
   ```bash
   composer dump-autoload
   php artisan optimize
   ```

5. **Configure Stripe in your Bagisto Admin Panel:**
   - Navigate to:
     ```
     http://localhost:8000/admin/configuration/sales/payment_methods
     ```
   - Enter your API key, or paste the demo API key into the **Stripe Client Secret** field:
     ```
     sk_test_xxxxxxxxxxxxxxxxxxxxxxx
     ```

> **That's it!** Now execute your project on your specified domain and start receiving secure payments.

---

## How to Contribute

Contributions are always welcome! Whether you have suggestions for design, documentation improvements, new components, code enhancements, additional features, or bug fixes, your input is valuable. For more information, please check our [Contribution Guideline document](https://github.com/reiarseni/bagisto-stripe-payment-gateway/blob/master/CONTRIBUTING.md).
