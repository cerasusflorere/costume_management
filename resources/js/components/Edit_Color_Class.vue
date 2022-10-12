<template>
    <div class="overlay" @click.self="$emit('close')">
      <div class="content content-confirm-dialog panel">
        <form class="form"  @submit.prevent="confirm_color_class">
          <!-- 色分類 -->
          <label for="color_class_edit">色分類</label>
          <input type="text" id="color_class_edit" class="form__item" v-model="editForm_color_class.color_class" required>
  
          <!--- 変更ボタン -->
          <div class="form__button">
            <button type="submit" class="button button--inverse"><i class="fas fa-edit fa-fw"></i>変更</button>
          </div>
        </form>
        <confirmDialog_Edit :confirm_dialog_edit_message="postMessage_Edit" v-show="showContent_confirmEdit" @Cancel_Edit="closeModal_confirmEdit_Cancel" @OK_Edit="closeModal_confirmEdit_OK"/>
  
        <!--- 削除ボタン -->
        <div class="form__button">
          <button type="button" class="button button--inverse" @click="openModal_confirmDelete"><i class="fas fa-trash fa-fw"></i>削除</button>
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
    name: 'editColor_Class',
    components: {
      confirmDialog_Edit,
      confirmDialog_Delete
    },
    props: {
      getColor_Class: {
        type: Number,
        required: true
      }
    },
    // データ
    data() {
      return {
        color_class_edit: [],
        editForm_color_class: {
          id: null,
          color_class: null
        },
        color_classes: [],
        // 変更confirm
        showContent_confirmEdit: false,
        postMessage_Edit: "",
        // 削除confirm
        showContent_confirmDelete: false,
        postMessage_Delete: ""
      }
    },
    watch: {
      getColor_Class: {
        async handler(getColor_Class) {
          if(this.getColor_Class){
            await this.fetchColor_Class_edit();
            await this.fetchColor_Classes();
          }
        },
        immediate: true,
      }
    },
    methods: {
      // 色分類の詳細を取得
      async fetchColor_Class_edit () {
        const response = await axios.get('/api/informations/color_classes/'+ this.getColor_Class);
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_class_edit = response.data;
        this.editForm_color_class.id = this.color_class_edit.id;
        this.editForm_color_class.color_class = this.color_class_edit.color_class;
      },
  
      // 色分類を取得
      async fetchColor_Classes () {
        const response = await axios.get('/api/informations/color_classes');
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_classes = response.data;
      },
  
      // 編集エラー
      confirm_color_class () {
        let color_class = 0;
        this.color_classes.forEach((color_classes) => {
          if(color_classes.color_class === this.editForm_color_class.color_class && color_classes.id !== this.editForm_color_class.id) {
            color_class = 1;
            return false;
          }
        }, this);
  
        if(this.color_class_edit.id === this.editForm_color_class.id && this.color_class_edit.color_class !== this.editForm_color_class.color_class && !color_class){
          this.openModal_confirmEdit();
        }else if(color_class){
          alert('同じ名前は登録できません。');
        }else{
          // メッセージ登録
          alert('元の色分類名と同じです！変更するなら違う色分類名にしてください！');
        }
      },
  
      // 編集confirmのモーダル表示 
      openModal_confirmEdit () {
        this.showContent_confirmEdit = true;
        this.postMessage_Edit = '以下のように編集します。\n属性：' + this.editForm_color_class.color_class;
      },
      // 編集confirmのモーダル非表示_OKの場合
      async closeModal_confirmEdit_OK() {
        this.showContent_confirmEdit = false;
        await this.edit_color_class();
      },
      // 編集confirmのモーダル非表示_Cancelの場合
      closeModal_confirmEdit_Cancel() {
        this.showContent_confirmEdit= false;
      },
  
      // 編集する
      async edit_color_class () {
        const response = await axios.post('/api/informations/color_classes/'+ this.color_class_edit.id, {
            color_class: this.editForm_color_class.color_class
        });
  
        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_class_edit.color_class = this.editForm_color_class.color_class;
  
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '色分類が変更されました！',
          timeout: 6000
        });
  
        this.$emit('close');
      },
  
      // 削除confirmのモーダル表示 
      openModal_confirmDelete (id) {
        this.showContent_confirmDelete = true;
        this.postMessage_Delete = 'この色分類を削除すると、紐づけられてた登場人物、その登場人物が小道具を使用するシーンも全て削除されます。\n本当に削除しますか？';
      },
      // 削除confirmのモーダル非表示_OKの場合
      async closeModal_confirmDelete_OK() {
        await this.deletColor_Class();
        this.showContent_confirmDelete = false;
      },
      // 削除confirmのモーダル非表示_Cancelの場合
      closeModal_confirmDelete_Cancel() {
        this.showContent_confirmDelete = false;
      },
  
      // 削除する
      async deletColor_Class() {
        const response = await axios.delete('/api/informations/color_classes/'+ this.color_class_edit.id);
  
        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.color_class_edit.id = null;
        this.color_class_edit.color_class = null;
        this.editForm_color_class.id = null;
        this.editForm_color_class.color_class = null;
  
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '色分類が1つ削除されました！',
          timeout: 6000
        });
  
        this.$emit('close');
      }
    }
  }
  </script>