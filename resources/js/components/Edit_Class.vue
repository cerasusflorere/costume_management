<template>
    <div class="overlay" @click.self="$emit('close')">
      <div class="content content-confirm-dialog panel">
          <form class="form"  @submit.prevent="confirm_class">
          <!-- 衣装分類 -->
          <label for="class_edit">衣装分類</label>
          <input type="text" id="class_edit" class="form__item" v-model="editForm_class.class" required>
  
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
    name: 'editClass',
    components: {
      confirmDialog_Edit,
      confirmDialog_Delete
    },
    props: {
      getClass: {
        type: Number,
        required: true
      }
    },
    // データ
    data() {
      return {
        class_edit: [],
        editForm_class: {
          id: null,
          class: null
        },
        classes: [],
        // 変更confirm
        showContent_confirmEdit: false,
        postMessage_Edit: "",
        // 削除confirm
        showContent_confirmDelete: false,
        postMessage_Delete: ""
      }
    },
    watch: {
      getClass: {
        async handler(getClass) {
          if(this.getClass){
            await this.fetchClass_edit();
            await this.fetchClasses();
          }        
        },
        immediate: true,
      }
    },
    methods: {
      // 衣装分類の詳細を取得
      async fetchClass_edit () {
        const response = await axios.get('/api/informations/classes/'+ this.getClass);
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.class_edit = response.data;
        this.editForm_class.id = this.class_edit.id;
        this.editForm_class.class = this.class_edit.class;
      },
  
      // 衣装分類を取得
      async fetchClasses () {
        const response = await axios.get('/api/informations/classes');
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.classes = response.data;
      },
  
      // 確認する
      confirm_class () {
        let classes = 0;
        this.classes.forEach((costume) => {
          if(costume.class === this.editForm_class.class && costume.id !== this.editForm_class.id) {
            classes = 1;
            return false;
          }
        }, this);
  
        if(this.class_edit.id === this.editForm_class.id && this.class_edit.class !== this.editForm_class.class && !classes){
          this.openModal_confirmEdit();
        }else if(classes){
          alert('同じ名前は登録できません。');
        }else{
          alert('元の名前と同じです！変更するなら違う名前にしてください！');
        }
      },
  
      // 編集confirmのモーダル表示 
      openModal_confirmEdit () {
        this.showContent_confirmEdit = true;
        this.postMessage_Edit = '以下のように編集します。\n衣装分類：' + this.editForm_class.class;
      },
      // 編集confirmのモーダル非表示_OKの場合
      async closeModal_confirmEdit_OK() {
        this.showContent_confirmEdit = false;
        await this.editClass();
      },
      // 編集confirmのモーダル非表示_Cancelの場合
      closeModal_confirmEdit_Cancel() {
        this.showContent_confirmEdit= false;
      },
  
      // 編集する
      async editClass () {
        const response = await axios.post('/api/informations/classes/'+ this.class_edit.id, {
            class: this.editForm_class.class
        });
        
        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.class_edit.class = this.editForm_class.class;
  
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '衣装分類の名前が変更されました！',
          timeout: 6000
        });
  
        this. $emit('close');
      },
  
      // 削除confirmのモーダル表示 
      openModal_confirmDelete (id) {
        this.showContent_confirmDelete = true;
        this.postMessage_Delete = 'この衣装分類を削除すると、紐づけられてたこの方が所有するする衣装も全て削除されます。\n本当に削除しますか？';
      },
      // 削除confirmのモーダル非表示_OKの場合
      async closeModal_confirmDelete_OK() {
        await this.deletClass();
        this.showContent_confirmDelete = false;
        this.$emit('close');
      },
      // 削除confirmのモーダル非表示_Cancelの場合
      closeModal_confirmDelete_Cancel() {
        this.showContent_confirmDelete = false;
      },
  
      // 削除する
      async deletClass() {
        const response = await axios.delete('/api/informations/classes/'+ this.class_edit.id);
  
        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }
  
        if (response.status !== 200) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
  
        this.class_edit.id = null;
        this.class_edit.class= null;
        this.editForm_class.id = null;
        this.editForm_class.class = null;
  
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '衣装分類が1つ削除されました！',
          timeout: 6000
        });
  
        this.$emit('close');
      }
    }
  }
  </script>