<template>
  <div class="overlay" @click.self="$emit('close')">
    <div class="content content-confirm-dialog panel">
      <form class="form"  @submit.prevent="confirm_section">
        <!-- 区分 -->
        <label for="section_edit">区分</label>
        <input type="text" id="section_edit" class="form__item" v-model="editForm_section.section" required>

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
  name: 'editSection',
  components: {
    confirmDialog_Edit,
    confirmDialog_Delete
  },
  props: {
    getSection: {
      type: Number,
      required: true
    }
  },
  // データ
  data() {
    return {
      section_edit: [],
      editForm_section: {
        id: null,
        section: null
      },
      sections: [],
      // 変更confirm
      showContent_confirmEdit: false,
      postMessage_Edit: "",
      // 削除confirm
      showContent_confirmDelete: false,
      postMessage_Delete: ""
    }
  },
  watch: {
    getSection: {
      async handler(getSection) {
        if(this.getSection){
          await this.fetchSection_edit();
          await this.fetchSections();
        }
      },
      immediate: true,
    }
  },
  methods: {
    // 区分の詳細を取得
    async fetchSection_edit () {
      const response = await axios.get('/api/informations/sections/'+ this.getSection);

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.section_edit = response.data;
      this.editForm_section.id = this.section_edit.id;
      this.editForm_section.section = this.section_edit.section;
    },

    // 区分を取得
    async fetchSections () {
      const response = await axios.get('/api/informations/sections');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.sections = response.data;
    },

    // 編集エラー
    confirm_section () {
      let section = 0;
      this.sections.forEach((name) => {
        if(name.section === this.editForm_section.section && name.id !== this.editForm_section.id) {
          section = 1;
          return false;
        }
      }, this);

      if(this.section_edit.id === this.editForm_section.id && this.section_edit.section !== this.editForm_section.section && !section){
        this.openModal_confirmEdit();
      }else if(section){
        alert('同じ名前は登録できません。');
      }else{
        // メッセージ登録
        alert('元の区分名と同じです！変更するなら違う区分名にしてください！');
      }
    },

    // 編集confirmのモーダル表示 
    openModal_confirmEdit () {
      this.showContent_confirmEdit = true;
      this.postMessage_Edit = '以下のように編集します。\n属性：' + this.editForm_section.section;
    },
    // 編集confirmのモーダル非表示_OKの場合
    async closeModal_confirmEdit_OK() {
      this.showContent_confirmEdit = false;
      await this.edit_section();
    },
    // 編集confirmのモーダル非表示_Cancelの場合
    closeModal_confirmEdit_Cancel() {
      this.showContent_confirmEdit= false;
    },

    // 編集する
    async edit_section () {
      const response = await axios.post('/api/informations/sections/'+ this.section_edit.id, {
        section: this.editForm_section.section
      });

      if (response.status === 422) {
        this.errors.error = response.data.errors;
        return false;
      }

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.section_edit.section = this.editForm_section.section;

      // メッセージ登録
      this.$store.commit('message/setContent', {
        content: '区分が変更されました！',
        timeout: 6000
      });

      this.$emit('close');
    },

    // 削除confirmのモーダル表示 
    openModal_confirmDelete (id) {
      this.showContent_confirmDelete = true;
      this.postMessage_Delete = 'この区分を削除すると、紐づけられてた登場人物、その登場人物が小道具を使用するシーンも全て削除されます。\n本当に削除しますか？';
    },
    // 削除confirmのモーダル非表示_OKの場合
    async closeModal_confirmDelete_OK() {
      await this.deletSection();
      this.showContent_confirmDelete = false;
    },
    // 削除confirmのモーダル非表示_Cancelの場合
    closeModal_confirmDelete_Cancel() {
      this.showContent_confirmDelete = false;
    },

    // 削除する
    async deletSection() {
      const response = await axios.delete('/api/informations/sections/'+ this.section_edit.id);

      if (response.status === 422) {
        this.errors.error = response.data.errors;
        return false;
      }

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.section_edit.id = null;
      this.section_edit.section = null;
      this.editForm_section.id = null;
      this.editForm_section.section = null;

      // メッセージ登録
      this.$store.commit('message/setContent', {
        content: '区分が1つ削除されました！',
        timeout: 6000
      });

      this.$emit('close');
    }
  }
}
</script>