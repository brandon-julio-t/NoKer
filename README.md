# NoKer

PHP BP case for NAR22-1

## How to Run

1. Config XAMPP to serve the projectd root directory.

> example

```
#DocumentRoot "C:/xampp/htdocs"
#<Directory "C:/xampp/htdocs">

DocumentRoot "D:\GitHub\NoKer"
<Directory "D:\GitHub\NoKer">
```

2. Create `noker` database.
3. Copy paste and run all sqls located in `/migrations` through `phpmyadmin`.
4. Visit `localhost/migrate` in browser to seed the database.
5. Login as admin using `admin@email.com` and `admin` email password combination.
6. Use the app.

## Ideas

- flow login register kaya user biasa, pakai email password
- blog terdiri dari tulisan dan gambar
- ada validasi file yaitu harus image dan max. 1mb
- role: user (penulis & pembaca) & admin (review & approve tulisan blog sebelum dipublish)
- admin user management, bisa block & unblock user
- flow bikin blog
  1. user bikin blog dan masuk ke blog approval queue
  2. admin review blog yg ada di blog approval queue
  3. kalau reject brrti ga bisa di-view public, kalau approved berarti bisa di-view public
- pages
  [x] homepage (all blogs w/ infinite scrolling)
  [x] view detail blog (di sini creator bisa ke update/delete blog)
  [x] create blog
  [x] update blog (creator only)
  [x] search blog (paginated)
  [x] manage users (paginated, admin only)
  [x] manage blog queues (paginated, admin only)
- security
  [x] hashing (login & register)
  [x] sqli (semua form)
  [x] csrf (semua form)
  [x] config htaccess
  [x] directory listing
  [x] custom error page
  [x] pretty url (/home.php jadi /home aja)
  [x] cors (ketika ajax)
