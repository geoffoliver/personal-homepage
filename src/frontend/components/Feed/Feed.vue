<template>
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
              <strong>{{post.friend.name}}</strong><br />
              <time>{{ post.isoDate | moment('dddd, MMMM Do YYYY, h:mm:ss a') }}</time>
            </div>
          </div>
          <div class="feed-post-item-content">
            <h3 class="is-size-5"><strong>{{ post.title }}</strong></h3>
            <div v-html="post.contentSnippet"></div>
          </div>
          <div class="feed-post-item-footer">
            ...
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

  time {
    font-size: 0.75rem;
    opacity: 0.75;
  }
}
.feed-post-item-footer {
  border-top: 1px solid #dfdfdf;
  padding-top: 10px;
  margin-top: 10px;
}
</style>

<script>
import Parser from "rss-parser";
const parser = new Parser();

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
          return new Date(a.isoDate) < new Date(b.isoDate);
        })
        .slice(0, this.perPage * this.page);
    }
  },
  methods: {
    loadFeeds: function() {
      this.loading = true;

      let promises = [];

      this.friends.forEach(f => {
        promises.push(new Promise((resolve, reject) => {
          parser.parseURL(`/friends/feed/${f.id}.xml`).then(
            feed => {

              console.log('feed', feed);

              this.posts = this.posts.concat(
                feed.items.map(feedItem => {
                  feedItem.friend  = f;
                  return feedItem;
                })
              );
              resolve(feed);
            },
            error => {
              f.loaded = true;
              reject(error);
            }
          );
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
