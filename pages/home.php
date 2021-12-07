<?php useFlashAlert(); ?>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3" x-data="{
    blogs: [],
    isLoading: true,
    page: 1,
    async fetchBlogs() {
      this.isLoading = true;
      const data = await (await fetch(`/api/home?page=${this.page}&q=<?= urlencode(isset($_GET['q']) ? $_GET['q'] : '') ?>`)).json();
      this.isLoading = false;
      this.page++;
      return data;
    },
  }">
  <template x-for="blog in blogs" key="blog.id">
    <div class="col">
      <a :href="`/blogs?id=${blog.id}`" class="text-reset text-decoration-none">
        <div class="card shadow-sm">
          <img :src="blog.image_path" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
          <div class="card-body">
            <h5 class="card-title" x-text="blog.title"></h5>
            <p class="card-text" x-text="blog.content"></p>
            <small class="card-text">
              <span x-text="blog.user.name"></span>
              &bull;
              <span x-text="new Date(blog.created_at).toDateString()"></span>
            </small>
          </div>
        </div>
      </a>
    </div>
  </template>

  <div x-intersect="blogs = [...blogs, ...(await fetchBlogs())];" style="height: 200px; width: 0; margin: 0; padding: 0;"></div>

  <h3 x-cloak x-transition x-show="!isLoading && blogs.length === 0" class="text-center w-100">
    No Blogs...
  </h3>

  <svg x-cloak x-transition x-show="isLoading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
    <path fill="none" stroke="#009ef7" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px">
      <animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate>
    </path>
  </svg>
</div>

<script>
  // const blogsContainer = document.querySelector('#blogs-container');
  // const blogTemplate = document.querySelector('#blog-template');
  // const loadingSpinner = document.querySelector("#loading-spinner");
  // let page = 1;
  // let isLoading = false;

  // loadingSpinner.style.opacity = '1';
  // fetchBlogs().then(buildBlogs).then(() => {
  //   loadingSpinner.style.opacity = '0';
  //   page++
  // });

  // window.onscroll = async () => {
  //   if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
  //     if (isLoading) return;
  //     isLoading = true;
  //     loadingSpinner.style.opacity = '1';
  //     const blogs = await fetchBlogs();
  //     if (blogs.length) {
  //       buildBlogs(blogs);
  //       isLoading = false;
  //       loadingSpinner.style.opacity = '0';
  //       page++;
  //     } else {
  //       isLoading = false;
  //       loadingSpinner.style.opacity = '0';
  //     }
  //   }
  // };

  // async function fetchBlogs() {
  //   const resp = await fetch(`/api/home?page=${page}&q=<?= urlencode(isset($_GET['q']) ? $_GET['q'] : '') ?>`);
  //   return await resp.json();
  // }

  // function buildBlogs(blogs) {
  //   blogs.forEach(blog => {
  //     const clone = blogTemplate.content.cloneNode(true);
  //     const link = clone.querySelector('#blog-link');
  //     const image = clone.querySelector('#blog-image');
  //     const title = clone.querySelector('#blog-title');
  //     const content = clone.querySelector('#blog-content');
  //     const creator = clone.querySelector('#blog-creator');
  //     const createdAt = clone.querySelector('#blog-created-at');

  //     link.href = `/blogs?id=${blog.id}`;
  //     image.src = blog.image_path;
  //     title.innerText = blog.title;
  //     content.innerText = blog.content;
  //     creator.innerText = blog.user.name;
  //     createdAt.innerText = new Date(blog.created_at).toDateString();

  //     blogsContainer.appendChild(clone);
  //   });
  // }
</script>
