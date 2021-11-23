# NoKer

PHP BP case for NAR22-1

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
  [ ] search blog (paginated)
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
  [ ] cors (ketika ajax)
