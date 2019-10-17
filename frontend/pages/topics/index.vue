<template>
  <div class="container">
    <h2>Latest Topics</h2>
    <div v-for="(topic, index) in topics.data" :key="index" class="bg-light mt-5 mb-5" style="padding: 20px;">
      <h2><nuxt-link :to="{name: 'topics-id', params: {id: topic.id}}">{{topic.title}}</nuxt-link></h2>
      <p class="text-muted">{{topic.created_at}} by {{topic.user.name}}</p>
      <div v-for="(content, index) in topic.posts" :key="index" class="ml-5 content pl-10 pr-10">
        {{content.body}}
        <p class="text-muted">{{content.created_at}} by {{content.user.name}}</p>
      </div>
    </div>
    <nav>
      <ul class="pagination justify-content-center">
        <li v-for="(key, value) in topics.links" class="page-item">
          <a href="#" @click="loadMore(key)" class="page-link">{{ value }}</a>
        </li>
      </ul>
    </nav>
<!--    <pre>{{topics}}</pre>-->
  </div>
</template>

<script>
  export default {
      data() {
          return {
              topics: []
          }
      },
      async asyncData({$axios}) {
        let {data, links} = await $axios.get('/topics')
        return {
            topics: data
        }
      },
      methods: {
          async loadMore(key) {
              let {data} =  await this.$axios.$get(key)
              console.log(key)
              console.log(data)

              return this.topics.data = {...this.topics.data, ...data}
          },
      }
  }
</script>

<style>
  .content {
    border-left: 10px solid white;
  }
</style>
