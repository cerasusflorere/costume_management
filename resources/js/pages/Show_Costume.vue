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

      <div class="button-area--small">
        <!-- 検索 -->
        <div class="button-area--small-small">
          <button type="button" @click="openModal_searchCostume(Math.random())" class="button button--inverse button--small"><i class="fas fa-search fa-fw"></i>検索</button>
        </div>
        <searchCostume :postSearch="postSearch" v-show="showContent_search" @close="closeModal_searchCostume" />

        <!-- ダウンロードボタン -->
        <!-- リスト表示かつPCかつデータがある時 -->
        <div v-show="tabCostume === 1" v-if="!sizeScreen && showCostumes.length" class="button-area--small-small">
          <button type="button" @click="downloadCostumes" class="button button--inverse button--small"><i class="fas fa-download fa-fw"></i>ダウンロード</button>
        </div>
      </div>      
    </div>


    <!-- 表示エリア -->
    <div v-show="tabCostume === 1" class="table-area">
      <div v-if="!sizeScreen" class="PC">
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
  import searchCostume from '../components/Search_Costume.vue';
  import ExcelJS from 'exceljs';

  export default {
    // このページの上で表示するコンポーネント
    components: {
      detailCostume,
      searchCostume
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
        postSearch: ""
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
      async closeModal_searchCostume(sort, name, refine) {
        this.showContent_search = false;
        if(sort !== null && refine !== null){
          let array_original = this.costumes.filter((a) => eval(refine));
          let array = [];

          if(this.h(name.input)){
            if(name.scope === "memo_together"){
              // メモを含めて検索
              array = array_original.filter((a) => {
                if(a.name.toLocaleLowerCase().indexOf(this.h(name.input).toLocaleLowerCase()) !== -1) {
                  return a;
                }else if(a.kana.toLocaleLowerCase().indexOf(this.h(name.input).toLocaleLowerCase()) !== -1) {
                  return a;
                }else if(a.costume_comments[0]){
                  if(a.costume_comments[0].memo.toLocaleLowerCase().indexOf(this.h(name.input).toLocaleLowerCase()) !== -1){
                    return a;
                  }                  
                }
              });
            }else{
              // 衣装名のみで検索
              array = array_original.filter((a) => {
                if(a.name.toLocaleLowerCase().indexOf(this.h(name.input).toLocaleLowerCase()) !== -1) {
                  return a;
                }else if(a.kana.toLocaleLowerCase().indexOf(this.h(name.input).toLocaleLowerCase()) !== -1) {
                  return a;
                }
              });
            }
          }else{
            // 入力検索しない
            array = array_original;
          }
          
          if(sort === "class"){
            array.sort((a, b) => a.class_id - b.class_id);
          }else if(sort === "created_at"){
            array.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
          }else if(sort === "updated_at"){
            array.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
          }else{
            array.sort((a, b) => a.kana - b.kana);
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