<a href="https://github.com/TutoDS"><img src="../../images/daniel-sousa.png" alt="Daniel Sousa" width="100px" /></a>

# Remove Comments (on Blog) from WordPress

> **Native alternative:** Settings > Discussion > "Allow people to submit comments on new posts" only affects new posts; existing ones need Bulk Edit. Neither hides the admin menu, dashboard widget or admin bar item, which this snippet does.

-   **File:** `remove-comments.php`

## What it does

By default, closes comments and pings everywhere, drops `comments`/`trackbacks` support from every post type, redirects away from `edit-comments.php`, and removes the Comments admin menu, admin bar node and dashboard widget.

Set `post_types` in the settings to limit this to specific post types instead. When it's not empty, the snippet only touches those post types: comments stay open and manageable everywhere else, the admin menu, admin bar node and dashboard widget are left alone.

> **WooCommerce caveat:** product reviews are stored as comments. Removing comments from every post type also kills reviews, and so does leaving `post_types` empty. On a WooCommerce shop, set `post_types` to `array( 'post' )` to drop blog comments while keeping reviews manageable through the normal Comments screen.

## Settings

Edit the array in `tds_remove_comments_settings()`:

| Key | Default | What it is |
|---|---|---|
| `post_types` | `array()` | Post types to remove comments from. Empty removes comments everywhere (current behaviour) |

## How to use

You can use this code two ways:
1. Using **[Code Snippets](https://pt.wordpress.org/plugins/code-snippets/)** plugin
2. Add on `functions.php` file (theme folder)
   * Recommend use **child theme** to add modifications.
