import Vue from 'vue';
import Feed from './Feed.vue';

Vue.config.productionTip = false

Vue.use(require('vue-moment'));

new Vue({
  render: h => h(Feed)
}).$mount('#myFeed')
