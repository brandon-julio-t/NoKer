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
  - homepage (all blogs w/ infinite scrolling)
  - view detail blog (di sini creator bisa ke update/delete blog)
  - create blog
  - update blog (creator only)
  - search blog (paginated)
  - manage users (paginated, admin only)
  - manage blog queues (paginated, admin only)
- security
  - hashing (login & register)
  - sqli (semua form)
  - csrf (semua form)
  - config htaccess
    - directory listing
    - custom error page
    - pretty url (/home.php jadi /home aja)
  - cors (ketika ajax)
