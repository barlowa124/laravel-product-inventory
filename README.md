# Product inventory

Product submission form backed by a JSON file store. Laravel 12, Bootstrap 5,
vanilla JavaScript for the Ajax calls.

## Requirements

PHP 8.2 or newer with mbstring, openssl, pdo_sqlite, tokenizer, xml, ctype,
json, and fileinfo extensions. All Composer dependencies ship in `vendor/`,
so no `composer install` is needed.

## Run

Point the web server document root at `public/`. If the document root is the
project root instead, the included `.htaccess` and `index.php` forward to
`public/`.

```
php artisan serve
```

Optional sample data:

```
php artisan db:seed
```

## How it works

`GET /` renders the form and products table through Blade. `POST /products`
validates and appends a record. `PUT /products/{id}` edits one. `DELETE
/products/{id}` removes one. All three return JSON that the page uses to
re-render the table without a reload.

Records persist in `storage/app/products.json` as pretty-printed JSON written
under an exclusive lock. Rows sort by `submitted_at` ascending. `total` is
`quantity * price` computed server-side, and the last table row sums every
total. Editing keeps the original `submitted_at`. The id stays stable across
edits.

Bootstrap CSS and JS are local files in `public/vendor/bootstrap/`, so the
page works without a CDN. `.env` is pre-configured with an APP_KEY and a
SQLite database that already has its tables. Sessions, cache, and queue run
on the database driver against that file.
