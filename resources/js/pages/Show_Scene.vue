<template>
  <!-- 表示エリア -->
  <div>
    <!-- ボタンエリア -->
    <div v-if="showScenes.length" class="button-area">
      <div class="button-area--small">
        <!-- 検索 -->
        <div class="button-area--small-small">
          <button type="button" @click="openModal_searchScene(Math.random())" class="button button--inverse button--small"><i class="fas fa-search fa-fw"></i>検索</button>
        </div>
        <searchScene :postSearch="postSearch" v-show="showContent_search" @close="closeModal_searchScene" />

        <!-- ダウンロードボタン -->
        <!-- リスト表示かつPCかつデータがある時 -->
        <div v-if="!sizeScreen" class="button-area--small-small">
          <button type="button" @click="downloadScenes" class="button button--inverse button--small"><i class="fas fa-download fa-fw"></i>ダウンロード</button>
        </div>
      </div>      
    </div>

    <div v-if="!sizeScreen && showScenes.length" class="PC">
      <table>
        <thead>
          <tr>
            <th class="th-non"></th>
            <th>ページ数</th>
            <th>登場人物</th>
            <th>衣装名</th> 
            <th>中間発表</th>
            <th>卒業公演</th>
            <th>上手</th>
            <th>下手</th>         
            <th class="th-memo">メモ</th>
            <th>登録日時</th>
            <th>更新日時</th>
          </tr>
        </thead>
        <tbody v-if="showScenes.length">
          <tr v-for="(scene, index) in showScenes">
              <!-- index -->
              <td type="button" class="list-button td-color" @click="openModal_sceneDetail(scene.id)">{{ index + 1 }}</td>
              <!-- 何ページから -->
              <td v-if="scene.first_page && scene.final_page != 1000">p.{{ scene.first_page }}<span v-if="scene.final_page"> ~ p.{{ scene.final_page }}</span></td>
              <td v-if="scene.first_page == 1 && scene.final_page == 1000">全シーン</td>
              <td v-if="!scene.first_page"></td>
              <!-- 登場人物 -->
              <td>{{ scene.character.name }}</td>
              <!-- 衣装名 -->
              <td type="button" class="list-button" @click="openModal_costumeDetail(scene.costume.id)">{{ scene.costume.name }}</td>
              <!-- 中間発表 -->
              <td v-if="scene.usage"><i class="fas fa-check fa-fw"></i></td>
              <td v-else></td> 
              <!-- 卒業公演 -->
              <td v-if="scene.usage_guraduation"><i class="fas fa-check fa-fw"></i></td>
              <td v-else></td>
              <!-- 上手 -->
              <td v-if="scene.usage_left"><i class="fas fa-check fa-fw"></i></td>
              <td v-else></td>
              <!-- 下手 -->
              <td v-if="scene.usage_right"><i class="fas fa-check fa-fw"></i></td>
              <td v-else></td>
              <!-- メモ -->
              <td v-if="scene.scene_comments.length">
                <div v-for="memo in scene.scene_comments"> {{ memo.memo }}</div>
              </td>
              <td v-else></td>
              <!-- 登録日時 -->
              <td>{{ scene.created_at }}</td>
              <!-- 更新日時 -->
              <td>{{ scene.updated_at }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="!showScenes.length">
        使用シーンは登録されていません。
      </div>
    </div>

    <div v-else class="phone">
        <div v-if="showScenes.length">
          <table>
            <div v-for="(scene, index) in showScenes">
              <tr>
                <!-- index -->
                <th class="th-non"></th>
                <td type="button" class="list-button td-color" @click="openModal_sceneDetail(scene.id)">{{ index + 1 }}</td>
              </tr>
              <tr>
                <!-- ページ数 -->
                <th>ページ数</th>
                <td v-if="scene.first_page && scene.final_page != 1000">p.{{ scene.first_page }}<span v-if="scene.final_page"> ~ p.{{ scene.final_page }}</span></td>
              <td v-if="scene.first_page == 1 && scene.final_page == 1000">全シーン</td>
              <td v-if="!scene.first_page"></td>
              </tr>
              <tr>
                <!-- 登場人物 -->
                <th>登場人物</th>
                <td>{{ scene.character.name }}</td>
              </tr>
              <tr>
                <!-- 衣装 -->
                <th>衣装</th>
                <td type="button" class="list-button" @click="openModal_costumeDetail(scene.costume.id)">{{ scene.costume.name }}</td>
              </tr>
              <tr>
                <!-- 中間発表 -->
                <th>中間</th>
                <td v-if="scene.usage"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
              </tr>
              <tr>
                <!-- 卒業公演 -->
                <th>卒業</th>
                <td v-if="scene.usage_guraduation"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
              </tr>
              <tr>
                <!-- 上手 -->
                <th>上手</th>
                <td v-if="scene.usage_left"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
              </tr>
              <tr>
                <!-- 下手 -->
                <th>下手</th>
                <td v-if="scene.usage_right"><i class="fas fa-check fa-fw"></i></td>
                <td v-else></td>
              </tr>
              <tr>
                <!-- メモ -->
                <th>メモ</th>
                <td v-if="scene.scene_comments.length">
                  <div v-for="memo in scene.scene_comments"> {{ memo.memo }}</div>
                </td>
                <td v-else></td>
              </tr>
              <tr>
                <!-- 登録日時 -->
                <th>登録日時</th>
                <td>{{ scene.created_at }}</td>
              </tr>
              <tr>
                <!-- 更新日時 -->
                <th>更新日時</th>
                <td>{{ scene.updated_at }}</td>
              </tr>
            </div>
          </table>
        </div>

        <div v-else>
          使用シーンは登録されていません。 
        </div>
      </div>
      
    <detailScene :postScene="postScene" v-show="showContent" @close="closeModal_sceneDetail" />
    <detailCostume :postCostume="postCostume" v-show="showContent_costume" @close="closeModal_costumeDetail" /> 
  </div>
</template>

<script>
  import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util';

  import detailScene from '../components/Detail_Scene.vue';
  import detailCostume from '../components/Detail_Costume.vue';
  import searchScene from '../components/Search_Scene.vue';
  import ExcelJS from 'exceljs';

  export default {
    // このページの上で表示するコンポーネント
    components: {
      detailScene,
      detailCostume,
      searchScene
    },
    data() {
      return{
        // 画面サイズ取得
        sizeScreen: 1, // 0:パソコン, 1:　スマホ
        // 取得するデータ
        scenes: [],
        // 表示するデータ
        showScenes: [],
        // ページの並び順
        page_order: [],
        // シーン詳細
        showContent: false,
        postScene: "",
        // 衣装詳細
        showContent_costume: false,
        postCostume: "",
        // シーン検索カスタム
        showContent_search: false,
        postSearch: ""
      }
    },
    watch: {
      $route: {
        async handler () {
          await this.fetchScenes();
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
      // 使用シーン一覧を取得
      async fetchScenes () {
        const response = await axios.get('/api/scenes');
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.page_order[0] = 1000;
        for(let i=1; i < 100; i++ ){
          this.page_order[i] = i;
        }
        
        this.scenes = response.data; // オリジナルデータ
        this.showScenes = JSON.parse(JSON.stringify(this.scenes));

        this.showScenes.sort((a, b) => {
          // 最初のページで並び替え
          if(a.first_page !== b.first_page){
            return a.first_page - b.first_page
          }
          // 最後のページで並び替え
          if(a.final_page !== b.final_page){
            return this.page_order.indexOf(a.final_page) - this.page_order.indexOf(b.final_page);
          }
          // 登場人物idで並び替え
          if(a.character_id !== b.character_id){
            return a.character_id - b.character_id;
          }
          return 0;
        });
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
      openModal_searchScene (number) {
        this.showContent_search = true;
        this.postSearch = number;
      },
      // 検索カスタムのモーダル非表示
      closeModal_searchScene(sort, name, refine) {
        this.showContent_search = false;
        if(sort !== null && refine !== null){
          let array_original = this.scenes.filter((a) => eval(refine));
          let array = array_original;
          
          if(sort === "character"){
            array.sort((a, b) => a.character_id - b.character_id);
          }else if(sort === "created_at"){
            array.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
          }else if(sort === "updated_at"){
            array.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
          }else{
            array.sort((a, b) => {
              // 最初のページで並び替え
              if(a.first_page !== b.first_page){
                return a.first_page - b.first_page
              }
              // 最後のページで並び替え
              if(a.final_page !== b.final_page){
                return this.page_order.indexOf(a.final_page) - this.page_order.indexOf(b.final_page);
              }
              // 登場人物idで並び替え
              if(a.character_id !== b.character_id){
                return a.character_id - b.character_id;
              }
              return 0;
            });
          }

          this.showScenes = array;
        }      
      },

      // 使用シーン詳細のモーダル表示 
      openModal_sceneDetail (id) {
        this.showContent = true;
        this.postScene = id;
      },
      // 使用シーン詳細のモーダル非表示
      async closeModal_sceneDetail() {
        this.showContent = false;
        await this.fetchScenes();
      },

      // 衣装詳細のモーダル表示 
      openModal_costumeDetail (id) {
        this.showContent_costume = true;
        this.postCostume = id;
      },
      // 衣装詳細のモーダル非表示
      async closeModal_costumeDetail() {
        this.showContent_costume = false;
        await this.fetchScenes();
      },

      // // ダウンロード
      // downloadScenes() {
      //   const response = axios.post('/api/scenes_list', this.showScenes);
      // }

      // ダウンロード
      async downloadScenes() {
        // ①初期化
        const workbook = new ExcelJS.Workbook(); // workbookを作成
        workbook.addWorksheet('Sheet1'); // worksheetを追加
        const worksheet = workbook.getWorksheet('Sheet1'); // 追加したworksheetを取得

        // ②データを用意
        // 各列のヘッダー
        worksheet.columns = [
          { header: '何ページから', key: 'first_page', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '何ページまで', key: 'final_page', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '登場人物', key: 'character', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
          { header: '衣装', key: 'costume', width: 12, style: { alignment: {vertical: "middle", horizontal: "center" }}},
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

        this.showScenes.forEach((scene, index) => {
          let datas = [];
          datas.push(scene.first_page);
          datas.push(scene.final_page);

          datas.push(scene.character.name);

          datas.push(scene.costume.name);

          if(scene.usage){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(scene.usage_guraduation){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(scene.usage_left){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(scene.usage_right){
            datas.push('〇');
          }else{
            datas.push(null);
          }

          if(scene.scene_comments.length){
            scene.scene_comments.forEach((comment, index_comment) => {
              if(index_comment){
                const remove_data = datas.splice(datas.length-1, datas.length-1, datas[datas.length-1]+'<br>'+comment.memo);
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
        const filename = 'Scenes_list_' + 'all' + '_' + today + '.xlsx';
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