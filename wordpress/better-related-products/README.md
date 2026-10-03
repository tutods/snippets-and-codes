<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# Better Related Products for WooCommerce

Tunes WooCommerce's related products so the list is actually relevant instead of showing near-random products from a shared parent category.

-   **File:** `better-related-products.php`

## What it does

* Sets how many related products show up.
* Relates products by their deepest shared category only, instead of any shared category. A parent category that holds every product (for example "Sale" or "Refurbished") would otherwise make all of them related to each other.
* Can disable relating products by shared tag.
* Can drop out-of-stock products from the related list.

## Settings

Edit the array in `tds_better_related_products_settings()`:

| Key | Default | What it is |
|---|---|---|
| `per_page` | `8` | Number of related products shown |
| `same_leaf_category` | `true` | Relate only by each product's deepest category |
| `relate_by_tag` | `false` | Also relate products that share a tag |
| `hide_out_of_stock` | `true` | Drop out-of-stock products from the related list |

## How to use

You can use this code two ways:
1. Using **[WPCode](https://wordpress.org/plugins/insert-headers-and-footers/)** or **[Code Snippets](https://wordpress.org/plugins/code-snippets/)** plugin
2. Add on `functions.php` file (theme folder)
   * Recommend use **child theme** to add modifications.
