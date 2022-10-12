<template>
    <div class="overlay" @click.self="$emit('close')">
      <div class="content content-confirm-dialog panel">
          <form class="form"  @submit.prevent="confirm_color">
          <!--色 -->
          <label for="color_edit_area">色</label>
          <!-- 色分類-->
          <label for="color_class_edit">色分類</label>
          <select id="color_class_edit" class="form__item" v-model="editForm_color.color_class_id">
            <option disabled value="">色分類</option>
            <option v-for="color_class in optionColor_Classes" v-bind:value="color_class.id">
              {{ color_class.color_class }}
            </option>
          </select>
          <!-- 名前 -->
          <input type="text" id="color_class_color" class="form__item" v-model="editForm_color.color" required>
  
          <!--- 変更ボタン -->
          <div class="form__button">
            <button type="submit" class="button button--inverse"><i class="fas fa-edit fa-fw"></i>変更</button>
          </div>
        </form>
        <confirmDialog_Edit :confirm_dialog_edit_message="postMessage_Edit" v-show="showContent_confirmEdit" @Cancel_Edit="closeModal_confirmEdit_Cancel" @OK_Edit="closeModal_confirmEdit_OK"/>
  
        <!--- 削除ボタン -->
        <div class="form__button">
          <button type="button" class="button button--inverse"  @click="openModal_confirmDelete"><i class="fas fa-trash fa-fw"></i>削除</button>
        </div>
        <confirmDialog_Delete :confirm_dialog_delete_message="postMessage_Delete" v-show="showContent_confirmDelete" @Cancel_Delete="closeModal_confirmDelete_Cancel" @OK_Delete="closeModal_confirmDelete_OK"/>
        
          <button type="button" @click="$emit('close')" class="button button--inverse">閉じる</button>
      </div>
    </div>
  </template>
  
  <script>
  import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util'
  
  import confirmDialog_Edit from './Confirm_Dialog_Edit.vue'
  import confirmDialog_Delete from './Confirm_Dialog_Delete.vue'
  
  export default {
    // モーダルとして表示
    name: 'editColor',
    components: {
      confirmDialog_Edit,
      confirmDialog_Delete
    },
    props: {
      getColor: {
        type: Number,
        required: true
      }
    },
    // データ
    data() {
      return {
        color_edit: [],
        optionColor_Classes: [],
        colors: [],
        editForm_color: {
          id: null,
          color_class_id: null,
          color_class: null,
          color: null
        },
        // 変更confirm
        showContent_confirmEdit: false,
        postMessage_Edit: "",
        // 削除confirm
        showContent_confirmDelete: false,
        postMessage_Delete: ""
      }
    },
    watch: {
      getColor: {
        async handler(getColor) {
          if(this.getColor){
            await this.fetchColor_Classes();
            await this.fetchColor_edit();
            await this.fetchColors();
          }        
        },
        immediate: true,
      }
    },
    methods: {
      // 色分類を取得
      async fetchColor_Classes () {
        const response = await axios.get('/api/informations/color_classes');
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.optionColor_Classes = response.data;
      },
  
      // 色を取得
      async fetchColors () {
        const response = await axios.get('/api/informations/colors');
        
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.colors = response.data;
      },
  
      // 色の詳細を取得
      async fetchColor_edit () {
        const response = await axios.get('/api/informations/colors/'+ this.getColor);
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_edit = response.data;
        this.editForm_color.id = this.color_edit.id;
        this.editForm_color.color_class_id = this.color_edit.color_class.id;
        this.editForm_color.color_class = this.color_edit.color_class.color_class;
        this.editForm_color.color = this.color_edit.color;
      },
  
      // 色回す
      turnColors () {
        
      },
  
      // 確認する
      confirm_color () {
        let color = 0;
        this.colors.forEach((names) => {
          names.colors.forEach((name) => {
            if(name.color === this.editForm_color.color && name.id !== this.editForm_color.id) {
                color = 1;
              return false;
            }
          }, this);
        }, this);
  
        if(this.color_edit.id === this.editForm_color.id && (this.color_edit.color_class.id !== this.editForm_color.color_class_id || this.color_edit.color !== this.editForm_color.color) && !color){
          this.openModal_confirmEdit();
        }else if(color){
          alert('同じ名前は登録できません。');
        }else{
          alert('元の名前と同じです！変更するなら違う名前にしてください！');
        }
      },
  
      // 編集confirmのモーダル表示 
      openModal_confirmEdit () {
        this.showContent_confirmEdit = true;
        // セクション名はリアクティブじゃない
        this.optionColor_Classes.forEach((color_class) => {
          if(color_class.id === this.editForm_color.color_class_id) { 
            this.editForm_color.color_class = color_class.color_class;
            return false;
          }
        }, this);
        this.postMessage_Edit = '以下のように編集します。\n属性：' + this.editForm_color.color_class + '\n名前：' + this.editForm_color.color;
      },
      // 編集confirmのモーダル非表示_OKの場合
      async closeModal_confirmEdit_OK() {
        this.showContent_confirmEdit = false;
        await this.editColor();
      },
      // 編集confirmのモーダル非表示_Cancelの場合
      closeModal_confirmEdit_Cancel() {
        this.showContent_confirmEdit= false;
      },
  
      // 編集する
      async editColor () {
        const response = await axios.post('/api/informations/colors/'+ this.color_edit.id, {
          color_class_id: this.editForm_color.color_class_id,
          color: this.editForm_color.color
        });
  
        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }      
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_edit.color_class = this.editForm_color.color_class;
        this.color_edit.color_class_id = this.editForm_color.color_class_id;
        this.color_edit.color = this.editForm_color.color;
  
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '色の分類または色名が変更されました！',
          timeout: 6000
        });
  
        this. $emit('close');
      },
  
      // 削除confirmのモーダル表示 
      openModal_confirmDelete () {
        this.showContent_confirmDelete = true;
        this.postMessage_Delete = 'この色を削除すると、この色の衣装及びその衣装を使用するシーンが全て削除されます。\n本当に削除しますか？';
      },
      // 削除confirmのモーダル非表示_OKの場合
      async closeModal_confirmDelete_OK() {
        await this.deletColor();
        this.showContent_confirmDelete = false;
      },
      // 削除confirmのモーダル非表示_Cancelの場合
      closeModal_confirmDelete_Cancel() {
        this.showContent_confirmDelete = false;
      },
  
      // 削除する
      async deletColor() {
        const response = await axios.delete('/api/informations/colors/'+ this.color_edit.id);
  
        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_edit = null;
        this.editForm_color.id = null;
        this.editForm_color.color = null;
        this.editForm_color.color_class_id = null;
        this.editForm_color.color_class = null;
    
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '色が1つ削除されました！',
          timeout: 6000
        });
  
        this.$emit('close');
      }
    }
  }
  </script>