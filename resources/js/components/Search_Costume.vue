<template>
  <div v-bind:class="[overlay_class === 1 ? 'overlay' : 'overlay overlay-custom']" @click.self="$emit('close', null, null, null)">
    <div class="content cotent-search content-confirm-dialog panel" ref="content_search_costume">
      <!-- 閉じるボタン -->
      <div class="button-search--close">
        <button type="button" @click.self="$emit('close', null, null, null)">×</button>
      </div>
      <form class="form form-search"  @submit.prevent="searchCostume">
        <div class="search-sort-area">
          <span class="search-span"><i class="fas fa-sort fa-fw"></i></i>並び替え</span>
          <div class="checkbox-area--together checkbox-area--search">
            <input type="radio" id="sort_costume_name" v-model="search_costume.costume_sort" value="kana">
            <label for="sort_costume_name">名前順</label>       

            <input type="radio" id="sort_costume_class" v-model="search_costume.costume_sort" value="class">
            <label for="sort_costume_class">分類順</label>

            <input type="radio" id="sort_costume_created_at" v-model="search_costume.costume_sort" value="created_at">
            <label for="sort_costume_created_at">作成日順</label>

            <input type="radio" id="sort_costume_updated_at" v-model="search_costume.costume_sort" value="updated_at">
            <label for="sort_costume_updated_at">更新日順</label>
          </div>
        </div>

        <div class="search-sort-area">
          <span class="search-span"><i class="fas fa-filter fa-fw"></i>絞り込み</span>

          <!-- 入力検索 -->
          <div class="search--select-area--box">
            <label for="search_costume_name" class="search--label">名前</label>
            <input type="text" class="form__item search--input" id="search_costume_name" v-model="search_costume.costume_search.name.input"></input>
            <span class="checkbox-area--together">
              <input type="radio" id="search_costume_name_only" v-model="search_costume.costume_search.name.scope" value="name_only">
              <label for="search_costume_name_only">名前のみ</label>       

              <input type="radio" id="search_costume_memo_toghether" v-model="search_costume.costume_search.name.scope" value="memo_together">
              <label for="search_costume_memo_toghether">メモ含む</label>
            </span>
          </div>

          <!-- 色々分類 -->
          <div class="search--select-area--box">
            <!--衣装分類-->
            <div class="search--select-area">
              <label for="search_costume_class" class="search--label">分類</label>
              <select id="search_costume_class" class="form__item"  v-model="search_costume.costume_search.class">
                <option value=0>選択なし</option>
                <option v-for="classes in optionClasses" v-bind:value="classes.id">
                  {{ classes.class }}
                </option>
              </select>
            </div>
            
            <!-- 色 -->
            <div class="search--select-area serach--select-area-box-colors">
              <label class="search--label">色</label>
              <div class="serach--select-area-colors">
                <div>
                  <label for="search_costume_color_class" class="search--label">色分類</label>
                  <select id="search_costume_color_class" class="form__item" v-model="selectedColor_Class" v-on:change="selected">
                    <option value=0>選択なし</option>
                    <option v-for="(value, key) in optionColors">
                      {{ key }}
                    </option>
                  </select>
                </div>

                <div>
                  <label for="search_costume_color" class="search--label">色名</label>
                  <select id="search_costume_color" class="form__item" v-model="search_costume.costume_search.color">
                    <option value=0>選択なし</option>
                    <option v-if="selectedColors" v-for="color in selectedColors"
                        v-bind:value="color.id">
                      {{ color.color }}
                    </option>
                  </select>
                </div>
              </div>
              
            </div>
            
            <!-- 持ち主 -->
            <div class="search--select-area">
              <label for="search_costume_owner" class="search--label">持ち主</label>
              <select id="search_costume_owner" class="form__item"  v-model="search_costume.costume_search.owner">
                <option value=0>選択なし</option>
                <option v-for="owner in optionOwners" v-bind:value="owner.id">
                  {{ owner.name }}
                </option>
              </select>
            </div>
            
            <!-- ピッコロに持ってきたか -->
            <div class="search-search--select-area checkbox-area--together">
              <label>ピッコロに</label>
              <span class="checkbox-area--together">
                <input type="checkbox" id="search_costume_location" class="form__check__input" v-model="search_costume.costume_search.location">
                <label for="search_costume_location" class="form__check__label">持ってきた</label>
              </span>
              <span class="checkbox-area--together">
                <input type="checkbox" id="search_costume_location_no" class="form__check__input" v-model="search_costume.costume_search.location_no">
                <label for="search_costume_location_no" class="form__check__label">持ってきてない</label>
              </span>
            </div>

            <!-- 使用するか -->
            <div class="search--select-area checkbox-area--together">
              <span class="checkbox-area--together search--select-area--performance">
                <input type="checkbox" id="search_costume_usage" class="form__check__input" v-model="search_costume.costume_search.usage">
                <label for="search_costume_usage" class="form__check__label">中間発表</label>
              </span>

              <span class="search--select-area--performance">
                <span class="checkbox-area--together">
                  <input type="checkbox" id="search_costume_usage_guraduation" class="form__check__input" v-model="search_costume.costume_search.usage_guraduation">
                  <label for="search_costume_usage_guraduation" class="form__check__label">卒業公演</label>
                </span>

                <span class="checkbox-area--together">
                  <input type="checkbox" id="search_costume_usage_left" class="form__check__input" value="left" v-model="search_costume.costume_search.usage_left">
                  <label for="search_costume_usage_left" class="form__check__label">上手</label>
                  
                  <input type="checkbox" id="search_costume_usage_right" class="form__check__input" value="right" v-model="search_costume.costume_search.usage_right"></input>
                  <label for="search_costume_usage_right" class="form__check__label">下手</label>
                </span>
              </span>            
            </div>
          </div>

        </div>
      
        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse"><i class="fas fa-search fa-fw"></i>検索</button>
        </div>
        
      </form>
    </div>
  </div>
</template>

<script>
  import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util';

  export default {
    // モーダルとして表示
    name: 'searchCostume',
    props: {
      postSearch: {
        type: Number,
        required: true
      }
    },
    data() {
      return{
        // overlayのクラス
        overlay_class: 1, // 1の時はつかない
        // 衣装分類リスト
        optionClasses: [],
        // 色
        optionColor_Classes: [],
        selectedColor_Class: '',
        selectedColors: '',
        optionColors: null,
        // 持ち主リスト
        optionOwners: [],
        // 検索
        search_costume: {
          costume_sort: "kana",
          costume_search: {
            name: {
              input: null,
              scope: "name_only"
            },
            class: 0,
            color_class: 0,
            color: 0,
            owner: 0,
            location: false,
            location_no: false,
            usage: false,
            usage_guraduation: false,
            usage_left: false,
            usage_right: false
          }
        }
      }
    },
    watch: {
      postSearch: {
        async handler(postSearch) {
          if(this.postSearch){
            await this.fetchClasses();
            await this.fetchColors();
            await this.fetchOwners();

            const content_dom = this.$refs.content_search_costume;
            const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得

            if(content_rect.top < 0){
              this.overlay_class = 0;
            }else{
              this.overlay_class = 1;
            }
          }        
        },
        immediate: true,
      }
    },
    methods: {
      // 衣装分類を取得
      async fetchClasses () {
        const response = await axios.get('/api/informations/classes');

        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.optionClasses = response.data;
      },

      // 色を取得
      async fetchColors () {
        const response = await axios.get('/api/informations/colors');
      
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.colors = response.data;

        // 色分類と色をオブジェクトに変換する
        let color_classes = new Object();
        this.colors.forEach(function(color_class){
          color_classes[color_class.color_class] = color_class.colors
        });
        this.optionColors = color_classes;      
      },

      // 連動プルダウン
      selected: function(){
        this.selectedColors = this.optionColors[this.selectedColor_Class];
        this.search_costume.costume_search.color_class = this.selectedColor_Class;
      },

      // 持ち主を取得
      async fetchOwners () {
        const response = await axios.get('/api/informations/owners');

        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.optionOwners = response.data;
      },

      // エスケープ処理
      h(unsafeText){
        if(typeof unsafeText !== 'string'){
            return unsafeText;
        }
        return unsafeText.replace(
          /[&'`"<>]/g, 
          function(match) {
            return {
              '&': '&amp;',
              "'": '&#x27;',
              '`': '&#x60;',
              '"': '&quot;',
              '<': '&lt;',
              '>': '&gt;',
            }[match]
          } 
        );
      },

      // 並び替えか絞り込みか // 全部一致か一部一致か
      searchCostume() {
        let name_input = '!=' + 100;
        let name_scope = '!=' + 100;
        let class_id = '!=' + 0;
        let color_id = 'a.color_id !=' + 0;
        let owner_id = '!=' + 0;
        let location = '!=' + 100;
        let usage = '!=' + 100;
        let usage_guraduation = '!=' + 100;
        let usage_left = '!=' + 100;
        let usage_right = '!=' + 100;
        
        if(this.search_costume.costume_search.name.input){
          name_input = '==' + this.h(this.search_costume.costume_search.name.input);
          if(this.search_costume.costume_search.name.scope === "memo_together"){
            name_scope = '==' + this.h(this.search_costume.costume_search.name.input);
          }else{
            name_scope = '!= 100';
          }
        }

        if(this.search_costume.costume_search.class != 0){
          class_id = '===' + this.search_costume.costume_search.class;
        }

        if(this.search_costume.costume_search.color_class != 0 && this.search_costume.costume_search.color == 0){
          // 色のid配列
          let color_ids = this.optionColors[this.search_costume.costume_search.color_class].map((color) => color.id);
          // 色のidで検索文字列
          color_id = '('
          color_ids.forEach((color, index) => {
            color_id = color_id + 'a.color_id === ' + color;
            if(index !== color_ids.length-1){
              color_id = color_id + '||';
            }else{
              color_id = color_id + ')';
            }
          });
        }

        if(this.search_costume.costume_search.color != 0){
          color_id = 'a.color_id ===' + this.search_costume.costume_search.color;
        }

        if(this.search_costume.costume_search.owner != 0){
          owner_id = '===' + this.search_costume.costume_search.owner;
        }

        if(this.search_costume.costume_search.location && !this.search_costume.costume_search.location_no){
          location = '===' + 1;
        }else if(!this.search_costume.costume_search.location && this.search_costume.costume_search.location_no){
          location = '===' + 0;
        }

        if(this.search_costume.costume_search.usage){
          usage = '===' + 1;
        }

        if(this.search_costume.costume_search.usage_guraduation){
          usage_guraduation = '===' + 1;
        }

        if(this.search_costume.costume_search.usage_left){
          usage_left = '===' + 1;
        }

        if(this.search_costume.costume_search.usage_right){
          usage_right = '===' + 1;
        }

        const refine = 'a.class_id' + class_id + '&&' + color_id +'&& a.owner_id' + owner_id +  '&& a.location' + location + '&& a.usage' + usage + '&& a.usage_guraduation' + usage_guraduation + '&& a.usage_left' + usage_left + '&& a.usage_right' + usage_right;

        this.$emit('close', this.search_costume.costume_sort, this.search_costume.costume_search.name, refine);
      },
    }
  }
</script>