<template>
  <div v-bind:class="[overlay_class === 1 ? 'overlay' : 'overlay overlay-custom']"  @click.self="$emit('close')">
    <div class="content content-confirm-dialog panel" ref="content_list_costumes">
      <ul v-if="costumes_list.length">
        <li v-for="costume in costumes_list">
          <div type="button" class="list-button" @click="openModal_costumeDetail(costume.id)">{{ costume.name }}</div>
        </li>
        <detailCostume :postCostume="postCostume" v-show="showContent" @close="closeModal_costumeDetail" />
      </ul>
      <div v-else>
        衣装は登録されていません。
      </div>
      <button type="button" @click="$emit('close')" class="button button--inverse">閉じる</button>
    </div>
  </div>
  
</template>

<script>
import { OK } from '../util'

import detailCostume from './Detail_Costume.vue'

export default {
  // モーダルとしてこのページを表示
  name: 'listCostumes',
  // このページの上で表示するコンポーネント
  components: {
    detailCostume
  },
  props: {
    postFlag: {
      type: Number,
      required: true
    }
  },
  // データ
  data() {
    return {
      // 衣装リスト
      costumes_list: [],
      // overlayのクラス
      overlay_class: 1, // 1の時はつかない
      // 衣装詳細
      showContent: false,
      postCostume: ""
    }
  },
  watch: {
    postFlag: {
      async handler (postFlag) {
        if(this.postFlag){
          const costumes = await this.fetchCostumes();

          const content_dom = this.$refs.content_list_costumes;
          const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
  
          if(content_rect.top < 0){
            this.overlay_class = 0;
          }else{
            this.overlay_class = 1;
          }
        }
      },
      immediate: true
    }
  },
  methods: {
    // 衣装一覧を取得
    async fetchCostumes () {
      const response = await axios.get('/api/costumes');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }
      
      this.costumes_list = response.data;
    },

    // 衣装詳細のモーダル表示 
    openModal_costumeDetail (id) {
      this.showContent = true;
      this.postCostume = id;
    },
    // 衣装詳細のモーダル非表示
    async closeModal_costumeDetail() {
      this.showContent = false;
      await this.fetchCostumes();
    },
  }
}
</script>