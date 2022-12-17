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

          this.sort_Standard();

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

    sort_Standard(){
      const regex_str = /[^ぁ-んー]/g; // ひらがな以外
      const regex_number = /[^0-9]/g; // 数字以外
      const regex_alf = /[^A-Z]/g; // アルファベット
      this.costumes_list.sort((a, b) => {
        if(a.class_id !== b.class_id){
          // classで並び替え
          return a.class_id - b.class_id;
        }else if(a.kana !== b.kana){
          // kanaで並び替え
          const a_str = a.kana.replace(regex_str, "");
          const b_str = b.kana.replace(regex_str, "");
          let a_number = a.kana.replace(regex_number, "");
          let b_number = b.kana.replace(regex_number, "");
          const a_alf = a.kana.replace(regex_alf, "");
          const b_alf = b.kana.replace(regex_alf, "");

          if(a_str !== b_str){
            let sort_str = a_str;
            if([...b_str].length < [...a_str].length){
              sort_str = b_str;
            } 
            for(let i=0; i < [...sort_str].length; i++){
              if(a_str.codePointAt(i) !== b_str.codePointAt(i)){
                if(a_str.codePointAt(i) > b_str.codePointAt(i)){
                  return 1;
                }else if(a_str.codePointAt(i) < b_str.codePointAt(i)){
                  return -1;
                }
              }          
            }
          }

          if(a_number !== b_number){
            if(!a_number){
              a_number = 0;
            }
            if(!b_number){
              b_number = 0;
            }

            if(parseInt(a_number) > parseInt(b_number)){
              return 1;
            }else if(parseInt(a_number) < parseInt(b_number)){
              return -1;
            }
          }

          if(a_alf !== b_alf){
            if(a_alf.codePointAt(0) > b_alf.codePointAt(0)){
              return 1;
            }else if(a_alf.codePointAt(0) < b_alf.codePointAt(0)){
              return -1;
            }else{
              return 0;
            }
          }
        }
        return 0;
      });
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