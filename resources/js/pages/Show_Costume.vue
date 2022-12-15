<template>
    <div>
      <!-- ボタンエリア -->
      <div class="button-area">
        <!--表示切替ボタン-->
        <div v-show="tabCostume === 1" class="button-area--showhow">
          <button type="button" @click="switchDisplay_costume" class="button button--inverse"><i class="fas fa-th fa-fw"></i>写真ブロック</button>
        </div>
        <div v-show="tabCostume === 2" class="button-area--showhow">
          <button type="button" @click="switchDisplay_costume" class="button button--inverse"><i class="fas fa-list-ul fa-fw"></i>リスト</button>
        </div>

        <div v-if="costumes.length" class="button-area--small">
          <div class="button-area--together-left">
            <!-- 検索 -->
            <div class="button-area--small-small">
              <button type="button" @click="openModal_searchCostume(Math.random())" class="button button--inverse button--small"><i class="fas fa-search fa-fw"></i>検索</button>
            </div>
            <searchCostume :postSearch="postSearch" v-show="showContent_search" @close="closeModal_searchCostume" />

            <!-- 並び替えリセット -->
            <div class="button-area--small-small">
              <button type="button" @click="sort_Standard('reset')" class="button button--inverse button--small"><i class="fas fa-undo-alt fa-fw"></i>リセット</button>
            </div>

            <!-- 選択 -->
            <div class="button-area--small-small">
              <button type="button" @click="showCheckBox" class="button button--inverse button--small button--choice"><i class="fas fa-check-square fa-fw"></i>選択</button>
            </div>
            
            <!-- 選択編集 -->
            <div v-if="choice_flag" class="button-area--small-small">
              <button type="button" @click="openModal_customEdit" class="button button--inverse button--small button--choice"><i class="fas fa-edit fa-fw"></i>選択編集</button>
            </div>
            <customDialog_Edit :custom_dialog_edit_message="postMessage_CustomEdit" v-show="showContent_customEdit" @Cancel_CustomEdit="closeModal_customEdit_Cancel" @OK_CustomEdit="closeModal_customEdit_OK"/>
            <confirmDialog_Edit :confirm_dialog_edit_message="postMessage_Edit" v-show="showContent_confirmEdit" @Cancel_Edit="closeModal_confirmEdit_Cancel" @OK_Edit="closeModal_confirmEdit_OK"/>

            <!-- 選択削除実行 -->
            <div v-if="choice_flag" class="button-area--small-small">
              <button type="button" @click="openModal_confirmDelete" class="button button--inverse button--small button--choice"><i class="fas fa-trash-alt fa-fw"></i>選択削除</button>
            </div>
            <confirmDialog_Delete :confirm_dialog_delete_message="postMessage_Delete" v-show="showContent_confirmDelete" @Cancel_Delete="closeModal_confirmDelete_Cancel" @OK_Delete="closeModal_confirmDelete_OK"/>
          </div>

          <!-- ダウンロードボタン -->
          <!-- リスト表示かつPCかつデータがある時 -->
          <div v-show="tabCostume === 1" v-if="!sizeScreen && showCostumes.length" class="button-area--small-small">
            <button type="button" @click="downloadCostumes" class="button button--inverse button--small"><i class="fas fa-download fa-fw"></i>ダウンロード</button>
          </div>
        </div> 
      </div>

      <!-- 表示エリア -->
      <div v-show="tabCostume === 1">
        <div v-if="!sizeScreen" class="PC">
          <table v-if="showCostumes.length">
            <thead>
              <tr>
                <th v-if="choice_flag" class="th-non">
                  <input type="checkbox" class="checkbox-delete" @click="choiceDeleteAllCostumes"></input>
                </th>
                <th class="th-non"></th>
                <th>衣装名</th>
                <th>分類</th>
                <th>色</th>
                <th>持ち主</th>
                <th>貸した</th>
                <th>ピッコロ</th>
                <th>作るか</th>
                <th>決定</th>
                <th>中間</th>
                <th>卒業</th>
                <th>上手</th>
                <th>下手</th>
                <th class="th-memo">メモ</th>
                <th>登録日時</th>
                <th>更新日時</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(costume, index) in showCostumes">
                <td v-if="choice_flag">
                  <input type="checkbox" class="checkbox-delete" v-model="choice_ids[costume.id]"></input>
                </td>
                <td class="td-color">{{ index + 1 }}</td>
                <!-- 衣装名 -->
                <td type="button" class="list-button" @click="openModal_costumeDetail(costume.id)">{{ costume.name }}</td>
                <!-- 分類 -->
                <td>{{ costume.class.class }}</td>
                <!-- 色 -->
                <td v-if="costume.color">{{ costume.color.color }}</td>
                <td v-else></td>
                <!-- 持ち主 -->
                <td v-if="costume.owner">{{ costume.owner.name }}</td>
                <td v-else></td>
                <!-- 貸し借りしたか -->
                <td v-if="costume.lend"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- ピッコロに持ってきたか -->
                <td v-if="costume.location"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- 作るかどうか -->
                <td v-if="costume.handmade === 1">完</td>
                <td v-else-if="costume.handmade === 2">仕</td>
                <td v-else-if="costume.handmade === 3">未</td>
                <td v-else></td>
                <!-- これで決定か -->
                <td v-if="costume.decision"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- 中間発表-->
                <td v-if="costume.usage"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- 卒業公演-->
                <td v-if="costume.usage_guraduation"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- 上手-->
                <td v-if="costume.usage_left"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- 下手-->
                <td v-if="costume.usage_right"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
                <!-- メモ -->
                <td v-if="costume.costume_comments.length">
                  <div v-for="memo in costume.costume_comments"> {{ memo.memo }}</div>
                </td>
                <td v-else></td>
                <!-- 登録日時 -->
                <td>{{ costume.created_at }}</td>
                <!-- 更新日時 -->
                <td>{{ costume.updated_at }}</td>
              </tr> 
            </tbody>      
          </table>

          <div v-if="!showCostumes.length">
            衣装は登録されていません。 
          </div>
        </div>

        <div v-else class="phone">
          <div v-if="showCostumes.length">
            <table>
              <div v-for="(costume, index) in showCostumes">
                <tr v-show="index === 0" v-if="choice_flag">
                  <th class="th-non">
                    <input type="checkbox" class="checkbox-delete" @click="choiceDeleteAllCostumes"></input>
                  </th>
                  <td></td>
                </tr>
                <tr>
                  <th class="th-non">
                    <input type="checkbox" v-if="choice_flag" class="checkbox-delete" v-model="choice_ids[costume.id]"></input>
                  </th>
                  <td class="td-color">{{ index + 1 }}</td> 
                </tr>
                <tr>
                  <!-- 衣装名 -->
                  <th>衣装名</th>
                  <td type="button" class="list-button" @click="openModal_costumeDetail(costume.id)">{{ costume.name }}</td>
                </tr>
                <tr>
                  <!-- 衣装分類 -->
                  <th>分類</th>
                  <td>{{ costume.class.class }}</td>
                </tr>
                <tr>
                  <!-- 色 -->
                  <th>色</th>
                  <td v-if="costume.color">{{ costume.color.color }}</td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 持ち主 -->
                  <th>持ち主</th>
                  <td v-if="costume.owner">{{ costume.owner.name }}</td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 貸し借りしたか -->
                  <th>貸した</th>
                  <td v-if="costume.lend"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- ピッコロに持ってきたか -->
                  <th>ピッコロにあるか</th>
                  <td v-if="costume.location"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 作るかどうか -->
                  <th>作るか</th>
                  <td v-if="costume.handmade === 1">完</td>
                  <td v-else-if="costume.handmade === 2">仕</td>
                  <td v-else-if="costume.handmade === 3">未</td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- これで決定か -->
                  <th>決定か</th>
                  <td v-if="costume.decision"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 中間発表 -->
                  <th>中間</th>
                  <td v-if="costume.usage"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 卒業公演 -->
                  <th>卒業</th>
                  <td v-if="costume.usage_guraduation"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 上手 -->
                  <th>上手</th>
                  <td v-if="costume.usage_left"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 下手 -->
                  <th>下手</th>
                  <td v-if="costume.usage_right"><i class="fas fa-check fa-fw"></i></td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- メモ -->
                  <th>メモ</th>
                  <td v-if="costume.costume_comments.length">
                    <div v-for="memo in costume.costume_comments"> {{ memo.memo }}</div>
                  </td>
                  <td v-else></td>
                </tr>
                <tr>
                  <!-- 登録日時 -->
                  <th>登録日時</th>
                  <td>{{ costume.created_at }}</td>
                </tr>
                <tr>
                  <!-- 更新日時 -->
                  <th>更新日時</th>
                  <td>{{ costume.updated_at }}</td>
                </tr>
              </div>
            </table>
          </div>

          <div v-else>
            衣装は登録されていません。 
          </div>
        </div>
      </div>

      <div v-show="tabCostume === 2">
        <div v-if="showCostumes.length && choice_flag">
          <input type="checkbox" @click="choiceDeleteAllCostumes"></input>
        </div>
        <div class="grid" v-if="showCostumes.length">
          <div class="grid__item" v-for="costume in showCostumes">
            <div class="photo">
              <input type="checkbox" v-if="choice_flag" v-model="choice_ids[costume.id]"></input>
              <figure class="photo__wrapper" type="button" @click="openModal_costumeDetail(costume.id)">
                <img
                  class="photo__image"
                  :src="costume.url"
                  :alt="costume.name"
                >
              </figure>
              <div>
                <!-- 衣装名 -->
                <div>
                  {{ costume.name }}
                </div>
                <!-- 衣装分類 -->
                <div>
                  {{ costume.class.class }}
                </div>
                <!-- 色 -->
                <div v-if="costume.color">
                  {{ costume.color.color }}
                </div>
                <!-- 持ち主 -->
                <div v-if="costume.owner">
                  {{ costume.owner.name }}
                </div>
                <!-- 貸し借りしたか -->
                <div>
                  <span class="usage-show">貸した:</span>
                  <span v-if="costume.lend" class="usage-show"><i class="fas fa-check fa-fw"></i></span>
                </div>

                <!-- ピッコロに持ってきたか -->
                <div>
                  <span class="usage-show">ピッコロにあるか:</span>
                  <span v-if="costume.location" class="usage-show"><i class="fas fa-check fa-fw"></i></span>
                </div>

                <!-- 作るかどうか -->
                <div>
                  <span class="usage-show">作るかどうか:</span>
                  <span v-if="costume.handmade === 1" class="usage-show">完</span>
                  <span v-else-if="costume.handmade === 2" class="usage-show">仕</span>
                  <span v-else-if="costume.handmade === 3" class="usage-show">未</span>
                </div>

                <!-- これで決定か -->
                <div>
                  <span class="usage-show">これで決定か:</span>
                  <span v-if="costume.decision" class="usage-show"><i class="fas fa-check fa-fw"></i></span>
                </div>

                <div>
                  <!-- 中間発表 -->
                  <span v-if="costume.usage" class="usage-show">Ⓟ</span>
                  <!-- 卒業公演 -->
                  <span v-if="costume.usage_guraduation" class="usage-show">Ⓖ</span>
                  <!-- 上手 -->
                  <span v-if="costume.usage_left" class="usage-show">㊤</span>
                  <!-- 下手 -->
                  <span v-if="costume.right" class="usage-show">㊦</span>
                </div>
                
                <!-- メモ -->
                <div v-if="costume.costume_comments.length">
                  <span>メモ: </span>
                  <div v-for="memo in costume.costume_comments">
                    {{ memo.memo }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else>
          衣装は登録されていません。
        </div>
      </div>
      <detailCostume :postCostume="postCostume" v-show="showContent" @close="closeModal_costumeDetail" /> 
    </div>  
</template>

<script>
  import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util';

  import detailCostume from '../components/Detail_Costume.vue';
  import searchCostume from '../components/Search_Costume.vue';
  import customDialog_Edit from '../components/Custom_Dialog_Edit.vue';
  import confirmDialog_Edit from '../components/Confirm_Dialog_Edit.vue';
  import confirmDialog_Delete from '../components/Confirm_Dialog_Delete.vue';
  import ExcelJS from 'exceljs';

  export default {
    // このページの上で表示するコンポーネント
    components: {
      detailCostume,
      searchCostume,
      customDialog_Edit,
      confirmDialog_Edit,
      confirmDialog_Delete
    },
    data() {
      return{
        // 画面サイズ取得
        sizeScreen: 1, // 0:パソコン, 1: スマホ
        // タブ切り替え
        tabCostume: 1,
        // 取得するデータ
        costumes: [],
        // 表示するデータ
        showCostumes: [],
        // 衣装詳細
        showContent: false,
        postCostume: "",
        // 衣装検索カスタム
        showContent_search: false,
        postSearch: "",
        custom_sort: null,
        custom_name: {
          input: null,
          scope: null
        },
        custom_refine: null,
        // 選択ボタン
        choice_flag: false,
        // 選択
        choice_ids: [],
        choice_many: 0,
        // 編集custom
        showContent_customEdit: false,
        postMessage_CustomEdit: "",
        edit_custom: null,
        yes_no: null,
        // 編集confirm
        showContent_confirmEdit: false,
        postMessage_Edit: "",
        // 削除confirm
        showContent_confirmDelete: false,
        postMessage_Delete: ""
      }
    },
    watch: {
      $route: {
        async handler () {
          await this.fetchCostumes();
          if (window.matchMedia('(max-width: 989px)').matches) {
            //スマホ処理
            this.sizeScreen = 1;
          } else {
            //PC処理
            this.sizeScreen = 0;
          }
        },
        immediate: true
      }
    },
    methods: {
      // 衣装一覧を取得
      async fetchCostumes () {
        const response = await axios.get('/api/costumes_all');
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.costumes = response.data; // オリジナルデータ
        this.showCostumes = JSON.parse(JSON.stringify(this.costumes));
        
        this.costumes.forEach((costume) => {
          this.choice_ids.push(false);
        }, this);

        if(this.custom_sort || this.custom_name.input !== null || this.custom_refine){
          await this.closeModal_searchCostume(this.custom_sort, this.custom_name, this.custom_refine);
        }else{
          this.sort_Standard(this.showCostumes);
        }
      },

      sort_Standard(array){
        if(array === 'reset'){
          this.showCostumes = JSON.parse(JSON.stringify(this.costumes));
          array = this.showCostumes;
          this.custom_sort = null;
          this.custom_name.input = null;
          this.custom_name.scope = null;
          this.custom_refine = null;
        }

        const regex_str = /[^ぁ-んー]/g; // ひらがな以外
        const regex_number = /[^0-9]/g; // 数字以外
        const regex_alf = /[^A-Z]/g; // アルファベット
        array.sort((a, b) => {
          
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

        this.showCostumes = array;
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

      // 検索カスタムのモーダル表示 
      openModal_searchCostume (number) {
        this.showContent_search = true;
        this.postSearch = number;
      },
      // 検索カスタムのモーダル非表示
      async closeModal_searchCostume(sort, name_input, refine) {
        this.showContent_search = false;
        if(sort !== null && refine !== null){
          this.custom_sort = sort;
          if(name_input.input && !Array.isArray(name_input.input)){
            this.custom_name.input = name_input.input.split(/,|、|，|\s+/);
            this.custom_name.input = this.custom_name.input.filter(Boolean);
            this.custom_name.scope = name_input.scope;
          }else if(!name_input.input){
            this.custom_name.input = null;
            this.custom_name.scope = null;
          }
          this.custom_refine = refine;

          let array_original = this.costumes.filter((a) => eval(refine));
          let array = [];

          if(Array.isArray(this.custom_name.input)){
            // 入力値があった
            let new_array = [];
            if(this.custom_name.scope === "memo_together"){
              // メモを含めて検索
              array_original.forEach((a) => {
                this.custom_name.input.forEach((name) => {
                  if(a.name.toLocaleLowerCase().indexOf(this.h(name).toLocaleLowerCase()) !== -1) {
                    new_array.push(a);
                  }else if(a.kana.toLocaleLowerCase().indexOf(this.h(name).toLocaleLowerCase()) !== -1) {
                    new_array.push(a);
                  }else if(a.costume_comments[0]){
                    if(a.costume_comments[0].memo.toLocaleLowerCase().indexOf(this.h(name).toLocaleLowerCase()) !== -1){
                      new_array.push(a);
                    }    
                  }
                }, this);
              }, this);
            }else{
              // 衣装名のみで検索
              array_original.forEach((a) => {
                this.custom_name.input.forEach((name) => {
                  if(a.name.toLocaleLowerCase().indexOf(this.h(name).toLocaleLowerCase()) !== -1) {
                    new_array.push(a);
                  }else if(a.kana.toLocaleLowerCase().indexOf(this.h(name).toLocaleLowerCase()) !== -1) {
                    new_array.push(a);
                  }
                }, this);
              }, this);
            }

            // 重複削除
            const set = new Set(new_array);
            const newArr = [...set];
            array = Array.from(new Set(newArr));
          }else{
            // 入力検索しない
            array = array_original;
          }
          
          if(sort === "class"){
            array.sort((a, b) => a.class_id - b.class_id);
          }else if(sort === "owner"){
            array.sort((a, b) => a.owner_id - b.owner_id);
          }else if(sort === "created_at"){
            array.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
          }else if(sort === "updated_at"){
            array.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
          }else{
            this.sort_Standard(array);
          }

          this.showCostumes = array;
        }      
      },
      
      // 表示切替
      switchDisplay_costume() {
        if(this.tabCostume === 1){
          this.tabCostume = 2;
        }else{
          this.tabCostume = 1;
        }
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

      // 選択ボタン出現
      showCheckBox() {
      if(this.choice_flag){
        this.choice_flag = false;
        this.choice_many = 0;
        this.costumes.forEach((costume) => {
          this.$set(this.choice_ids, costume.id, false);
        }, this);
      }else{
        this.choice_flag = true;
      }
      },

      // 選択（全選択）
      choiceDeleteAllCostumes() {
      if(!this.choice_many){
        this.choice_many = 1;
        this.showCostumes.forEach((costume) => {
          // リアクティブにするため
          this.$set(this.choice_ids, costume.id, true);
        }, this);
      }else{
        this.choice_many = 0;
        this.showCostumes.forEach((costume) => {
          this.$set(this.choice_ids, costume.id, false);
        }, this);
      }
      },

      // 編集customのモーダル表示 
      openModal_customEdit () {
      this.showContent_customEdit = true;
      this.postMessage_CustomEdit = '衣装の編集項目について選択してください。';
      },
      // 編集customのモーダル非表示_OKの場合
      async closeModal_customEdit_OK(edit_custom_flag) {
      if(edit_custom_flag !== null){
        this.showContent_customEdit = false;
        this.$emit('close');
        const yes = edit_custom_flag.indexOf('yes');
        const no = edit_custom_flag.indexOf('no');
        const handmade_custom = edit_custom_flag.split('_');
        if(yes !== -1){
          // yes
          this.yes_no = 1;
          this.edit_custom =  edit_custom_flag.replace('_yes', '');
        }else if(no !== -1){
          // no
          this.yes_no = 0;
          this.edit_custom = edit_custom_flag.replace('_no', '');
        }else{
          // handmade
          this.yes_no = 1;
          console.log(handmade_custom[1]);
          this.edit_custom = handmade_custom[1];
        }
        this.openModal_confirmEdit();
      }        
      },
      // 編集customのモーダル非表示_Cancelの場合
      closeModal_customEdit_Cancel() {
      this.showContent_customEdit = false;
      },

      // 編集confirmのモーダル表示 
      openModal_confirmEdit () {
      this.showContent_confirmEdit = true;
      let edit_costumes_name = '';
      let edit_custom_show;
      let yes_no_show;
      if(this.costumes.length === this.showCostumes.length && this.choice_many){
        edit_costumes_name ='全て\n';
      }
      this.showCostumes.forEach((costume, index) => {
        if(this.choice_ids[costume.id]){
          edit_costumes_name = edit_costumes_name + '・' + costume.name + '\n';
        }
      }, this);

      if(this.edit_custom === 'lend'){
        edit_custom_show = '貸して';
      }else if(this.edit_custom === 'location'){
        edit_custom_show = 'ピッコロに持ってきて';
      }else if(this.edit_custom === 'decision'){
        edit_custom_show = '決定して'
      }else if(this.edit_custom === 'usage'){
        edit_custom_show = '中間発表で使用して';
      }else if(this.edit_custom === 'usage_guraduation'){
        edit_custom_show = '卒業公演で使用して';
      }else if(this.edit_custom === 'usage_left'){
        edit_custom_show = '上手で使用して';
      }else if(this.edit_custom === 'usage_right'){
        edit_custom_show = '下手で使用して';
      }else{
        edit_custom_show = '作';
      }

      if(this.yes_no === 1){
        yes_no_show = 'る';
        if(this.edit_custom === 'complete'){
          yes_no_show = yes_no_show + ': 完成';
        }else if(this.edit_custom === 'progress'){
          yes_no_show = yes_no_show + ': 仕掛中';
        }else if(this.edit_custom === 'unfinished'){
          yes_no_show = yes_no_show + ': 未着手';
        }
      }else{
        if(this.edit_custom === 'handmade'){
          yes_no_show = 'らない';
        }else{
          yes_no_show = 'ない';
        }        
      }

      this.postMessage_Edit = '以下の衣装を' + edit_custom_show + yes_no_show + 'と変更します。\n本当に変更しますか？\n' + edit_costumes_name;
      },
      // 編集confirmのモーダル非表示_OKの場合
      async closeModal_confirmEdit_OK() {
      this.showContent_confirmEdit = false;
      this.$emit('close');
      await this.EditCostumes();
      },
      // 編集confirmのモーダル非表示_Cancelの場合
      closeModal_confirmEdit_Cancel() {
      this.showContent_confirmEdit = false;
      this.openModal_customEdit();
      },

      // 選択編集(実行)
      async EditCostumes() {
      let ids = [];
      let method = this.edit_custom;
      let yes_no;
      this.showCostumes.forEach((costume) => {
        if(this.choice_ids[costume.id]){
          ids.push(costume.id);
        }
      });
      if(this.yes_no === 1){
        yes_no = 1;
        if(this.edit_custom === 'complete'){
          yes_no = 1;
          method = 'handmade';
        }else if(this.edit_custom === 'progress'){
          yes_no = 2;
          method = 'handmade';
        }else if(this.edit_custom === 'unfinished'){
          yes_no = 3;
          method = 'handmade';
        }
      }else{
        yes_no = null;
      }
      const response = await axios.post('/api/costumes_many/' + ids, {
        method: method,
        yes_no: yes_no
      });
      await this.fetchCostumes();
      // 選択削除閉じる
      this.showCheckBox();
      },

      // 削除confirmのモーダル表示 
      openModal_confirmDelete (id) {
      this.showContent_confirmDelete = true;
      let delete_costumes_name = '';
      if(this.costumes.length === this.showCostumes.length && this.choice_many){
        delete_costumes_name ='全て\n';
      }
      this.showCostumes.forEach((costume, index) => {
        if(this.choice_ids[costume.id]){
          delete_costumes_name = delete_costumes_name + '・' + costume.name + '\n';
        }
      }, this);
      this.postMessage_Delete = '以下の衣装を削除すると、紐づけられてたこの衣装を使用するシーンも全て削除されます。\n本当に削除しますか？\n' + delete_costumes_name;
      },
      // 削除confirmのモーダル非表示_OKの場合
      async closeModal_confirmDelete_OK() {
      this.showContent_confirmDelete = false;
      this.$emit('close');
      await this.deleteCostumes();
      },
      // 削除confirmのモーダル非表示_Cancelの場合
      closeModal_confirmDelete_Cancel() {
      this.showContent_confirmDelete = false;
      },

      // 選択削除（実行）
      async deleteCostumes() {
      let ids = [];
      this.showCostumes.forEach((costume) => {
        if(this.choice_ids[costume.id]){
          ids.push(costume.id);
        }
      });
      const response = await axios.delete('/api/costumes_many/' + ids);
      await this.fetchCostumes();
      // 選択削除閉じる
      this.showCheckBox();
      },

      // ダウンロード
      // downloadCostumes() {
      //   const response = axios.post('/api/costumes_list', this.showCostumes);
      // }

      // ダウンロード
      async downloadCostumes() {
        // ①初期化
        const workbook = new ExcelJS.Workbook(); // workbookを作成
        workbook.addWorksheet('Sheet1'); // worksheetを追加
        const worksheet = workbook.getWorksheet('Sheet1'); // 追加したworksheetを取得

        // ②データを用意
        // 各列のヘッダー
        worksheet.columns = [
          { header: '衣装名', key: 'name', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},          
          { header: '分類', key: 'class', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '色', key: 'color', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '持ち主', key: 'owner', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '貸した', key: 'lend', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: 'ピッコロ', key: 'location', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '作るか', key: 'handmade', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '決定', key: 'decision', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '中間発表', key: 'usage', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '卒業公演', key: 'usage_guraduation', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '上手', key: 'usage_left', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '下手', key: 'usage_right', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: 'メモ', key: 'memo', width: 24, style: { alignment: {vertical: "middle", horizontal: "center" }}},
        ];

        worksheet.views = [
          {state: 'frozen', xSplit: 0, ySplit: 1, activeCell: 'A1'}  // ウィンドウ固定
        ];

        const font =  { color: { argb: '169b62' }}; // 文字
        const fill =  { type: 'pattern', pattern:'solid', fgColor: { argb:'ddefe3' }}; // 背景色
        worksheet.getCell('A1').font = font;
        worksheet.getCell('A1').fill = fill;
        worksheet.getCell('B1').font = font;
        worksheet.getCell('B1').fill = fill;
        worksheet.getCell('C1').font = font;
        worksheet.getCell('C1').fill = fill;
        worksheet.getCell('D1').font = font;
        worksheet.getCell('D1').fill = fill;
        worksheet.getCell('E1').font = font;
        worksheet.getCell('E1').fill = fill;
        worksheet.getCell('F1').font = font;
        worksheet.getCell('F1').fill = fill;
        worksheet.getCell('G1').font = font;
        worksheet.getCell('G1').fill = fill;
        worksheet.getCell('H1').font = font;
        worksheet.getCell('H1').fill = fill;
        worksheet.getCell('I1').font = font;
        worksheet.getCell('I1').fill = fill;
        worksheet.getCell('J1').font = font;
        worksheet.getCell('J1').fill = fill;
        worksheet.getCell('K1').font = font;
        worksheet.getCell('K1').fill = fill;
        worksheet.getCell('L1').font = font;
        worksheet.getCell('L1').fill = fill;
        worksheet.getCell('M1').font = font;
        worksheet.getCell('M1').fill = fill;

        this.showCostumes.forEach((costume, index) => {
          let datas = [];
          datas.push(costume.name);

          datas.push(costume.class.class);

          if(costume.color){
            datas.push(costume.color.color);
          }else{
            datas.push(null);
          }

          if(costume.owner){
            datas.push(costume.owner.name);
          }else{
            datas.push(null);
          }

          if(costume.lend){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.location){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.handmade === 1){
            datas.push('完');
          }else if(costume.handmade === 2){
            datas.push('仕');
          }else if(costume.handmade === 3){
            datas.push('未');
          }else{
            datas.push(null);
          }

          if(costume.decision){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.usage){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.usage_guraduation){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.usage_left){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.usage_right){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(costume.costume_comments.length){
            costume.costume_comments.forEach((comment, index_comment) => {
              if(index_comment){
                const remove_data = datas.splice(datas.length-1, datas.length-1, datas[datas.length-1]+'<br>'+comment.memo)
              }else{
                datas.push(comment.memo);
              }
            })
          }

          //行を取得
          let sheet_row = worksheet.getRow( index + 2 ) ;
 
          //列を取得し値を設定
          datas.forEach((data, index_data) => {
            sheet_row.getCell( index_data + 1 ).value = data ;
          })
 
          
        })

        // ③ファイル生成
        const uint8Array = await workbook.xlsx.writeBuffer(); // xlsxの場合
        const blob = new Blob([uint8Array], { type: 'application/octet-binary' });
        const a = document.createElement('a');
        a.href = (window.URL || window.webkitURL).createObjectURL(blob);
        const today = this.formatDate(new Date());
        const filename = 'Costumes_list_' + 'all' + '_' + today + '.xlsx';
        a.download = filename;
        a.click();
        a.remove();
      },

      // 日付をyyyy-mm-ddで返す
      formatDate(dt) {
        const y = dt.getFullYear();
        const m = ('00' + (dt.getMonth()+1)).slice(-2);
        const d = ('00' + dt.getDate()).slice(-2);
        return (y + '-' + m + '-' + d);
      }
    }
  }  
</script>