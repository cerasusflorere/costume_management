<template>
<!-- 登録-使用シーン上ではclass=overay と class=contentを有効にする-->
  <div v-bind:class="[val === 1 ? 'overlay' : '', (overlay_class === 0 && val === 1) ? 'overlay-custom' : '']" @click.self="val === 1 ? $emit('close') : null">
    <div v-bind:class="[val === 1 ? 'content content-confirm-dialog' : '']" class="panel"  ref="content_register_costume">
      <div class="checkbox-area--together">
        <input type="radio" id="costume_passo" v-model="season_costume" value="passo">
        <label for="costume_passo">中間公演</label>       

        <input type="radio" id="costume_guraduation" v-model="season_costume" value="guraduation">
        <label for="costume_guraduation">卒業公演</label>
      </div>

      <form class="form" @submit.prevent="register_costume">
        <!-- エラー表示 -->
        <div class="errors" v-if="errors.error">
         <ul v-if="errors.error.photo">
           <li v-for="msg in errors.error.photo" :key="msg">{{ msg }}</li>
          </ul>
        </div>

        <!-- 衣装名 -->
        <div>
          <label for="costume_input">衣装</label>
          <div class="form__button">
            <button type="button" @click="openModal_listCostumes(1)" class="button button--inverse"><i class="fas fa-list-ul fa-fw"></i>衣装リスト</button>
          </div>
        </div>
         
        <input type="text" class="form__item" id="costume_input" v-model="registerForm.costume" @input="handleNameInput" placeholder="衣装" required>
        <label for="furigana">ふりがな</label>
        <input type="text" name="furigana" id="furigana" v-model="registerForm.kana" class="form__item form__item--furigana" placeholder="ふりがな" required>

        <!-- 衣装分類 -->
        <label for="class">分類</label>
        <select id="class" class="form__item"  v-model="registerForm.class" required>
          <option disabled value="">分類一覧</option>
          <option v-for="classes in optionClasses" v-bind:value="classes.id">
            {{ classes.class }}
          </option>
        </select>

        <!-- 色 -->
        <label for="color_class">色</label>
        <select class="form__item" v-model="selectedColor_Class" v-on:change="selected">
          <option value=0>色分類</option>
          <option v-for="(value, key) in optionColors">
            {{ key }}
          </option>
        </select>

        <select class="form__item" v-model="registerForm.color">
          <option value=0>色一覧</option>
          <option v-if="selectedColors" v-for="color in selectedColors"
           v-bind:value="color.id">
            {{ color.color }}
          </option>
        </select>

        <!-- 所有者 -->
        <label for="owner">持ち主</label>
        <select id="owner" class="form__item"  v-model="registerForm.owner">
          <option value=0>持ち主一覧</option>
          <option v-for="owner in optionOwners" v-bind:value="owner.id">
            {{ owner.name }}
          </option>
        </select>

        <!-- 貸し借りしたか -->
        <div class="checkbox-area--together">
          <label for="costume_lend">貸し借りした</label>
          <input type="checkbox" id="costume_lend" v-model="registerForm.lend"></input>    
        </div>

        <!-- ピッコロにあるか -->
        <div class="checkbox-area--together">
          <label for="costume_location">ピッコロに持ってきた</label>
          <input type="checkbox" id="costume_location" v-model="registerForm.location"></input>    
        </div>

        <!-- 作る必要があるか -->
        <div class="checkbox-area--together">
          <label for="costume_handmade">作る必要がある</label>
          <input type="checkbox" id="costume_handmade" v-model="registerForm.handmade"></input>

          <div class="checkbox-area--together">
            <!-- 作る必要があるなら -->
            <input type="radio" id="costume_handmade_complete" :disabled="!registerForm.handmade" value=1 v-model="registerForm.handmade_complete"></input>
            <label for="costume_handmade_complete">完成</label>

            <input type="radio" id="costume_handmade_progress" :disabled="!registerForm.handmade" value=2 v-model="registerForm.handmade_complete"></input>
            <label for="costume_handmade_progress">仕掛中</label>

            <input type="radio" id="costume_handmade_unfinished" :disabled="!registerForm.handmade" value=3 v-model="registerForm.handmade_complete"></input>
            <label for="costume_handmade_unfinished">未着手</label>
          </div>          
        </div>

        <!-- これで決定か -->
        <div class="checkbox-area--together">
          <label for="costume_decision">これで決定</label>
          <input type="checkbox" id="costume_decision" v-model="registerForm.decision"></input>    
        </div>

        <!-- 使用するか -->
        <div>
          <div v-show="season_tag_costume === 1" class="checkbox-area--together">
            <label for="costume_usage_scene">中間発表での使用</label>
            <input type="checkbox" id="costume_usage_scene" v-model="registerForm.usage_costume"></input>    
          </div>
          <div v-show="season_tag_costume === 2">
            <div class="checkbox-area--together">
              <label for="costume_usage_scene_guraduation">卒業公演での使用</label>
              <input type="checkbox" id="costume_usage_scene_guraduation" v-model="registerForm.usage_guraduation_costume" @change="selectGuraduation_Costume">
            </div>
            <div v-if="guraduation_tag_costume" class="checkbox-area--together">
              <input type="radio" id="costume_usage_scene_left" value="usage_left" v-model="registerForm.usage_stage_costume">            
              <label for="costume_usage_scene_left">上手</label>

              <input type="radio" id="costume_usage_scene_right" value="usage_right" v-model="registerForm.usage_stage_costume">
              <label for="costume_usage_scene_right">下手</label>
            </div>
          </div>
        </div>
     
        <!-- メモ -->
        <label for="comment_costume">メモ</label>
        <textarea class="form__item" id="comment_costume" v-model="registerForm.comment" placeholder="メモ"></textarea>
     
        <!-- 写真 -->
        <label for="photo_input">写真</label>
        <div v-if="errors.photo">{{ errors.photo }}</div>
          <input id="photo_photo" class="form__item" type="file" @change="onFileChange">
          <output class="form__output" v-if="preview">
            <img :src="preview" alt="" style="max-height: 12em">
          </output>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse"><i class="fas fa-paper-plane fa-fw"></i>登録</button>
        </div>
      </form>
      <listCostumes :postFlag="postFlag" v-show="showContent" @close="closeModal_listCostumes" />
      <!-- 登録- 使用シーンでは閉じるボタンを出現させる -->
      <button type="button" v-if="val===1" @click="$emit('close')" class="button button--inverse">閉じる</button>
    </div>
  </div>
</template>

<script>
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util'

// 衣装リスト
import listCostumes from '../components/List_Costumes.vue'
// 予測変換
import VueSuggestInput from 'vue-suggest-input'
import 'vue-suggest-input/dist/vue-suggest-input.css'
// ふりがな
import * as AutoKana from 'vanilla-autokana';

let autokana;

export default {
  // モーダルとして表示
  name: 'registerCostume',
  props: {
    val: {
      required: false,
      type: Number      
    }
  },
  // 表示するコンポーネント
  components: {
    listCostumes
  },
  // データ
  data() {
    return {
      // 衣装分類リスト
      optionClasses: [],
      // 色
      colors: [],
      // 持ち主リスト
      optionOwners: [],
      // 連動プルダウン
      selectedColor_Class: '',
      selectedColors: '',
      optionColors: null,
      // 衣装リスト
      showContent: false,
      postFlag: "",
      // 衣装候補
      costumes: [],
      // 中間公演or卒業公演
      season_costume: null,
      season_tag_costume: null,
      // 卒業公演
      guraduation_tag_costume: 0,
      // 写真プレビュー
      preview: null,
      // overlayのクラス
      overlay_class: 1, // 1のときはつかない
      // エラー
      errors: {
        photo: null,
        error: null,
      },
      // 登録内容
      registerForm: {
        costume: '',
        kana: '',
        class: '',
        color: '',
        owner: '',
        lend: false,
        location: false,
        handmade: false,
        handmade_complete: 1,
        decision: false,
        usage_costume: '',
        usage_guraduation_costume: 0,
        usage_stage_costume: null,
        comment: '',
        // 写真
        photo: ''
      },
      // 登録状態
      loading: false,
      // ユニコード
      first_uni: 9312, // ①
      final_uni: 9331  // ⑳
    }
  },
  mounted() {
    // ふりがなのinput要素のidは省略可能
    // 使用シーン登録時のid=costumeと被るから
    autokana = AutoKana.bind('#costume_input');
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

    // 持ち主を取得
    async fetchOwners () {
      const response = await axios.get('/api/informations/owners');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.optionOwners = response.data;
    },

    // 衣装一覧を取得
    async fetchCostumes () {
      const response = await axios.get('/api/costumes');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.costumes = response.data;
    },

    handleNameInput() {
      this.registerForm.kana = autokana.getFurigana();
    },

    // 連動プルダウン
    selected: function(){
      this.selectedColors = this.optionColors[this.selectedColor_Class];
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
          this.season_costume = "guraduation";
        }else{
          this.season_costume = "passo";
        }
      }else if(month === 11){
        const year = today.getFullYear();
        const passo_day = await this.getDateFromWeek(year, month, 1, 0); // 11月第1日曜日
        if(passo_day >= day){
          this.season_costume = "passo";
        }else{
          this.season_costume = "guraduation";
        }
      }else if(3 < month && month < 11){
        this.season_costume = "passo";
      }else{
        this.season_costume = "guraduation";
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
    selectGuraduation_Costume() {
      if(!this.guraduation_tag_costume){
        this.guraduation_tag_costume = 1;
      }else{
        this.guraduation_tag_costume = 0;
        this.registerForm.usage_stage_costume = null;
      }
    },

    // 衣装リストのモーダル表示 
    openModal_listCostumes (number) {
      this.showContent = true;
      this.postFlag = 1;;
    },
    // 衣装リストのモーダル非表示
    closeModal_listCostumes (){
      this.showContent = false;
      this.postFlag = "";
    },
    
    // フォームでファイルが選択されたら実行される
    onFileChange (event) {
      this.errors.photo = null;
      // 何も選択されていなかったら処理中断
      if (event.target.files.length === 0) {
        this.reset_photo();
        return false;
      }

      // ファイルが画像ではなかったら処理中断
      if (! event.target.files[0].type.match('image.*')) {
        this.reset_photo();
        this.errors.photo = '写真データを選択してください';
        return false;
      }

      // FileReaderクラスのインスタンスを取得
      const reader = new FileReader();

      // ファイルを読み込み終わったタイミングで実行する処理
      reader.onload = e => {
        // previewに読み込み結果（データURL）を代入する
        // previewに値が入ると<output>につけたv-ifがtrueと判定される
        // また<output>内部の<img>のsrc属性はpreviewの値を参照しているので
        // 結果として画像が表示される
        this.preview = e.target.result;
      }

      // ファイルを読み込む
      // 読み込まれたファイルはデータURL形式で受け取れる（上記onload参照）
      reader.readAsDataURL(event.target.files[0]);
  
      this.registerForm.photo = event.target.files[0];      

      if(this.val){
        // 調整
        this.$nextTick(() => {
          const content_dom = this.$refs.content_register_costume;
          const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
          if(content_rect.top < 0){
            this.overlay_class = 0;
          }else{
            this.overlay_class = 1;
          }
        });
      }     
    },
     
    // 画像をクリアするメソッド
    reset_photo () {
      this.preview = null;
      this.registerForm.photo = '';
      this.$el.querySelector('input[type="file"]').value = null;

      if(this.val){
        // 調整
        this.$nextTick(() => {
          const content_dom = this.$refs.content_register_costume;
          const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
          if(content_rect.top < 0){
            this.overlay_class = 0;
          }else{
            this.overlay_class = 1;
          }
        });
      }
    },

    // 入力欄の値とプレビュー表示をクリアするメソッド
    reset () {
      this.selectedColor_Class = '';
      this.registerForm.costume = '';
      this.registerForm.kana = '';
      this.registerForm.class = '';
      this.registerForm.color = '';
      this.registerForm.owner = '';
      this.registerForm.lend = false;
      this.registerForm.location = false;
      this.registerForm.handmade = false;
      this.registerForm.handmade_complete = 1;
      this.registerForm.decision = false;
      this.registerForm.usage_costume = '';
      this.registerForm.usage_guraduation_costume = '';
      this.registerForm.usage_stage_costume = null;
      this.registerForm.comment = '';
      this.preview = null;
      this.registerForm.photo = '';
      this.$el.querySelector('input[type="file"]').value = null;
      this.errors.photo = null;

      if(this.val){
        // 調整
        this.$nextTick(() => {
          const content_dom = this.$refs.content_register_costume;
          const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
          if(content_rect.top < 0){
            this.overlay_class = 0;
          }else{
            this.overlay_class = 1;
          }
        });
      }
    },

    // 全角→半角（数字）
    Zenkaku2hankaku_number(str) {
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

    // 全角→半角（アルファベット）
    Zenkaku2hankaku_alf(str) {
      return str.replace(/[ａ-ｚＡ-Ｚ]/g, function(s) {
        return String.fromCharCode(s.charCodeAt(0) - 0xFEE0);
      });

      let pattern_alf = /^([A-Z]\d)$/; // 0~9の数字かどうか
      const chars = str.split('');
      let sets = '';
      chars.forEach((char, index) => {
        char.replace(/[ａ-ｚＡ-Ｚ]/g, function(s) {
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

    // 半角→全角（カタカナ）
    hunkaku2Zenkaku_str(str) {
      const kanaMap = {
        'ｶﾞ': 'ガ', 'ｷﾞ': 'ギ', 'ｸﾞ': 'グ', 'ｹﾞ': 'ゲ', 'ｺﾞ': 'ゴ',
        'ｻﾞ': 'ザ', 'ｼﾞ': 'ジ', 'ｽﾞ': 'ズ', 'ｾﾞ': 'ゼ', 'ｿﾞ': 'ゾ',
        'ﾀﾞ': 'ダ', 'ﾁﾞ': 'ヂ', 'ﾂﾞ': 'ヅ', 'ﾃﾞ': 'デ', 'ﾄﾞ': 'ド',
        'ﾊﾞ': 'バ', 'ﾋﾞ': 'ビ', 'ﾌﾞ': 'ブ', 'ﾍﾞ': 'ベ', 'ﾎﾞ': 'ボ',
        'ﾊﾟ': 'パ', 'ﾋﾟ': 'ピ', 'ﾌﾟ': 'プ', 'ﾍﾟ': 'ペ', 'ﾎﾟ': 'ポ',
        'ｳﾞ': 'ヴ', 'ﾜﾞ': 'ヷ', 'ｦﾞ': 'ヺ',
        'ｱ': 'ア', 'ｲ': 'イ', 'ｳ': 'ウ', 'ｴ': 'エ', 'ｵ': 'オ',
        'ｶ': 'カ', 'ｷ': 'キ', 'ｸ': 'ク', 'ｹ': 'ケ', 'ｺ': 'コ',
        'ｻ': 'サ', 'ｼ': 'シ', 'ｽ': 'ス', 'ｾ': 'セ', 'ｿ': 'ソ',
        'ﾀ': 'タ', 'ﾁ': 'チ', 'ﾂ': 'ツ', 'ﾃ': 'テ', 'ﾄ': 'ト',
        'ﾅ': 'ナ', 'ﾆ': 'ニ', 'ﾇ': 'ヌ', 'ﾈ': 'ネ', 'ﾉ': 'ノ',
        'ﾊ': 'ハ', 'ﾋ': 'ヒ', 'ﾌ': 'フ', 'ﾍ': 'ヘ', 'ﾎ': 'ホ',
        'ﾏ': 'マ', 'ﾐ': 'ミ', 'ﾑ': 'ム', 'ﾒ': 'メ', 'ﾓ': 'モ',
        'ﾔ': 'ヤ', 'ﾕ': 'ユ', 'ﾖ': 'ヨ',
        'ﾗ': 'ラ', 'ﾘ': 'リ', 'ﾙ': 'ル', 'ﾚ': 'レ', 'ﾛ': 'ロ',
        'ﾜ': 'ワ', 'ｦ': 'ヲ', 'ﾝ': 'ン',
        'ｧ': 'ァ', 'ｨ': 'ィ', 'ｩ': 'ゥ', 'ｪ': 'ェ', 'ｫ': 'ォ',
        'ｯ': 'ッ', 'ｬ': 'ャ', 'ｭ': 'ュ', 'ｮ': 'ョ',
        '｡': '。', '､': '、', 'ｰ': 'ー', '｢': '「', '｣': '」', '･': '・'
      };
      let reg = new RegExp('(' + Object.keys(kanaMap).join('|') + ')', 'g');
      return str.replace(reg, function(s){
        return kanaMap[s];
      }).replace(/ﾞ/g, '゛').replace(/ﾟ/g, '゜');
    },

    /** 文字列内のカタカナをひらがなに変換します。 */
    kata2Hira(str) {
      return str.replace(/[\u30A1-\u30FA]/g, ch =>
      String.fromCharCode(ch.charCodeAt(0) - 0x60)
      );
    },

    // 登録する
    async register_costume () {
      const regex_str = /[^ぁ-んー]/g; // ひらがな以外
      const regex_number = /[^0-9]/g; // 数字以外
      const regex_alf = /[^A-Z]/g; // アルファベット
      let kana = '';
      let kanas = [...this.registerForm.kana];
      let pattern_number = /^([0-9]\d*|0)$/; // 0~9の数字かどうか
      let pattern_alf = /^([A-Z]\d*)$/; // A~Zのアルファベットかどうか*いる
      let names = [...this.registerForm.costume];
      let name_last = names[names.length-1];

      // kana正規表現
      if(this.first_uni <= name_last.charCodeAt(0) && name_last.charCodeAt(0) <= this.final_uni){
        // 囲み文字の処理
        const name_last_point_diff = name_last.charCodeAt(0)-this.first_uni + 1;
        name_last = name_last_point_diff;
      }else{
        // 囲み文字じゃなかった
        name_last = this.Zenkaku2hankaku_number(name_last);
        if(pattern_number.test(name_last)){
          // 数字だった
          for(let i = 2; i<names.length+1; i++){
            // 遡る
            let name_candidate = this.Zenkaku2hankaku_number(names[names.length-i]);
            if(pattern_number.test(name_candidate)){
              name_last = String(name_candidate) + String(name_last);
              name_last = Number(name_last);
            }else{
              break;
            }
          }
        }else{
          // 数字じゃなかった=文字だった
          name_last = this.Zenkaku2hankaku_alf(name_last);
          if(pattern_alf.test(name_last.toUpperCase())){
            // アルファベットだった
            name_last = name_last.toUpperCase();
            for(let i = 2; i<names.length+1; i++){
              // 遡る
              let name_candidate = this.Zenkaku2hankaku_alf(names[names.length-i]);
              if(pattern_alf.test(name_candidate)){
                name_last = name_candidate.toUpperCase() + name_last;
              }else{
                break;
              }
            }
          }else{
            // アルファベットじゃなかった=ひらがなかカタカナだった
            name_last = '';
          }
        }
      }

      kanas.forEach(a => {
        // 一文字ずつになっている
        if(this.first_uni <= a.charCodeAt(0) && a.charCodeAt(0) <= this.final_uni){
          // 囲み文字の処理
          const name_last_point_diff = a.charCodeAt(0)-this.first_uni + 1;
          kana = kana + name_last_point_diff;
        }else{
          // 囲み文字じゃなかった
          const number = this.Zenkaku2hankaku_number(a);
          if(pattern_number.test(number)){
            // 数字だった
            kana = kana + number;
          }else{
            // 数字じゃなかった=文字だった
            const alf = this.Zenkaku2hankaku_alf(number);
            if(pattern_alf.test(alf.toUpperCase())){
              // アルファベットだった
              kana = kana + alf.toUpperCase();
            }else{
              // アルファベットじゃなかった=ひらがなかカタカナだった
              const str = this.hunkaku2Zenkaku_str(alf);
              kana = kana + this.kata2Hira(str);
            }
          }
        }
      }, this);

      if(name_last){
        if(kana.slice( eval('-'+String(name_last).length)) !== String(name_last) ){
          // 最後のマークが名前と一致しない場合追加する
          kana = kana + String(name_last);
        }
      }
      
      const formData = new FormData();
      formData.append('name', this.registerForm.costume);
      formData.append('kana', kana);
      formData.append('class_id', this.registerForm.class);
      if(this.registerForm.color !== 0){
        formData.append('color_id', this.registerForm.color);
      }else{
        formData.append('color_id', '');
      }
      
      if(this.registerForm.owner !== 0){
        formData.append('owner_id', this.registerForm.owner);
      }else{
        formData.append('owner_id', '');
      }
      
      if(this.registerForm.lend){
        formData.append('lend', 1);
      }else{
        formData.append('lend', 0);
      }
      formData.append('memo', this.registerForm.comment);
      if(this.registerForm.location){
        formData.append('location', 1);
      }else{
        formData.append('location', 0);
      }
      if(this.registerForm.handmade){
        if(this.registerForm.handmade_complete === 1){
          formData.append('handmade', 1); // 手作りしなければいけない、完成
        }else if(this.registerForm.handmade_complete === 2){
          formData.append('handmade', 2); // 手作りしなければいけない、仕掛中
        }else{
          formData.append('handmade', 3); // 手作りしなければいけない、未着手
        }
      }else{
        formData.append('handmade', 0); // 手作りしなくていい
      }
      if(this.registerForm.decision){
        formData.append('decision', 1);
      }else{
        formData.append('decision', 0);
      }
      formData.append('usage', this.registerForm.usage_costume);
      formData.append('usage_guraduation', this.registerForm.usage_guraduation_costume);
      if(this.registerForm.usage_stage_costume === "usage_left"){
        formData.append('usage_left', 1);
        formData.append('usage_right', '');
      }else if(this.registerForm.usage_stage_costume === "usage_right"){
        formData.append('usage_right', 1);
        formData.append('usage_left', '');
      }else{
        formData.append('usage_left', '');
        formData.append('usage_right', '');
      }
      formData.append('photo', this.registerForm.photo);
      const response = await axios.post('/api/costumes', formData);
  
      if (response.status === 422) {
        this.errors.error = response.data.errors;
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '登録できませんでした',
          timeout: 6000
        });
        return false;
      }

      if (response.status !== 201) {
        this.$store.commit('error/setCode', response.status);
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '登録できませんでした',
          timeout: 6000
        });
        return false;
      }

      // 諸々データ削除
      this.reset();

      // メッセージ登録
      this.$store.commit('message/setContent', {
        content: '衣装が登録されました！',
        timeout: 6000
      });
    }
  },
  watch: {
    $route: {
      async handler () {
        await this.fetchClasses();
        await this.fetchColors();
        await this.fetchOwners();
        await this.fetchCostumes();
        await this.choicePerformance();
      },
      immediate: true
    },
    season_costume: {
      async handler(season_costume) {
        if(this.season_costume === "passo"){
          this.season_tag_costume = 1;
        }else if(this.season_costume === "guraduation"){
          this.season_tag_costume = 2;
        }
      },
      immediate: true
    },
    val: {
      async handler(val) {
        if(this.val){
          this.$nextTick(() => {
            const content_dom = this.$refs.content_register_costume;
            const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
  
            if(content_rect.top < 0){
              this.overlay_class = 0;
            }else{
              this.overlay_class = 1;
            }
          });
        }        
      },
      immediate: true
    }
  }
}
</script>