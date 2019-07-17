<template>
  <div id="feedItems">
    <div v-if="displayedPosts.length === 0 && loading">
      <div class="box">
        <i class="fas fa-spin fa-spinner"></i> Loading Feed...
      </div>
    </div>
    <div v-else v-for="post in displayedPosts" :key="post.guid" class="box">
      <div class="feed-post-item">
        <div class="header">
          <figure class="image is-32x32">
            <img
              :src="post.friend.icon"
              class="is-rounded friend-icon"
            />
          </figure>
          <div class="name">
            <strong>{{post.friend.name}}</strong><br />
            {{ post.isoDate | moment('dddd, MMMM Do YYYY, h:mm:ss a') }}
          </div>
        </div>
        <div class="content">
          <h3 class="is-size-4"><strong>{{ post.title }}</strong></h3>
          <div v-html="post.contentSnippet"></div>
        </div>
        <div class="footer">
          ...
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
.header {
  display: flex;
  flex-direction: row;
  border-bottom: 1px solid #fefefe;
  padding-bottom: 10px;
  margin-bottom: 10px;
}
.name {
  padding-left: 15px;
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
          console.log('promise success');
          this.loading = false;
          // this.displayedPosts = this.posts.slice(0, this.perPage);
        },
        () => {
          console.log('promises failed');
          this.loading = false;
        }
      );
    },
  }
};
</script>
