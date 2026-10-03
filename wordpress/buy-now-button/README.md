<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# Buy Now Button for WooCommerce

Adds a "Buy now" button next to the add to cart button on the single product page: one click adds the product to the cart and sends the customer straight to checkout.

-   **File:** `buy-now-button.php`

## What it does

* Adds a second submit button right after the add to cart button, using the same form so the chosen variation and quantity are sent with it.
* The button's `formaction` carries a `tds_buy_now` flag; `woocommerce_add_to_cart_redirect` sends the customer to checkout only when that flag is present, so the normal add to cart button still goes to the cart or stays on the page as usual.
* Skipped for external and grouped products, since neither adds to the cart directly.
* On variable products, the button starts disabled and only enables once a valid, in-stock, purchasable variation is selected, mirroring the state of the main add to cart button.

## Settings

Edit the array in `tds_buy_now_settings()`:

| Key | Default | What it is |
|---|---|---|
| `label` | `Comprar agora` | Button text |
| `background` | `#1d2327` | Button background colour |
| `hover_background` | `#0f7896` | Button background colour on hover |
| `color` | `#fff` | Button text colour |

## How to use

You can use this code two ways:
1. Using **[WPCode](https://wordpress.org/plugins/insert-headers-and-footers/)** or **[Code Snippets](https://wordpress.org/plugins/code-snippets/)** plugin
2. Add on `functions.php` file (theme folder)
   * Recommend use **child theme** to add modifications.
