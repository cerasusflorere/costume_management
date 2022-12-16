<template>
  <div class="panel">
    <div>
      <div class="checkbox-area--together">
        <input type="radio" id="passo" v-model="season_scene" value="passo">
        <label for="passo">中間公演</label>

        <input type="radio" id="guraduation" v-model="season_scene" value="guraduation">
        <label for="guraduation">卒業公演</label>
      </div>

      <form class="form"  @submit.prevent="register">
        <!-- 登場人物 -->
        <label for="character_attr">登場人物</label>
        <select class="form__item" v-model="selectedAttr" v-on:change="selected">
          <option disabled value="">登場人物属性</option>
          <option v-for="(value, key) in optionCharacters">
            {{ key }}
          </option>
        </select>

        <select class="form__item" v-model="registerForm.character" required>
          <option disabled value="">登場人物一覧</option>
          <option v-if="selectedCharacters" v-for="characters in selectedCharacters"
           v-bind:value="characters.id">
            {{ characters.name }}
          </option>
        </select>

        <!-- 衣装名 -->
        <label for="costume_select">衣装</label>
        <select id="costume_select" class="form__item"  v-model="registerForm.costume" required>
          <option disabled value="">衣装一覧</option>
          <option v-for="costume in optionCostumes" 
            v-bind:value="costume.id">
            {{ costume.name }}
          </option>
        </select>
        <div class="form__button">
          <button type="button" @click="openModal_register()" class="button button--inverse"><i class="fas fa-plus fa-fw"></i>新たな衣装追加</button>
        </div>
        <registerCostume :val="postFlag" v-show="showContent" @close="closeModal_register" />

        <!-- ページ数 -->
        <label for="page">ページ数</label>
        <div class="page-area">
          <small>例) 22, 24-25</small>
          <small>半角</small>
          <span class="checkbox-area--together">
            <label for="all_page">全シーン</label>
            <input type="checkbox" id="all_page" v-model="select_all_page">
          </span>
        </div>
        <input type="text" id="page" class="form__item" v-model="registerForm.pages" :disabled="select_all_page" placeholder="ページ数"></input>
        
        <!-- これで決定か -->
        <div class="checkbox-area--together">
          <label for="decision">これで決定</label>
          <input type="checkbox" id="decision" v-model="registerForm.decision"></input>    
        </div>

        <!-- 使用するか -->
        <div>
          <div v-show="season_tag_scene === 1" class="checkbox-area--together">
            <label for="usage_scene">中間発表での使用</label>
            <input type="checkbox" id="usage_scene" v-model="registerForm.usage"></input>    
          </div>
          <div v-show="season_tag_scene === 2">
            <div class="checkbox-area--together">
              <label for="usage_scene_guradutaion">卒業公演での使用</label>
              <input type="checkbox" id="usage_scene_guradutaion" v-model="registerForm.usage_guraduation" @change="selectGuraduation">
            </div>
            <div v-if="guradutaion_tag" class="checkbox-area--together">
              <input type="radio" id="usage_scene_left" value="usage_left" v-model="registerForm.usage_stage">            
              <label for="usage_scene_left">上手</label>

              <input type="radio" id="usage_scene_right" value="usage_right" v-model="registerForm.usage_stage">
              <label for="usage_scene_right">下手</label>
            </div>
          </div>
        </div>

        <!-- 誰がセットする -->
        <label for="setting">セットする人</label>
        <select id="setting" class="form__item"  v-model="registerForm.setting">
          <option value=0>学生一覧</option>
          <option v-for="student in optionSettings" v-bind:value="student.id">
            {{ student.name }}
          </option>
        </select>
        
        <!-- メモ -->
        <label for="comment_scene">メモ</label>
        <textarea class="form__item" id="comment_scene" v-model="registerForm.comment" placeholder="メモ"></textarea>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse"><i class="fas fa-paper-plane fa-fw"></i>登録</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util'

import registerCostume from './Register_Costume.vue'

export default {
  components: {
    registerCostume
  },
  data() {
    return {
      // 取得するデータ
      characters: [],
      optionCostumes: [],
      optionSettings: [],
      // 連動プルダウン
      selectedAttr: '',
      selectedCharacters: '',
      optionCharacters: null,
      // 全ページ使用するか
      select_all_page: false,
      // 中間公演or卒業公演
      season_scene: null,
      season_tag_scene: null,
      // 卒業公演
      guradutaion_tag: 0,
      // 衣装登録
      showContent: false,
      postFlag: "",
      // 登録内容
      registerForm: {
        character: '',
        costume: '',
        pages: '',
        decision: '',
        usage: '',
        usage_guraduation: 0,
        usage_stage: null,
        setting: '',
        comment: ''
      }
    }
  },
  methods: {
    // 登場人物を取得
    async fetchCharacters () {
      const response = await axios.get('/api/informations/characters');
      
      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.characters = response.data;

      // 区分と登場人物をオブジェクトに変換する
      let sections = new Object();
      this.characters.forEach(function(section){
        sections[section.section] = section.characters
      });
      this.optionCharacters = sections;      
    },

    // 衣装を取得
    async fetchCostumes () {
      const response = await axios.get('/api/costumes');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.optionCostumes = response.data;      
    },

    // 学生一覧を取得
    async fetchSettings () {
      const response = await axios.get('/api/informations/owners');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.optionSettings = response.data;
    },

    // 連動プルダウン
    selected: function(){
      this.selectedCharacters = this.optionCharacters[this.selectedAttr];
    },

    // どちらの公演か取得
    async choicePerformance() {
      let today = new Date();
      const month = today.getMonth()+1;
      const day = today.getDate();

      if(month === 3){
        const year = today.getFullYear();
        const guraduation_day = await this.getDateFromWeek(year, month, 1, 0); // 11月第1日曜日
        if(guraduation_day <= day){          
          this.season_scene = "guraduation";
        }else{
          this.season_scene = "passo";
        }
      }else if(month === 11){
        const year = today.getFullYear();
        const passo_day = await this.getDateFromWeek(year, month, 1, 0); // 11月第1日曜日
        if(passo_day >= day){
          this.season_scene = "passo";
        }else{
          this.season_scene = "guraduation";
        }
      }else if(3 < month && month < 11){
        this.season_scene = "passo";
      }else{
        this.season_scene = "guraduation";
      }
    },

    // 第1日曜日の日付を返す
    async getDateFromWeek(year, month_origin, turn, weekday) {
      const month = month_origin - 1;
      // 月初の日
      const firstDateOfMonth = new Date(year, month, 1);
      // 月初の曜日
      const firstDayOfWeek = firstDateOfMonth.getDay();
 
      // 指定された曜日が最初に出現する日付を求める
      let firstWeekdayDate = null;
      if (firstDayOfWeek == weekday) {
        // 月初の曜日が指定曜日の時
        firstWeekdayDate = new Date(year, month, 1);
      } else if (firstDayOfWeek < weekday) {
        // 月初の曜日 < 指定の曜日の時
        firstWeekdayDate = new Date(year, month, 1 + (weekday - firstDayOfWeek));
      } else if (weekday < firstDayOfWeek) {
        // 指定の曜日 < 月初の曜日の時
        firstWeekdayDate = new Date(year, month, 1 + (7 - (firstDayOfWeek - weekday)));
      }

      // 第○の指定の分だけ日数を足す
      const firstWeekDay = firstWeekdayDate.getDate();
      const specifiedDate = new Date(year, month, firstWeekDay + 7 * (turn - 1)); // yyyy年mm月dd日
      if (specifiedDate.getMonth() != month) {
        return null;
      }
      return firstWeekDay + 7 * (turn - 1);
    },

    // 卒業公演の使用にチェックが付いたか
    selectGuraduation() {
      if(!this.guradutaion_tag){
        this.guradutaion_tag = 1;
      }else{
        this.guradutaion_tag = 0;
        this.registerForm.usage_stage = null;
      }
    },

    // 衣装登録のモーダル表示 
    openModal_register () {
      this.showContent = true;
      this.postFlag = 1;
    },
    // 衣装登録のモーダル非表示
    closeModal_register (){
      this.showContent = false;
    },

    reset() {
      this.selectedAttr = '';
      this.registerForm.character = '';
      this.registerForm.costume = '';
      this.registerForm.pages = '';
      this.registerForm.usage = '';
      this.registerForm.decision = '';
      this.registerForm.usage_guraduation = '';
      this.registerForm.usage_stage = null;
      this.registerForm.setting = '';
      this.registerForm.comment = '';
      this.select_all_page = false;
      this.season_tag_scene = null;
      this.guradutaion_tag = 0;
      this.choicePerformance();
    },

    // first_pageとfinal_pageに分割する
    first_finalDivide(str) {
      return str.split(/-|ー|‐|―|⁻|－|～|—|₋|ｰ|~/);
    },

    // 全角→半角
    hankaku2Zenkaku(str) {
      return str.replace(/[０-９]/g, function(s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
      });

      let pattern_number = /^([0-9]\d*|0)$/; // 0~9の数字かどうか
      const chars = str.split('');
      let sets = '';
      chars.forEach((char, index) => {
        char.replace(/[０-９]/g, function(s) {
          const number = String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
          if(pattern_number.test(number)){
            sets = sets + number;
          }else{
            sets  = 0;
          }
        });
        if(index === chars.length-1){
          return sets;
        }
      });
    },

    // 登録する
    register () {
      if(this.select_all_page){
        this.registerForm.pages = '1-1000';
      }
      // ページを分割
      let first_pages = [];
      let final_pages = [];
      first_pages[0] = 0;
      final_pages[0] = 0;
      if(this.registerForm.pages){
        let pages_before = this.registerForm.pages.split(/,|、|，|\s+/);
        pages_before.forEach(page => {
          page = page.replaceAll(/\s+/g, '');
        });
        let pages_after = pages_before.filter(Boolean);
  
        let pattern = /-|ー|‐|―|⁻|－|～|—|₋|ｰ|~/;
        let pattern_number = /^([0-9]\d*|0)$/; // 0~9の数字かどうか

        pages_after.forEach((page, index) => {
          if(index === 0){
            if ( pattern.test(page) ) {
              let pages = this.first_finalDivide(page);

              const chars_first = pages[0].split('');
              let sets_first = '';
              chars_first.forEach((char, index) => {
                // 一文字ずつになっている
                const number = this.hankaku2Zenkaku(char);
                if(pattern_number.test(number)){
                  sets_first = sets_first + number;
                }else{
                  sets_first  = 0;
                }
              },);

              const chars_final = pages[1].split('');
              let sets_final = '';
              chars_final.forEach((char, index) => {
                // 一文字ずつになっている
                const number = this.hankaku2Zenkaku(char);
                if(pattern_number.test(number)){
                  sets_final = sets_final + number;
                }else{
                  sets_final  = 0;
                }
              });

              if(parseInt(sets_first) > parseInt(sets_final)) {
                sets_final = 0;
              }

              first_pages[index] = (parseInt(sets_first));
              final_pages[index] = (parseInt(sets_final));
            }else{
              const chars_first = page.split('');
              let sets_first = '';
              chars_first.forEach((char, index) => {
                // 一文字ずつになっている
                const number = this.hankaku2Zenkaku(char);
                if(pattern_number.test(number)){
                  sets_first = sets_first + number;
                }else{
                  sets_first  = 0;
                }
              });

              first_pages[index] = (parseInt(sets_first));
              final_pages[index] = (0);
            }
          }else{
            if ( pattern.test(page) ) {
              let pages = this.first_finalDivide(page);
              
              const chars_first = pages[0].split('');
              let sets_first = '';
              chars_first.forEach((char, index) => {
                // 一文字ずつになっている
                const number = this.hankaku2Zenkaku(char);
                if(pattern_number.test(number)){
                  sets_first = sets_first + number;
                }else{
                  sets_first  = 0;
                }
              });

              const chars_final = pages[1].split('');
              let sets_final = '';
              chars_final.forEach((char, index) => {
                // 一文字ずつになっている
                const number = this.hankaku2Zenkaku(char);
                if(pattern_number.test(number)){
                  sets_final = sets_final + number;
                }else{
                  sets_final  = 0;
                }
              });

              if(parseInt(sets_first) > parseInt(sets_final)) {
                sets_final = 0;
              }

              first_pages.push(parseInt(sets_first));
              final_pages.push(parseInt(sets_final));
            }else{
              const chars_first = page.split('');
              let sets_first = '';
              chars_first.forEach((char, index) => {
                // 一文字ずつになっている
                const number = this.hankaku2Zenkaku(char);
                if(pattern_number.test(number)){
                  sets_first = sets_first + number;
                }else{
                  sets_first  = 0;
                }
              });

              first_pages.push(parseInt(sets_first));
              final_pages.push(0);
            }
          }          
        });
      }
      
      let usage_left = 0;
      let usage_right = 0;
      if(this.registerForm.usage_stage === "usage_left"){
        usage_left = 1;
      }else if(this.registerForm.usage_stage ==="usage_right"){
        usage_right = 1;
      }
      let last_flag = false;

      if(this.registerForm.setting === 0){
        this.registerForm.setting = '';
      }

      first_pages.forEach(async function(page, index) {
        const response = await axios.post('/api/scenes', {
          character_id: this.registerForm.character,
          costume_id: this.registerForm.costume,
          first_page: page,
          final_page: final_pages[index],
          decision: this.registerForm.decision,
          usage: this.registerForm.usage,
          usage_guraduation: this.registerForm.usage_guraduation,
          usage_left: usage_left,
          usage_right: usage_right,
          setting_id: this.registerForm.setting,
          memo: this.registerForm.comment
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }
  
        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        if(index === first_pages.length-1){
          const costume = this.registerForm.costume;
          const usage = this.registerForm.usage;
          const usage_guraduation = this.registerForm.usage_guraduation;
          // 諸々データ削除
          this.reset();

          // メッセージ登録
          this.$store.commit('message/setContent', {
            content: '使用シーンが投稿されました！',
            timeout: 6000
          });

          // 要検討
          if(usage || usage_guraduation){
            // 衣装の使用有無変更
            if(usage){
              // 中間発表で使用
              const response_costume = axios.post('/api/costumes/'+ costume, {
                method: 'usage_change',
                usage: usage
              });

              if (response_costume.status === 422) {
                this.errors.error = response.data.errors;
                return false;
              }

              if (response_costume.statusText !== 204) {
                this.$store.commit('error/setCode', response.status);
                return false;
              }
            }

            if(usage_guraduation){
              if(usage_left){
                // 上手で使用
                const response_costume = axios.post('/api/costumes/'+ costume, {
                  method: 'usage_left_change',
                  usage_guraduation: usage_guraduation,
                  usage_left: usage_left
                });
  
                if (response_costume.status === 422) {
                  this.errors.error = response.data.errors;
                  return false;
                }
 
                if (response_costume.status !== 204) {
                  this.$store.commit('error/setCode', response.status);
                  return false;
                }
              }else if(usage_right){
                // 下手で使用
                const response_costume = axios.post('/api/costumes/'+ costume, {
                  method: 'usage_right_change',
                  usage_guraduation: usage_guraduation,
                  usage_right: usage_right
                });

                if (response_costume.status === 422) {
                  this.errors.error = response.data.errors;
                  return false;
                }

                if (response_costume.statusText !== 204) {
                  this.$store.commit('error/setCode', response.status);
                  return false;
                }
              }else{
                // とりあえず卒業公演で使用
                const response_costume = axios.post('/api/costumes/'+ costume, {
                  method: 'usage_guraduation_change',
                  usage_guraduation: usage_guraduation
                });

                if (response_costume.status === 422) {
                  this.errors.error = response.data.errors;
                  return false;
                }
  
                if (response_costume.statusText !== 204) {
                  this.$store.commit('error/setCode', response.status);
                  return false;
                }
              }
            }
          }
        } 
      }, this);
    }
  },
  watch: {
    $route: {
      async handler () {
        await this.fetchCharacters();
        await this.fetchCostumes();
        await this.fetchSettings();
        await this.choicePerformance();
      },
      immediate: true
    },
    season_scene: {
      async handler(season_scene) {
        if(this.season_scene === "passo"){
          this.season_tag_scene = 1;
        }else if(this.season_scene === "guraduation"){
          this.season_tag_scene = 2;
        }
      },
      immediate: true
    }
  }
}
</script>