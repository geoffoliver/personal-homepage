<template>
  <div class="columns">
    <div class="column is-one-quarter">
      <div class="sidebar sticky-sidebar">
        <div class="box">
          <h3>
            <a href="/friends">
              <i class="fas fa-fw fa-user-friends"></i>
              <strong>My Friends</strong>
            </a>
          </h3>
          <div class="sidebar-thumbnail-grid" v-if="friends.length">
            <div
              v-for="friend in friends"
              :key="friend.id"
              class="friend-icon"
            >
              <a
                target="_blank"
                rel="noopener noreferrer"
                :href="friend.url"
                :title="friend.name"
              >
                <img
                  :src="friend.icon"
                  :alt="`Icon for ${friend.name}`"
                />
              </a>
              <div class="spinner-container" v-if="friend.loading">
                <i class="fas fa-spin fa-spinner" />
              </div>
            </div>
          </div>
          <p v-else>
            You have not setup any friends yet.
          </p>
        </div>
      </div>
    </div>
    <div class="column">
      <div id="feedItems">
        <div v-if="displayedPosts.length === 0 && loading">
          <div class="box">
            <i class="fas fa-spin fa-spinner"></i> Loading Feed...
          </div>
        </div>
        <div v-else>
          <div v-for="post in displayedPosts" :key="post.guid" class="box">
            <div class="feed-post-item">
              <div class="feed-post-item-header">
                <figure class="image is-48x48">
                  <img
                    :src="post.friend.icon"
                    class="is-rounded friend-icon"
                  />
                </figure>
                <div class="feed-post-item-friend-name">
                  <strong>
                    {{post.friend.name}}
                    <span v-if="post.author && post.author.name">
                      &mdash; by {{ post.author.name }}
                    </span>
                  </strong><br />
                  <time>
                    {{ post.date_published | moment('dddd, MMMM Do YYYY, h:mm:ss a') }}
                    <span v-if="post.date_published !== post.date_modified" class="updated-time">
                      (Updated {{ post.date_modified | moment('dddd, MMMM Do YYYY, h:mm:ss a') }})
                    </span>
                  </time>
                </div>
              </div>
              <div class="feed-post-item-content">
                <h3 class="is-size-5">
                  <strong>
                    <a :href="post.url" target="_blank" rel="noopener noreferrer">
                      {{ post.title }}
                    </a>
                  </strong>
                </h3>
                <div v-html="post.summary"></div>
              </div>
              <hr />
              <div class="feed-post-item-footer">
                <nav class="level is-mobile">
                  <div class="level-left">
                    <a class="level-item" aria-title="View Original" :href="post.url" target="_blank" rel="noopener noreferrer">
                      <i class="fas fa-external-link-alt"></i>
                      &nbsp;
                      View Original
                    </a>
                    <a
                      target="_blank"
                      rel="noopener noreferrer"
                      class="level-item"
                      title="View Comments"
                      :href="post._page_feed.comments.url"
                      v-if="post._page_feed && post._page_feed.comments && post._page_feed.comments.url"
                    >
                        <i class="fas fa-comment"></i>
                        &nbsp;
                        {{ post._page_feed.comments.total }} Comments
                    </a>
                  </div>
                </nav>
              </div>
            </div>
          </div>
          <div class="load-more" v-if="displayedPosts.length < posts.length">
            <button
              @click="nextPage()"
              class="button is-link is-full-width"
            >
              Next Page
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style lang="scss" scoped>
.feed-post-item img {
  max-width: 100%;
  height: auto;
}
.feed-post-item-header {
  display: flex;
  flex-direction: row;
  border-bottom: 1px solid #dfdfdf;
  padding-bottom: 10px;
  margin-bottom: 10px;
  align-items: center;
}
.feed-post-item-friend-name {
  padding-left: 15px;
  display: flex;
  flex-direction: column;

  span {
    font-weight: lighter;
    opacity: 0.75;
  }

  time {
    font-size: 0.75rem;
    opacity: 0.75;
  }
}

/*
.feed-post-item-footer {
  border-top: 1px solid #dfdfdf;
  padding-top: 10px;
  margin-top: 10px;
}
*/

.friend-icon {
  position: relative;
}

.spinner-container {
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 24px;
  height: 24px;
  text-align: center;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.8);
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.8);
}
</style>
<script>
import nanoajax from "nanoajax";

export default {
  data: function() {
  return {
      friends: [],
      feeds: [],
      posts: [],
      loading: false,
      perPage: 50,
      page: 1,
    };
  },
  mounted: function() {
    if (window.friends) {
      this.friends = window.friends;
    }

    this.loadFeeds();
  },
  computed: {
    displayedPosts: function() {
      return this.posts.slice()
        .sort((a, b) => {
          return new Date(a.date_modified) < new Date(b.date_modified);
        })
        .slice(0, this.perPage * this.page);
    }
  },
  methods: {
    loadFeeds: function() {
      this.loading = true;

      let promises = [];

      this.friends.forEach(f => {
        f.loading = true;
        promises.push(new Promise((resolve, reject) => {
          nanoajax.ajax({
            url: `/friends/feed/${f.id}.json`,
            responseType: "json"
          }, (code, feed) => {
            if (code !== 200) {
              f.loading = false;
              f.loaded = true;
              reject(error);
              return;
            }

            f.loading = false;
            this.posts = this.posts.concat(
              feed.items.map(feedItem => {
                feedItem.friend = f;
                return feedItem;
              })
            );
            resolve(feed);
          });
        }));
      });

      Promise.all(promises).then(
        () => {
          this.loading = false;
        },
        () => {
          this.loading = false;
        }
      );
    },
    nextPage: function() {
      this.page = this.page + 1;
    }
  }
};
</script>
