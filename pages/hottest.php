<?php
useAuth();
useFlashAlert();
?>

<div x-data="{
  blogs: [],
  page: 1,
  isLoading: false,
  totalLength: Infinity,
  async fetchHottest() {
    if (this.isLoading) return;
    this.isLoading = true;
    const response = await (await fetch(`/api/hottest?page=${this.page}`)).json();
    this.blogs = [...this.blogs, ...response.data];
    this.totalLength = response.totalCount;
    this.isLoading = false;
    this.page++;
  },
}">
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
    <template x-for="blog in blogs" key="blog.id">
      <div class="col">
        <div x-data @click="location.href = `/blogs?id=${blog.id}`" class="card shadow-sm" style="cursor: pointer;">
          <img :src="blog.image_path" class="card-img-top" alt="Blog image" style="max-height: 150px; object-fit: cover;">
          <div class="card-body">
            <span x-show="blog.is_premium" class="badge bg-primary mb-3">Premium</span>
            <h5 class="card-title" x-text="blog.title"></h5>
            <p class="card-text" x-text="blog.content"></p>
            <small class="card-text">
              <span x-text="blog.user.name"></span>
              &bull;
              <span x-text="new Date(blog.created_at).toDateString()"></span>
            </small>
          </div>
        </div>
      </div>
    </template>
  </div>

  <div class="d-flex justify-content-center">
    <svg x-cloak x-transition x-show="isLoading" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="margin: auto;" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
      <path fill="none" stroke="#009ef7" stroke-width="8" stroke-dasharray="42.76482137044271 42.76482137044271" d="M24.3 30C11.4 30 5 43.3 5 50s6.4 20 19.3 20c19.3 0 32.1-40 51.4-40 C88.6 30 95 43.3 95 50s-6.4 20-19.3 20C56.4 70 43.6 30 24.3 30z" stroke-linecap="round" style="transform:scale(0.8);transform-origin:50px 50px">
        <animate attributeName="stroke-dashoffset" repeatCount="indefinite" dur="1s" keyTimes="0;1" values="0;256.58892822265625"></animate>
      </path>
    </svg>
  </div>

  <h3 x-cloak x-transition x-show="!isLoading && blogs.length === 0" class="text-center w-100 mt-3">
    No blogs yet.
  </h3>

  <div x-show="blogs.length < totalLength" x-intersect="fetchHottest()" style="height: 200px; width: 0;"></div>
</div>
