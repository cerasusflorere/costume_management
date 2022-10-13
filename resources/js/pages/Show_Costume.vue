<template>
  <div>
    <!-- ボタンエリア -->
    <div>
      <!--表示切替ボタン-->
      <div v-show="tabCostume === 1" class="button-area--showhow">
       <button type="button" @click="switchDisplay_costume" class="button button--inverse"><i class="fas fa-th fa-fw"></i>写真ブロック</button>
      </div>
      <div v-show="tabCostume === 2" class="button-area--showhow">
       <button type="button" @click="switchDisplay_costume" class="button button--inverse"><i class="fas fa-list-ul fa-fw"></i>リスト</button>
      </div>

      <form class="form"  @submit.prevent="searchCostume">
        <div>
          <span>並び替え</span>
          <div class="checkbox-area--together">
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

        <div>
          <span>検索</span>

          <!-- 入力検索 -->
          <div>
            <label for="search_costume_name">名前</label>
            <input type="text" class="form__item" id="search_costume_name" v-model="search_costume.costume_search.name.input"></input>
            <span class="checkbox-area--together">
              <input type="radio" id="search_costume_name_only" v-model="search_costume.costume_search.name.scope" value="name_only">
              <label for="search_costume_name_only">名前のみ</label>       

              <input type="radio" id="search_costume_memo_toghether" v-model="search_costume.costume_search.name.scope" value="memo_together">
              <label for="search_costume_memo_toghether">メモ含む</label>
            </span>
          </div>

          <!-- 色々分類 -->
          <div>
            <!--衣装分類-->
            <label for="search_costume_class">分類</label>
            <select id="search_costume_class" class="form__item"  v-model="search_costume.costume_search.class">
              <option value=0>選択なし</option>
              <option v-for="classes in optionClasses" v-bind:value="classes.id">
                {{ classes.class }}
              </option>
            </select>

            <!-- 色 -->
            <label>色</label>
            <label for="search_costume_color_class">色分類</label>
            <select id="search_costume_color_class" class="form__item" v-model="selectedColor_Class" v-on:change="selected">
              <option value=0>選択なし</option>
              <option v-for="(value, key) in optionColors">
                {{ key }}
              </option>
            </select>

            <label for="search_costume_color">色名</label>
            <select id="search_costume_color" class="form__item" v-model="search_costume.costume_serach_color">
              <option value=0>選択なし</option>
              <option v-if="selectedColors" v-for="color in selectedColors"
                      v-bind:value="color.id">
                 {{ color.color }}
              </option>
            </select>

            <!-- 持ち主 -->
            <label for="search_costume_owner">持ち主</label>
            <select id="search_costume_owner" class="form__item"  v-model="search_costume.costume_search.owner">
              <option value=0>選択なし</option>
              <option v-for="owner in optionOwners" v-bind:value="owner.id">
                {{ owner.name }}
              </option>
            </select>

            <!-- 使用するか -->
            <div>
                <span class="checkbox-area--together">
                  <label for="serach_costume_usage" class="form__check__label">中間発表</label>
                  <input type="checkbox" id="costume_usage_costume_edit" class="form__check__input" v-model="search_costume.costume_search.usage">
                </span>
                
                <span class="checkbox-area--together">
                  <label for="serach_costume_usage_guraduation" class="form__check__label">卒業公演</label>
                  <input type="checkbox" id="costume_usage_guraduation_scene_edit" class="form__check__input" v-model="search_costume.costume_search.usage_guraduation">
                </span>

                <span class="checkbox-area--together">
                  <input type="checkbox" id="serach_costume_usage_left" class="form__check__input" value="left" v-model="search_costume.costume_search.usage_left">
                  <label for="serach_costume_usage_left" class="form__check__label">上手</label>

                  <input type="checkbox" id="serach_costume_usage_right" class="form__check__input" value="right" v-model="search_costume.costume_search.usage_right"></input>
                  <label for="serach_costume_usage_right" class="form__check__label">下手</label>                
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


    <!-- 表示エリア -->
    <div v-show="tabCostume === 1">
      <div v-if="!sizeScreen" class="PC">
        <!-- ダウンロードボタン -->
        <div v-if="showCostumes.length" class="button-area--download">
          <button type="button" @click="downloadCostumes" class="button button--inverse"><i class="fas fa-download fa-fw"></i>ダウンロード</button>
        </div>
        <table v-if="showCostumes.length">
          <thead>
            <tr>
              <th class="th-non"></th>
              <th>衣装名</th>
              <th>分類</th>
              <th>色</th>
              <th>持ち主</th>
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
              <tr>
                <th class="th-non"></th>
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
      <div class="grid" v-if="showCostumes.length">
        <div class="grid__item" v-for="costume in showCostumes">
          <div class="photo">
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
                {{ costume.name}}
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
  import ExcelJS from 'exceljs';

  export default {
    // このページの上で表示するコンポーネント
    components: {
      detailCostume
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
        // 衣装分類リスト
        optionClasses: [],
        // 色
        selectedColor_Class: '',
        selectedColors: '',
        optionColors: null,
        // 持ち主リスト
        optionOwners: [],
        // 衣装詳細
        showContent: false,
        postCostume: "",
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
            usage: false,
            usage_guraduation: false,
            usage_left: false,
            usage_right: false
          }
        }
      }
    },
    watch: {
      $route: {
        async handler () {
          await this.fetchCostumes();
          await this.fetchClasses();        
          await this.fetchColors();
          await this.fetchOwners();
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
      },

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
        let color_class_id = '!=' + 0;
        let color_id = '!=' + 0;
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

        if(this.search_costume.costume_search.color_class != 0){
          color_class_id = '===' + this.search_costume.costume_search.color_class;
        }

        if(this.search_costume.costume_search.color != 0){
          color_id = '===' + this.search_costume.costume_search.color;
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

        const keyword = 'a.name' + name_input +'&& a.kana' + name_input + '&& a.costume_comments[0]' + name_scope + '&& a.class_id' + class_id + '&& a.color_class_id' + color_class_id + '&& a.color_id' + color_id + '&& a.usage' + usage + '&& a.usage_guraduation' + usage_guraduation + '&& a.usage_left' + usage_left + '&& a.usage_right' + usage_right;

        const array = this.costumes.filter((a) => eval(keyword));

        if(this.search_costume.costume_sort === "class"){
          array.sort((a, b) => a.class_id - b.class_id);
        }else if(this.search_costume.costume_sort === "created_at"){
          array.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        }else if(this.search_costume.costume_sort === "updated_at"){
          array.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
        }else{
          array.sort((a, b) => a.kana - b.kana);
        }

        this.showCostumes = array;
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