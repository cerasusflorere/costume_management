<template>
  <div v-bind:class="[overlay_class === 1 ? 'overlay' : 'overlay overlay-custom']" @click.self="$emit('close')">
    <div class="content content-detail panel" ref="content_detail_costume">
      <div class="area--detail-box">
        <!--- 閲覧/編集 -->
        <div class="form__button button--area--detail-box">
          <button type="button" v-show="tab === 1" class="button button--inverse" @click="alterTab"><i class="fas fa-edit fa-fw"></i>編集</button>
          <button type="button" v-show="tab === 2" class="button button--inverse" @click="alterTab"><i class="fas fa-eye fa-fw"></i>閲覧</button>
        </div>

        <div v-show="tab === 1">
          <div class="detail-box">
            <!-- 写真 -->
            <div>
              <div v-if="costume.url" class="detail-box--img">
                <img :src="costume.url" :alt="costume.name"></img>
              </div>
              <div v-else>
                <img :src="costume.url" :alt="costume.name"></img>
              </div>
            </div>
            <!-- 詳細 -->
            <div>
              <!--- 削除ボタン -->
              <div class="form__button">
                <button type="button" class="button button--inverse" @click="openModal_confirmDelete"><i class="fas fa-trash fa-fw"></i>削除</button>
              </div>
              <confirmDialog_Delete :confirm_dialog_delete_message="postMessage_Delete" v-show="showContent_confirmDelete" @Cancel_Delete="closeModal_confirmDelete_Cancel" @OK_Delete="closeModal_confirmDelete_OK"/>
            
              <div>
                <h1>{{ costume.name }}</h1>
              </div>

              <div>分類: <span>{{ costume.class.class }}</span></div>

              <div>色: <span v-if="costume.color">{{ costume.color.color }}</span></div>
              
              <div>持ち主: <span v-if="costume.owner">{{ costume.owner.name }}</span></div>

              <div>貸したか: <span v-if="costume.lend" class="usage-show"><i class="fas fa-check fa-fw"></i></span></div>

              <div>ピッコロに持ってきたか: <span v-if="costume.location" class="usage-show"><i class="fas fa-check fa-fw"></i></span></div>

              <div>
                作るかどうか: 
                <span v-if="costume.handmade === 1" class="usage-show">完</span>
                <span v-else-if="costume.handmade === 2" class="usage-show">仕</span>
                <span v-else-if="costume.handmade === 3" class="usage-show">未</span>
              </div>

              <div>これで決定か: <span v-if="costume.decision" class="usage-show"><i class="fas fa-check fa-fw"></i></span></div>

              <div>
                <!-- 中間発表 -->
                <span v-if="costume.usage" class="usage-show">Ⓟ</span>
                <!-- 卒業公演 -->
                <span v-if="costume.usage_guraduation" class="usage-show">Ⓖ</span>
                <!-- 上手 -->
                <span v-if="costume.usage_left" class="usage-show">㊤</span>
                <!-- 下手 -->
                <span v-if="costume.usage_right" class="usage-show">㊦</span>
              </div>

              <div>
                <label>メモ:</label>
                <ul v-if="costume.costume_comments.length" >
                  <li v-for="comment in costume.costume_comments">
                    <div>{{ comment.memo }}</div>
                  </li>
                </ul>
              </div>

              <div>
                <label>シーン:</label>
                  <ol v-if="costume.scenes.length">
                    <li v-for="scene in costume.scenes">
                      <span class="costume-detail--scene-area">
                        <div>
                          <!-- 名前 -->
                          <span>{{ scene.character.name }}</span>
                          <!-- 何ページ -->
                          <span v-if="scene !== null && scene.first_page !== null && scene.final_page !== 1000">: p.{{ scene.first_page }} 
                            <span v-if="scene !== null && scene.final_page !== null && scene.final_page !== 1000"> ~ p.{{ scene.final_page}}</span>
                          </span>
                          <span v-if="scene !== null && scene.first_page === 1 && scene.final_page === 1000">
                            : 全シーン
                          </span>
                        </div>

                        <div>
                          <!-- 決定かどうか -->
                          <span v-if="scene.decision" class="usage-show">決定</span>

                          <!-- 使用状況 -->
                          <span v-if="scene.usage" class="usage-show">Ⓟ</span>
                          <span v-if="scene.usage_guraduation" class="usage-show">Ⓖ</span>
                          <span v-if="scene.usage_left" class="usage-show">㊤</span>
                          <span v-if="scene.usage_right" class="usage-show">㊦</span>
                        </div>
                        
                        <!-- メモ -->
                        <div>
                          <ul v-if="scene.scene_comments.length">
                            <li v-for="comment in scene.scene_comments">
                              <div>{{ comment.memo }}</div>
                            </li>
                          </ul>
                        </div>

                      </span>
                    </li>
                  </ol>
              </div> 
            </div>
          </div>
        </div>
    
        <!-- 編集 -->
        <div v-show="tab === 2" class="edit-area">
          <form class="detail-box"  @submit.prevent="confirmCostume">
            <div>
              <!-- 写真 -->
              <div v-show="editForm_costume.url && editForm_costume.photo === 1">
                <img :src="costume.url" :alt="costume.name">
                <button type="button" class="button button--inverse" @click="deletePhoto">×</button>
              </div>
              <div v-show="!editForm_costume.photo">
                <input id="photo_edit" class="form__item" type="file" @change="onFileChange">
              </div>
              <div v-show="editForm_costume.photo !== 1 && editForm_costume.photo">
                <output class="form__output" v-if="preview">
                  <img :src="preview" alt="" style="max-height: 12em">
                </output>
                <button type="button" class="button button--inverse" @click="resetPhoto">×</button>
              </div>
            </div>         

            <!-- 詳細 -->
            <div>
              <!--- 送信ボタン -->
              <div class="form__button">
                <button type="submit" class="button button--inverse"><i class="fas fa-edit fa-fw"></i>編集</button>
              </div>

              <div>
                <label for="costume_name_edit">衣装名</label>
                <input type="text" id="costume_name_edit" class="form__item" v-model="editForm_costume.name" @input="handleNameInput" placeholder="衣装" required>
                <!-- <input type="text" name="furigana" id="costume_furigana_edit" class="form__item form__item--furigana" v-model="editForm_costume.kana" placeholder="ふりがな" required> -->
                <input type="text" name="furigana" :id="costume.id" class="form__item form__item--furigana" v-model="editForm_costume.kana" placeholder="ふりがな" required></input>
              </div>

              <div>
                <select id="costume_class_edit" class="form__item"  v-model="editForm_costume.class_id" required>
                  <option disabled value="">衣装分類一覧</option>
                  <option v-for="classes in optionClasses" v-bind:value="classes.id">
                    {{ classes.class }}
                  </option>
                </select>
              </div>

              <div>
                <!-- 色 -->
                <label for="color_class">色</label>
                <select class="form__item" v-model="editForm_costume.color.color_class.color_class" v-on:change="selectedColor">
                  <option value=0>色分類</option>
                  <option v-for="(value, key) in optionColors">
                    {{ key }}
                  </option>
                </select>

                <select class="form__item" v-model="editForm_costume.color_id">
                  <option value=0>色一覧</option>
                  <option v-if="selectedColors" v-for="color in selectedColors"
                          v-bind:value="color.id">
                    {{ color.color }}
                  </option>
                </select>
              </div>
              
              <div>
                <label for="costume_owner_edit">所有者:</label> 
                <select id="costume_owner_edit" class="form__item"  v-model="editForm_costume.owner_id">
                  <option value=0>持ち主一覧</option>
                  <option v-for="owner in optionOwners" v-bind:value="owner.id">
                    {{ owner.name }}
                  </option>
                </select>
              </div>

              <!-- 貸し借りしたたか -->
              <div  class="checkbox-area--together">
                <label for="costume_lend_edit" class="form__check__label">貸したか</label>
                <input type="checkbox" id="costume_lend_edit" class="form__check__input" v-model="editForm_costume.lend">
              </div>

              <!-- ピッコロに持ってきたか -->
              <div  class="checkbox-area--together">
                <label for="costume_location_edit" class="form__check__label">ピッコロに持ってきたか</label>
                <input type="checkbox" id="costume_location_edit" class="form__check__input" v-model="editForm_costume.location">
              </div>

              <!-- 作る必要があるか -->
              <div class="checkbox-area--together">
                <label for="costume_handmade_edit">作る必要がある</label>
                <input type="checkbox" id="costume_handmade_edit" v-model="editForm_costume.handmade"></input>

                <div class="checkbox-area--together">
                <!-- 作る必要があるなら -->
                  <input type="radio" id="costume_handmade_complete_edit" :disabled="!editForm_costume.handmade" value=1 v-model="editForm_costume.handmade_complete"></input>
                  <label for="costume_handmade_complete_edit">完成</label>

                  <input type="radio" id="costume_handmade_progress_edit" :disabled="!editForm_costume.handmade" value=2 v-model="editForm_costume.handmade_complete"></input>
                  <label for="costume_handmade_progress_edit">仕掛中</label>

                  <input type="radio" id="costume_handmade_unfinished_edit" :disabled="!editForm_costume.handmade" value=3 v-model="editForm_costume.handmade_complete"></input>
                  <label for="costume_handmade_unfinished_edit">未着手</label>
                </div>          
              </div>

              <!-- これで決定か -->
              <div  class="checkbox-area--together">
                <label for="costume_decision_edit" class="form__check__label">これで決定か</label>
                <input type="checkbox" id="costume_decision_edit" class="form__check__input" v-model="editForm_costume.decision">
              </div>

              <!-- 使用するか -->
              <div>
                <div  class="checkbox-area--together">
                  <label for="costume_usage_costume_edit" class="form__check__label">中間発表での使用</label>
                  <input type="checkbox" id="costume_usage_costume_edit" class="form__check__input" v-model="editForm_costume.usage">
                </div>
                
                <div  class="checkbox-area--together">
                  <label for="costume_usage_guraduation_costume_edit" class="form__check__label">卒業公演での使用</label>
                  <input type="checkbox" id="costume_usage_guraduation_scene_edit" class="form__check__input" v-model="editForm_costume.usage_guraduation" @change="selectGuraduation">
                </div>

                <div v-if="guradutaion_tag"  class="checkbox-area--together">
                  <input type="checkbox" id="costume_usage_left_costume_edit" class="form__check__input" value="left" v-model="editForm_costume.usage_left">
                  <label for="costume_usage_left_costume_edit" class="form__check__label">上手</label>

                  <input type="checkbox" id="costume_usage_right_costume_edit" class="form__check__input" value="right" v-model="editForm_costume.usage_right"></input>
                  <label for="costume_usage_right_costume_edit" class="form__check__label">下手</label>                
                </div>
              </div>

              <div>
                <label for="costume_comment_edit">メモ:</label>
                <ul v-if="editForm_costume.costume_comments.length" >
                  <li v-for="comment in editForm_costume.costume_comments">
                    <textarea class="form__item" v-model="comment.memo">{{ comment.memo }}</textarea>
                  </li>
                </ul>
                <div v-else>
                  <textarea id="costume_comment_edit" class="form__item" v-model="editForm_costume.memo" placeholder="メモ"></textarea>
                </div>
              </div>

              <!-- <div>
                <label>シーン:</label>
                <div v-if="editForm_costume.scenes.length && editForm_costume.scenes[0].id">
                  <ol>
                    <li v-for="(scene, index) in editForm_costume.scenes"> -->
                      <!-- 名前 -->
                      <!-- <label for="character_attr">登場人物</label>
                      <select class="form__item" v-model="scene.section" v-on:change="selected(index)">
                        <option disabled value="">登場人物属性</option>
                        <option v-for="(value, key) in optionCharacters">
                          {{ key }}
                        </option>
                      </select>
  
                      <select class="form__item" v-model="scene.character_id" required>
                        <option disabled value="">登場人物一覧</option>
                          <option v-if="selectedCharacters" v-for="characters in selectedCharacters[index]"
                           v-bind:value="characters.id">
                            {{ characters.name }}
                          </option>
                      </select> -->

                      <!-- ページ数 -->
                      <!-- <label>ページ数</label>
                      <div> p. <input class="form__item" v-model="scene.first_page">
                         ~ p. <input class="form__item" v-model="scene.final_page">
                      </div> -->

                      <!-- 使用するか -->
                      <!-- <div>
                        <label>中間発表での使用</label>
                        <input type="checkbox" class="form__check__input" v-model="scene.usage"></input>          
                      </div> -->
        
                      <!-- コメント -->
                      <!-- <label for="comment_scene">コメント</label>
                      <div>
                        <ul v-if="scene.scene_comments.length">
                          <li v-for="comment in scene.scene_comments">
                            <textarea class="form__item" v-model="comment.memo"></textarea>
                          </li>
                        </ul>
                        <div v-else>
                          <textarea class="form__item" v-model="scene.memo"></textarea>
                        </div>
                      </div>
                    </li>
                  </ol>
                </div>
  
                <div v-else> -->
                  <!-- 登場人物 -->
                  <!-- <label>登場人物</label>
                  <select class="form__item" v-model="editForm_costume.scenes[0].section" v-on:change="selected(0)">
                    <option disabled value="">登場人物属性</option>
                    <option v-for="(value, key) in optionCharacters">
                      {{ key }}
                    </option>
                  </select>

                  <select class="form__item" v-model="editForm_costume.scenes[0].character_id" required>
                    <option disabled value="">登場人物一覧</option>
                    <option v-if="selectedCharacters" v-for="characters in selectedCharacters"
                     v-bind:value="characters.id">
                      {{ characters.name }}
                    </option>
                  </select> -->

                  <!-- ページ数 -->
                  <!-- <label for="page">ページ数</label>
                  <small>例) 22, 24-25</small>
                  <input type="text"  id="page" class="form__item" v-model="editForm_costume.scenes[0].pages"> -->

                  <!-- 使用するか -->
                  <!-- <div class="form__check">
                    <label for="usage_scene" class="form__check__label">中間発表での使用</label>
                    <input type="checkbox" id="usage_scene" class="form__check__input" v-model="editForm_costume.scenes[0].usage" checked></input>          
                  </div> -->
        
                  <!-- コメント -->
                  <!-- <label for="comment_scene">コメント</label>
                  <textarea class="form__item" id="comment_scene" v-model="editForm_costume.scenes[0].scene_comments[0].memo"></textarea>
                </div>
              </div>  -->
            </div>
          </form>
          <confirmDialog_Edit :confirm_dialog_edit_message="postMessage_Edit" v-show="showContent_confirmEdit" @Cancel_Edit="closeModal_confirmEdit_Cancel" @OK_Edit="closeModal_confirmEdit_OK"/>
        </div>
      </div>
      
      <button type="button" @click="$emit('close')" class="button button--inverse button--area--detail-box">閉じる</button>
    </div>
  </div>
</template>

<script>
import { OK, UNPROCESSABLE_ENTITY } from '../util'

import confirmDialog_Edit from './Confirm_Dialog_Edit.vue'
import confirmDialog_Delete from './Confirm_Dialog_Delete.vue'
// ふりがな
import * as AutoKana from 'vanilla-autokana';

let autokana;

export default {
  // モーダルとして表示
  name: 'detailCostume',
  components: {
    confirmDialog_Edit,
    confirmDialog_Delete
  },
  props: {
    postCostume: {
      type: Number,
      required: true
    }
  },
  // データ
  data() {
    return {
      // 表示する衣装のデータ
      costume: [],
      // ページの並び順
      page_order: [], 
      // 編集データ
      editForm_costume: {
        id: null,
        name: null,
        kana: null,
        class: {
          class: null
        },
        class_id: null,
        color: {
          color: null,
          color_class: {
            color_class: null
          },
        },
        character_id: null,
        owner: {
          name: ''
        },
        owner_id: '',
        lend: 0,
        location: 0,
        handmade: 0,
        handmade_complete: 1,
        decision: 0,
        url: '',
        public_id: '',
        usage: 0,
        usage_guraduation: 0,
        usage_left: 0,
        usage_right: 0,
        photo: 0,
        costume_comments: [],
        scenes: []
      },
      // 取得するデータ
      costumes: [],
      optionClasses: [],
      optionOwners: [],
      // 連動プルダウン(色)
      selectedColors: [],
      optionColors: [],
      // 連動プルダウン(登場人物)
      selectedCharacters: [],
      optionCharacters: [],
      // タブ
      tab: 1,
      // 卒業公演
      guradutaion_tag: 0,
      // overlayのクラス
      overlay_class: 1,
      // 写真プレビュー
      preview: null,
      // エラー
      errors: {
        photo: null,
        error: null,
      },
      // 編集confirm
      showContent_confirmEdit: false,
      postMessage_Edit: "",
      // ユニコード
      first_uni: 9312, // ①
      final_uni: 9331,  // ⑳
      // 編集範囲
      editCostumeMode_detail: "",
      editCostumeMode_memo: "",
      // 削除confirm
      showContent_confirmDelete: false,
      postMessage_Delete: ""
    }
  },
  watch: {
    postCostume: {
      async handler(postCostume) {
        if(this.postCostume){
          this.overlay_class = 1;
          await this.fetchCharacters(); // 最初にしないと間に合わない
          await this.fetchColors(); 
          await this.fetchCostume();
          await this.fetchClasses();
          await this.fetchOwners();
          await this.fetchCostumes();

          // ふりがなのinput要素のidは省略可能
          // 使用シーン登録時のid=costumeと被るから
          const autokana_id = '#'+ this.costume.id;
          autokana = AutoKana.bind(autokana_id);
          autokana.input = this.costume.kana;

          const content_dom = this.$refs.content_detail_costume;
          const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得

          if(content_rect.top < 0){
            this.overlay_class = 0;
          }else{
            this.overlay_class = 1;
          }
        }        
      },
      immediate: true,
    },
    editCostumeMode_memo: {
      async handler(editCostumeMode_memo){
        if(this.editCostumeMode_detail === 100 || this.editCostumeMode_memo === 100){
          await this.resetCostume();
          await this.fetchCostume();

          // 調整
          this.$nextTick(() => {
            const content_dom = this.$refs.content_detail_costume;
            const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
            if(content_rect.top < 0){
              this.overlay_class = 0;
            }else{
              this.overlay_class = 1;
            }
          });

          // メッセージ登録
          this.$store.commit('message/setContent', {
            content: '衣装が変更されました！',
            timeout: 6000
          });
        }else if(this.editCostumeMode_detail || this.editCostumeMode_memo){
          await this.openModal_confirmEdit();
        }else if(this.editCostumeMode_detail === 0 && this.editCostumeMode_memo === 0){
          alert('元のデータと同じです！変更してください');
          this.editCostumeMode_detail = "";
          this.editCostumeMode_memo = "";
        }
      },
      immediate: true,
    }
  },
  mounted() {
    // ふりがなのinput要素のidは省略可能
    // 使用シーン登録時のid=costumeと被るから
    // const autokana_id = '#'+ this.costume.id;
    // console.log(autokana_id);
    // autokana = AutoKana.bind(autokana_id);
    // console.log(autokana);
  },
  methods: {
    // 衣装の詳細を取得
    async fetchCostume () {
      this.tab = 1;
      const response = await axios.get('/api/costumes/'+ this.postCostume);

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.costume = response.data;
      this.editForm_costume.id = this.costume.id;
      this.editForm_costume.name = this.costume.name;
      this.editForm_costume.kana = this.costume.kana;

      this.editForm_costume.class_id = this.costume.class_id;
      this.editForm_costume.class.class = this.costume.class.class;
      
      if(this.costume.color_id){
        this.editForm_costume.color_id = this.costume.color_id;
        this.editForm_costume.color.color = this.costume.color.color;
        this.editForm_costume.color.color_class.color_class = this.costume.color.color_class.color_class;
        this.selectedColor();
      }else{
        this.costume.color_id = 0;
        this.editForm_costume.color_id = 0;
        this.editForm_costume.color.color = null;
        this.editForm_costume.color.color_class.color_class = null;
      }

      if(this.costume.owner_id){
        this.editForm_costume.owner_id = this.costume.owner_id;
        this.editForm_costume.owner.name = this.costume.owner.name;
      }else{
        this.editForm_costume.owner_id = 0;
        this.editForm_costume.owner.name = '';
      }

      this.editForm_costume.lend = this.costume.lend;

      this.editForm_costume.location = this.costume.location;

      this.editForm_costume.handmade = this.costume.handmade; // 0: 作らない、1: 完成、2: 仕掛中、3: 未着手

      if(this.costume.handmade){
        this.editForm_costume.handmade_complete = this.costume.handmade;
      }else{
        this.editForm_costume.handmade_complete = 1;
      }

      this.editForm_costume.decision = this.costume.decision;

      this.editForm_costume.url = this.costume.url;
      this.editForm_costume.public_id = this.costume.public_id;

      this.editForm_costume.usage = this.costume.usage;
      this.editForm_costume.usage_guraduation = this.costume.usage_guraduation;
      this.editForm_costume.usage_left = this.costume.usage_left;
      this.editForm_costume.usage_right = this.costume.usage_right;

      this.editForm_costume.costume_comments = JSON.parse(JSON.stringify(this.costume.costume_comments));
      const scenes = this.costume.scenes;
      this.sort_Standard(scenes);
      this.editForm_costume.scenes = JSON.parse(JSON.stringify(this.costume.scenes));

      if(this.costume.usage_guraduation){
        this.guradutaion_tag = 1;
      }else{
        this.guradutaion_tag = 0;
      }

      if(this.editForm_costume.public_id){
        this.editForm_costume.photo = 1; // 写真が登録されている（可能性：1のまま、0に変更（この時public_idは存在する）、写真バイナリ代入（この時public_idは存在する））
      }else{
        this.editForm_costume.photo = 0; // 写真が登録されていない（可能性：0のまま（この時pubic_idは存在しない）、写真バイナリ代入（この時public_idは存在しない））
      }
      this.preview = null;
      // if(!this.editForm_costume.scenes.length){
      //   this.editForm_costume.scenes[0] = Object.assign({}, this.editForm_costume.scenes[0], {character_id: null, section: '' , page: '', usage: '', scene_comments: []})
      //   this.editForm_costume.scenes[0].scene_comments[0] = Object.assign({}, this.editForm_costume.scenes[0].scene_comments[0], {memo: null})
      // }else{
      //   this.editForm_costume.scenes.forEach((scene,index) => {
      //     this.editForm_costume.scenes[index] = Object.assign({}, this.editForm_costume.scenes[index], {section: this.editForm_costume.scenes[index].character.section.section})
      //     this.selected(index)
      //   })
      // }
      this.editCostumeMode_detail = "";
      this.editCostumeMode_memo = "";

      // 調整
      this.$nextTick(() => {
        const content_dom = this.$refs.content_detail_costume;
        const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
        if(content_rect.top < 0){
          this.overlay_class = 0;
        }else{
          this.overlay_class = 1;
        }
      });
    },

    sort_Standard(array){
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

      this.costume.scenes = array;
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
        color_classes[color_class.color_class] = color_class.colors;
      });
      this.optionColors = color_classes;        
    },
    
    // 連動プルダウン
    selectedColor() {
      this.selectedColors = this.optionColors[this.editForm_costume.color.color_class.color_class];
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

    // 登場人物を取得
    async fetchCharacters () {
      const response = await axios.get('/api/informations/characters');

      if (response.statu !== 200) {
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

    // 連動プルダウン
    selected(index) {
      this.selectedCharacters[index] = this.optionCharacters[this.editForm_costume.scenes[index].section];
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
      this.editForm_costume.kana = autokana.getFurigana();
    },
    
    // タブ切り替え
    alterTab() {
      if(this.tab === 1){
        this.tab = 2;
      }else{
        this.tab = 1;
      }

      // 調整
      this.$nextTick(() => {
        const content_dom = this.$refs.content_detail_costume;
        const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
        if(content_rect.top < 0){
          this.overlay_class = 0;
        }else{
          this.overlay_class = 1;
        }
      });
    },

    // 写真を見せない
    deletePhoto(){
      this.editForm_costume.photo = 0;
      // 調整
      this.$nextTick(() => {
        const content_dom = this.$refs.content_detail_costume;
        const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
        if(content_rect.top < 0){
          this.overlay_class = 0;
        }else{
          this.overlay_class = 1;
        }
      });
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

      this.editForm_costume.photo = event.target.files[0];

      // 調整
      this.$nextTick(() => {
        const content_dom = this.$refs.content_detail_costume;
        const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
        if(this.overlay_class === 0 || content_rect.top < 0){
          this.overlay_class = 0;
        }else{
          this.overlay_class = 1;
        }
      });
    },

    // 画像をクリアするメソッド
    resetPhoto () {
      this.preview = null;
      this.editForm_costume.photo = 0;
      this.$el.querySelector('input[type="file"]').value = null;
      // 調整
      this.$nextTick(() => {
        const content_dom = this.$refs.content_detail_costume;
        const content_rect = content_dom.getBoundingClientRect(); // 要素の座標と幅と高さを取得
        if(content_rect.top < 0){
          this.overlay_class = 0;
        }else{
          this.overlay_class = 1;
        }
      });
    },

    // 卒業公演の使用にチェックが付いたか
    selectGuraduation() {
      if(!this.guradutaion_tag){
        this.guradutaion_tag = 1;
      }else{
        this.guradutaion_tag = 0;
        this.editForm_costume.usage_left = 0;
        this.editForm_costume.usage_right = 0;
      }
    },

    // 諸々リセット
    resetCostume() {
      this.editForm_costume.id = null;
      this.editForm_costume.name = null;
      this.editForm_costume.kana = null;
      this.editForm_costume.class.class = '';
      this.editForm_costume.class_id = '';
      this.editForm_costume.color.color = null;
      this.editForm_costume.color.color_class.color_class = null;
      this.editForm_costume.color_id = null;
      this.editForm_costume.owner.name = '';
      this.editForm_costume.owner_id = '';
      this.editForm_costume.lend = 0;
      this.editForm_costume.location = 0;
      this.editForm_costume.handmade = 0;
      this.editForm_costume.handmade_complete = 1;
      this.editForm_costume.decision = 0;
      this.editForm_costume.url = '';
      this.editForm_costume.public_id = '';
      this.editForm_costume.usage = 0;
      this.editForm_costume.usage_guraduation = 0;
      this.editForm_costume.usage_left = 0;
      this.editForm_costume.usage_right = 0;
      this.editForm_costume.photo = 0;
      this.editForm_costume.costume_comments = [];
      this.editForm_costume.scenes = [];
      // 卒業公演
      this.guradutaion_tag = 0;

      this.preview = null;
      this.editForm_costume.photo = 0;
      this.$el.querySelector('input[type="file"]').value = null;

      if(this.val){
        // 調整
        this.$nextTick(() => {
          const content_dom = this.$refs.content_detail_costume;
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

    // 編集エラー
    confirmCostume () {
      const regex_str = /[^ぁ-んー]/g; // ひらがな以外
      const regex_number = /[^0-9]/g; // 数字以外
      const regex_alf = /[^A-Z]/g; // アルファベット
      let kana = '';
      let kanas = [...this.editForm_costume.kana];
      let pattern_number = /^([0-9]\d*|0)$/; // 0~9の数字かどうか
      let pattern_alf = /^([A-Z]\d*)$/; // A~Zのアルファベットかどうか*いる
      let names = [...this.editForm_costume.name];
      let name_last = names[names.length-1];

      // kan正規表現
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
      });
      if(name_last){
        if(kana.slice( eval('-'+String(name_last).length))!== String(name_last) ){
          // 最後のマークが名前と一致しない場合追加する
          kana = kana + String(name_last);
        }
      }
      this.editForm_costume.kana = kana;

      if(this.editForm_costume.color_id === "0"){
        this.editForm_costume.color_id = 0;
      }

      if(this.editForm_costume.owner_id === "0"){
        this.editForm_costume.owner_id = 0;
      }

      if(!this.editForm_costume.handmade){
        this.editForm_costume.handmade = 0;
      }else{
        this.editForm_costume.handmade = this.editForm_costume.handmade_complete;
      }

      if(this.costume.id === this.editForm_costume.id && (this.costume.name !== this.editForm_costume.name || this.costume.kana !== this.editForm_costume.kana || ((this.costume.class_id !== this.editForm_costume.class_id) || (!this.costume.class_id && !this.editForm_costume.class_id)) || ((this.costume.color_id !== this.editForm_costume.color_id) || (!this.costume.color_id && !this.editForm_costume.color_id)) || ((this.costume.owner_id !== this.editForm_costume.owner_id) || (!this.costume.owner_id && !this.editForm_costume.owner_id)) || this.costume.lend !== this.editForm_costume.lend || this.costume.location !== this.editForm_costume.location || this.costume.handmade !== this.editForm_costume.handmade || this.costume.decision !== this.editForm_costume.decision || this.costume.usage !== this.editForm_costume.usage || this.costume.usage_guraduation !== this.editForm_costume.usage_guraduation || this.costume.usage_left !== this.editForm_costume.usage_left || this.costume.usage_right !== this.editForm_costume.usage_right) && ((this.costume.public_id && this.editForm_costume.photo === 1) || (!this.costume.public_id && !this.editForm_costume.photo))){
        // 怪しい
        // if(!this.costume.class_id && !this.editForm_costume.class_id && !this.costume.color_id && !this.editForm_costume.color_id && !this.costume.owner_id && !this.editForm_costume.owner_id){
        //   this.editCostumeMode_detail = 0;
        // }else{
          // 写真をアップデートしない
          this.editCostumeMode_detail = 1; // 'photo_non_update'
        // }        
      }else if(this.costume.id === this.editForm_costume.id && !this.costume.public_id && this.editForm_costume.photo && this.editForm_costume.photo !== 1){
        // 写真新規
        this.editCostumeMode_detail = 2; // 'photo_store'
      }else if(this.costume.id === this.editForm_costume.id && this.costume.public_id && !this.editForm_costume.photo){
        // 写真削除
        this.editCostumeMode_detail = 3; //'photo_delete'
      }else if(this.costume.id === this.editForm_costume.id && this.costume.public_id && this.editForm_costume.photo && this.editForm_costume.photo !== 1){
        // 写真アップデート
        this.editCostumeMode_detail = 4; //'photo_update'
      }else{
        this.editCostumeMode_detail = 0;
      }

      if(this.costume.id === this.editForm_costume.id && !this.costume.costume_comments.length && this.editForm_costume.memo){
        // メモ新規投稿
        this.editCostumeMode_memo = 1; // 'memo_store'
      }else if(this.costume.id === this.editForm_costume.id && this.costume.costume_comments.length){
        if(this.costume.id === this.editForm_costume.id && this.costume.costume_comments[0].id && !this.editForm_costume.costume_comments[0].memo){
          // メモ削除
          this.editCostumeMode_memo = 2; //'memo_delete'
        }else if(this.costume.id === this.editForm_costume.id && this.costume.costume_comments[0].id && this.costume.costume_comments[0].memo !== this.editForm_costume.costume_comments[0].memo){
          // メモアップデート
          this.editCostumeMode_memo = 3; // 'memo_update'
        }else{
          this.editCostumeMode_memo = 0;
        }
      }else{
        this.editCostumeMode_memo = 0;
      }
    },

    // 編集confirmのモーダル表示 
    openModal_confirmEdit () {
      this.showContent_confirmEdit = true;

      if(this.editForm_costume.owner_id !== 0){
          this.optionOwners.forEach((owner) => {
          if(owner.id === this.editForm_costume.owner_id){
            this.editForm_costume.owner.name = owner.name;
            return false;
          }
        }, this);
      }else{
        this.editForm_costume.owner.name = '';
      }
      
      if(this.editForm_costume.color_id !== 0){
          Object.keys(this.optionColors).forEach((colors) => {
          if(colors === this.editForm_costume.color.color_class.color_class){
            this.optionColors[colors].forEach((color) => {
              if(color.id === this.editForm_costume.color_id){
                this.editForm_costume.color.color = color.color;
                return false;
              }
            }, this);
          }        
        }, this);
      }else{
        this.editForm_costume.color.color = '';
      }
      

      let lend = 'てない';
      let location = '持ってきてない';
      let handmade = '作らない';
      let decision = 'してない';
      let usage = '';
      let usage_guraduation = '';
      let usage_left = '';
      let usage_right = '';

      if(this.editForm_costume.lend){
        lend = 'た';
      }

      if(this.editForm_costume.location) {
        location = '持ってきてる';
      }

      if(this.editForm_costume.handmade == 1){
        handmade = '作る: 完成';
      }else if(this.editForm_costume.handmade == 2){
        handmade = '作る: 仕掛中';
      }else if(this.editForm_costume.handmade == 3){
        handmade = '作る: 未着手';
      }

      if(this.editForm_costume.decision) {
        decision = 'してる';
      }

      if(this.editForm_costume.usage) { 
        usage = 'Ⓟ ';
      }
      if(this.editForm_costume.usage_guraduation) { 
        usage_guraduation = 'Ⓖ ';
      }
      if(this.editForm_costume.usage_left) {
        usage_left = '㊤ ';
      }
      if(this.editForm_costume.usage_right){
        usage_right = '㊦';
      }

      let memos = [];
      this.editForm_costume.costume_comments.forEach((memo, index) => {
        if(memo.memo && index !== this.editForm_costume.costume_comments.length - 1){
          memos.push(memo.memo+'\n　　　');
        }else if(memo.memo){
          memos.push(memo.memo);
        }
      }, this);
      memos.push(this.editForm_costume.memo);

      // 写真は出ない
      let photo = '変更する';
      if(this.editCostumeMode_detail === 1 || this.editCostumeMode_detail === 0){
        photo = '変更しない';
      }

      this.postMessage_Edit = '以下のように編集します。\n衣装名：'+this.editForm_costume.name+'\nふりがな：'+this.editForm_costume.kana + '\n分類：'+this.editForm_costume.class.class + '\n色：'+this.editForm_costume.color.color + '\n持ち主：'+this.editForm_costume.owner.name + '\n貸し：'+lend + '\nピッコロに：'+location + '\n'+handmade + '\n決定：'+decision +'\n使用状況：'+usage+usage_guraduation+usage_left+usage_right+'\nメモ：'+memos+'\n写真：'+photo;
    },
    // 編集confirmのモーダル非表示_OKの場合
    async closeModal_confirmEdit_OK() {
      this.showContent_confirmEdit = false;
      if(this.editCostumeMode_detail){
        await this.editCostume();
      }
      if(this.editCostumeMode_memo){
        await this.editCostume_memo();
      }
    },
    // 編集confirmのモーダル非表示_Cancelの場合
    closeModal_confirmEdit_Cancel() {
      this.showContent_confirmEdit= false;
      this.editForm_costume.owner = "";
      this.editCostumeMode_detail = "";
      this.editCostumeMode_memo = "";
      if(this.editForm_costume.handmade){
        this.editForm_costume.handmade = true;
      }
    },

    // 基本情報を編集する
    async editCostume () {
      // 準備
      if(this.editForm_costume.color_id === 0){
        this.editForm_costume.color_id = '';
      }

      if(this.editForm_costume.owner_id === 0){
        this.editForm_costume.owner_id = '';
      }
      if(this.editCostumeMode_detail === 1){
        // 写真は変更しない
        const response = await axios.post('/api/costumes/'+ this.costume.id, {
          method: 'photo_non_update',
          name: this.editForm_costume.name,
          kana: this.editForm_costume.kana,
          class_id: this.editForm_costume.class_id,
          color_id: this.editForm_costume.color_id,
          owner_id: this.editForm_costume.owner_id,
          lend: this.editForm_costume.lend,
          location: this.editForm_costume.location,
          handmade: this.editForm_costume.handmade,
          decision: this.editForm_costume.decision,
          usage: this.editForm_costume.usage,
          usage_guraduation: this.editForm_costume.usage_guraduation,
          usage_left: this.editForm_costume.usage_left,
          usage_right: this.editForm_costume.usage_right
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 204) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
        
        this.editCostumeMode_detail = 100;
        if(this.editCostumeMode_memo === 0){
          this.editCostumeMode_memo = 100;
        }

      }else if(this.editCostumeMode_detail === 2){
        // 写真新規投稿
        const formData = new FormData();
        formData.append('method', 'photo_store');
        formData.append('name', this.editForm_costume.name);
        formData.append('kana', this.editForm_costume.kana);
        formData.append('class_id', this.editForm_costume.class_id);
        formData.append('color_id', this.editForm_costume.color_id);
        formData.append('owner_id', this.editForm_costume.owner_id);
        formData.append('lend', this.editForm_costume.lend);
        formData.append('location', this.editForm_costume.location);
        formData.append('handmade', this.editForm_costume.handmade);
        formData.append('decision', this.editForm_costume.decision);
        formData.append('usage', this.editForm_costume.usage);
        formData.append('usage_guraduation', this.editForm_costume.usage_guraduation);
        formData.append('usage_left', this.editForm_costume.usage_left);
        formData.append('usage_right', this.editForm_costume.usage_right);
        formData.append('photo', this.editForm_costume.photo);
        const response = await axios.post('/api/costumes/'+ this.costume.id, formData);

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 204) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.editCostumeMode_detail = 100;
        if(this.editCostumeMode_memo === 0){
          this.editCostumeMode_memo = 100;
        }

      }else if(this.editCostumeMode_detail === 3){
        // 写真削除
        const response = await axios.post('/api/costumes/'+ this.costume.id, {
          method: 'photo_delete',
          name: this.editForm_costume.name,
          kana: this.editForm_costume.kana,
          class_id: this.editForm_costume.class_id,
          color_id: this.editForm_costume.color_id,
          owner_id: this.editForm_costume.owner_id,
          lend: this.editForm_costume.lend,
          location: this.editForm_costume.location,
          handmade: this.editForm_costume.handmade,
          decision: this.editForm_costume.decision,
          public_id: this.editForm_costume.public_id,
          usage: this.editForm_costume.usage,
          usage_guraduation: this.editForm_costume.usage_guraduation,
          usage_left: this.editForm_costume.usage_left,
          usage_right: this.editForm_costume.usage_right
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 204) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.editCostumeMode_detail = 100;
        if(this.editCostumeMode_memo === 0){
          this.editCostumeMode_memo = 100;
        }

      }if(this.editCostumeMode_detail === 4){
        // 写真アップデート
        const formData = new FormData();
        formData.append('method', 'photo_update');
        formData.append('name', this.editForm_costume.name);
        formData.append('kana', this.editForm_costume.kana);
        formData.append('class_id', this.editForm_costume.class_id);
        formData.append('color_id', this.editForm_costume.color_id);
        formData.append('owner_id', this.editForm_costume.owner_id);
        formData.append('lend', this.editForm_costume.lend);
        formData.append('location', this.editForm_costume.location);
        formData.append('handmade', this.editForm_costume.handmade);
        formData.append('decision', this.editForm_costume.decision);
        formData.append('public_id', this.editForm_costume.public_id);
        formData.append('usage', this.editForm_costume.usage);
        formData.append('usage_guraduation', this.editForm_costume.usage_guraduation);
        formData.append('usage_left', this.editForm_costume.usage_left);
        formData.append('usage_right', this.editForm_costume.usage_right);
        formData.append('photo', this.editForm_costume.photo);
        const response = await axios.post('/api/costumes/'+ this.costume.id, formData);

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 204) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.editCostumeMode_detail = 100;
        if(this.editCostumeMode_memo === 0){
          this.editCostumeMode_memo = 100;
        }
      }
    },
    // メモを更新する
    async editCostume_memo() {
      if(this.editCostumeMode_memo === 1){
        // メモ新規投稿
        const response = await axios.post('/api/costume_comments', {
          costume_id: this.editForm_costume.id,
          memo: this.editForm_costume.memo
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.editCostumeMode_memo = 100;

      }else if(this.editCostumeMode_memo === 2){
        // メモ削除
        const response = await axios.delete('/api/costume_comments/'+ this.costume.costume_comments[0].id);

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 204) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.editCostumeMode_memo = 100;

      }else if(this.editCostumeMode_memo === 3){
        // メモアップデート
        const response = await axios.post('/api/costume_comments/'+ this.costume.costume_comments[0].id, {
          memo: this.editForm_costume.costume_comments[0].memo
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 204) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.editCostumeMode_memo = 100;

      }
    },

    // 削除confirmのモーダル表示 
    openModal_confirmDelete (id) {
      this.showContent_confirmDelete = true;
      this.postMessage_Delete = 'この衣装を削除すると、紐づけられてたこの衣装を使用するシーンも全て削除されます。\n本当に削除しますか？';
    },
    // 削除confirmのモーダル非表示_OKの場合
    async closeModal_confirmDelete_OK() {
      this.showContent_confirmDelete = false;
      this.$emit('close');
      await this.deletCostume();
    },
    // 削除confirmのモーダル非表示_Cancelの場合
    closeModal_confirmDelete_Cancel() {
      this.showContent_confirmDelete = false;
    },

    // 削除する
    async deletCostume() {
      const response = await axios.delete('/api/costumes/'+ this.costume.id);

      if (response.status === 422) {
        this.errors.error = response.data.errors;
        return false;
      }

      if (response.status !== 204) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.costume = [];
      this.resetCostume();

      // メッセージ登録
      this.$store.commit('message/setContent', {
        content: '衣装が1つ削除されました！',
        timeout: 6000
      });

      this.$emit('close');
    }
  }
}
</script>