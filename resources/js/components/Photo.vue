<template>
  <div class="photo">
    <figure class="photo__wrapper" type="button" @click="openModal_costumeDetail(costume_photo.id)">
      <img
        class="photo__image"
        :src="costume_photo.url"
        :alt="costume_photo.name"
      >
      <div>
        {{ costume_photo.name}}
      </div>
      <div v-if="costume_photo.owner">
        {{ costume_photo.owner.name }}
      </div>
      <div v-if="costume_photo.usage">
        <i class="fas fa-tag"></i>
      </div>
      <div v-if="costume_photo.costume_comments.length">
        <div v-for="memo in costume_photo.costume_comments">
          {{ memo.memo }}
        </div>
      </div>
    </figure>
  </div>
</template>

<script>
import detailCostume from '../components/Detail_Costume.vue'

export default {
  // このページの上で表示するコンポーネント
  components: {
    detailCostume
  },
  props: {
    costume_photo: {
      type: Object,
      required: true
    }
  },
  data() {
    return{
      // 小道具詳細
      showContent: false,
      postCostume: ""
    }
  },
  methods: {
    // 小道具詳細のモーダル表示 
    openModal_costumeDetail (id) {
      this.showContent = true
      this.postCostume = id;
    },
    // 小道具詳細のモーダル非表示
    async closeModal_costumeDetail() {
      this.showContent = false
      await this.fetchCostumes()
    },
  }
}
</script>