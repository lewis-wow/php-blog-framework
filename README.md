# php-blog-framework

Create php ssr markdown blog

## Download

```bash
composer install
```

## Run blog

```bash
composer run-script run
```

## Structure

The only folder you should edit is `/routes`

## Format

Only `.md` and `.css` files are supported, but `Layout.php` is php file with html.

## Template

`Layout.php` file includes `$css` and `$content` variables where are the contents of page stored.
