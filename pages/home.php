<?php useFlashAlert(); ?>

<div id="blogs-container" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
</div>

<svg id="loading-spinner" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto; display: block; opacity: 0;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
  <path fill="none" stroke="#009ef7" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px">
    <animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate>
  </path>
</svg>

<template id="blog-template">
  <div class="col">
    <a id="blog-link" class="text-reset text-decoration-none">
      <div class="card shadow-sm">
        <img id="blog-image" src="" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
        <div class="card-body">
          <h5 id="blog-title" class="card-title"></h5>
          <p id="blog-content" class="card-text"></p>
          <small class="card-text">
            <span id="blog-creator"></span>
            &bull;
            <span id="blog-created-at"></span>
          </small>
        </div>
      </div>
    </a>
  </div>
</template>

<script>
  const blogsContainer = document.querySelector('#blogs-container');
  const blogTemplate = document.querySelector('#blog-template');
  const loadingSpinner = document.querySelector("#loading-spinner");
  let page = 1;
  let isLoading = false;

  loadingSpinner.style.opacity = '1';
  fetchBlogs().then(buildBlogs).then(() => {
    loadingSpinner.style.opacity = '0';
    page++
  });

  window.onscroll = async () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
      if (isLoading) return;
      isLoading = true;
      loadingSpinner.style.opacity = '1';
      const blogs = await fetchBlogs();
      if (blogs.length) {
        buildBlogs(blogs);
        isLoading = false;
        loadingSpinner.style.opacity = '0';
        page++;
      } else {
        isLoading = false;
        loadingSpinner.style.opacity = '0';
      }
    }
  };

  async function fetchBlogs() {
    const resp = await fetch(`/api/home?page=${page}&q=<?= urlencode(isset($_GET['q']) ? $_GET['q'] : '') ?>`);
    return await resp.json();
  }

  function buildBlogs(blogs) {
    blogs.forEach(blog => {
      const clone = blogTemplate.content.cloneNode(true);
      const link = clone.querySelector('#blog-link');
      const image = clone.querySelector('#blog-image');
      const title = clone.querySelector('#blog-title');
      const content = clone.querySelector('#blog-content');
      const creator = clone.querySelector('#blog-creator');
      const createdAt = clone.querySelector('#blog-created-at');

      link.href = `/blogs?id=${blog.id}`;
      image.src = blog.image_path;
      title.innerText = blog.title;
      content.innerText = blog.content;
      creator.innerText = blog.user.name;
      createdAt.innerText = new Date(blog.created_at).toDateString();

      blogsContainer.appendChild(clone);
    });
  }
</script>
