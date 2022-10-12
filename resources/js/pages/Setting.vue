<template>
  <div class="container--small">
    <ul class="tab">
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 1 }"
        @click="tab = 1"
      >一覧</li>
      <li
        class="tab__item"
        :class="{'tab__item--active': tab === 2 }"
        @click="tab = 2"
      >登録</li>
    </ul>

    <!-- 閲覧 -->
    <div class="panel" v-show="tab === 1">
      <!-- 登場人物 -->
      <label for="section_view">登場人物</label>
      <div id="section_view">
        <ul v-if="gainSet.characters.length">
          <li v-for="section in gainSet.characters">
            <div type="button" class="list-button" @click="openModal_sectionEdit(section.id)">{{ section.section }}</div>
            <ul v-if="section.characters.length">
              <li v-for="name in section.characters">
                <div type="button" class="list-button" @click="openModal_characterEdit(name.id)">{{ name.name }}</div>                
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <editSection :getSection="postSection" v-show="showContent_section" @close="closeModal_sectionEdit" />
      <editCharacter :getCharacter="postCharacter" v-show="showContent_character" @close="closeModal_characterEdit" />
      
      <!-- 持ち主 -->
      <label for="owner_view">持ち主</label>
      <div id="owner_view">
        <ul v-if="gainSet.owners.length">
          <li v-for="owner in gainSet.owners">
            <div type="button" class="list-button" @click="openModal_ownerEdit(owner.id)">{{ owner.name }}</div> 
          </li>
        </ul>
      </div>
      <editOwner :getOwner="postOwner" v-show="showContent_owner" @close="closeModal_ownerEdit" />

      <!-- 衣装分類 -->
      <label for="class_view">衣装分類</label>
      <div id="class_view">
        <ul v-if="gainSet.classes.length">
          <li v-for="classes in gainSet.classes">
            <div type="button" class="list-button" @click="openModal_classEdit(classes.id)">{{ classes.class }}</div> 
          </li>
        </ul>
        <ul v-else style="list-style: none;">
          <li>
            <div>登録されていません。</div>
          </li>
        </ul>
      </div>
      <editClass :getClass="postClass" v-show="showContent_class" @close="closeModal_classEdit" />

      <!-- 色 -->
      <label for="color_class_view">衣装の色</label>
      <div id="color_class_view">
        <ul v-if="gainSet.colors.length">
          <li v-for="color_class in gainSet.colors">
            <div type="button" class="list-button" @click="openModal_color_classEdit(color_class.id)">{{ color_class.color_class }}</div>
            <ul v-if="color_class.colors.length">
              <li v-for="color in color_class.colors">
                <div type="button" class="list-button" @click="openModal_colorEdit(color.id)">{{ color.color }}</div>                
              </li>
            </ul>
            <ul v-else style="list-style: none;">
              <li>
                <div>登録されていません。</div>
              </li>
            </ul>
          </li>
        </ul>
        <ul v-else style="list-style: none;">
          <li>
            <div>登録されていません。</div>
          </li>
        </ul>
      </div>
      <editColor_Class :getColor_Class="postColor_Class" v-show="showContent_color_class" @close="closeModal_color_classEdit" />
      <editColor :getColor="postColor" v-show="showContent_color" @close="closeModal_colorEdit" />
      
    </div>

    <!-- 登録 -->
    <div class="panel" v-show="tab === 2">
      <!-- 区分登録 -->
      <form class="form"  @submit.prevent="register_section">
        <!-- 区分 -->
        <label for="section_input">区分</label>
        <input id="section_input" type="text" class="form__item" v-model="registerForm_section" placeholder="区分" required>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse">登録</button>
        </div>
      </form>

      <!-- 登場人物登録 -->
      <form class="form" @submit.prevent="register_character">
        <!-- 区分-->
        <label for="character_attr">区分</label>
        <select class="form__item" v-model="registerForm_character.section">
          <option disabled value="">区分</option>
          <option v-for="section in optionSections" v-bind:value="section.id">
            {{ section.section }}
          </option>
        </select>

        <!-- 登場人物 -->
        <label for="character_input">登場人物</label>
        <input id="character_input" type="text" class="form__item" v-model="registerForm_character.character" placeholder="登場人物" required>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse">登録</button>
        </div>
      </form>

      <!-- 持ち主登録 -->
      <form class="form" @submit.prevent="register_owner">
        <!-- 持ち主 -->
        <label for="owner_input">持ち主</label>
        <input id="owner_input" type="text" class="form__item" v-model="registerForm_owner" placeholder="持ち主" required>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse">登録</button>
        </div>
      </form>

      <!-- 衣装分類登録 -->
      <form class="form" @submit.prevent="register_class">
        <!-- 持ち主 -->
        <label for="class_input">衣装分類</label>
        <input id="class_input" type="text" class="form__item" v-model="registerForm_class" placeholder="分類" required>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse">登録</button>
        </div>
      </form>

      <!-- 色分類登録 -->
      <form class="form"  @submit.prevent="register_color_class">
        <!-- 色分類 -->
        <label for="color_class_input">色分類</label>
        <input id="color_class_input" type="text" class="form__item" v-model="registerForm_color_class" placeholder="色分類" required>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse">登録</button>
        </div>
      </form>

      <!-- 色登録 -->
      <form class="form" @submit.prevent="register_color">
        <!-- 色分類 -->
        <label for="color_class">色分類</label>
        <select class="form__item" v-model="registerForm_color.color_class">
          <option disabled value="">色分類</option>
          <option v-for="color_class in optionColor_Classes" v-bind:value="color_class.id">
            {{ color_class.color_class }}
          </option>
        </select>

        <!-- 色 -->
        <label for="color_input">色</label>
        <input id="color_input" type="text" class="form__item" v-model="registerForm_color.color" placeholder="色" required>

        <!--- 送信ボタン -->
        <div class="form__button">
          <button type="submit" class="button button--inverse">登録</button>
        </div>
      </form>

    </div>
  </div>
</template>

<script>
import { OK, CREATED, UNPROCESSABLE_ENTITY } from '../util'

import editSection from '../components/Edit_Section.vue'
import editCharacter from '../components/Edit_Character.vue'
import editOwner from '../components/Edit_Owner.vue'
import editClass from '../components/Edit_Class.vue'
import editColor_Class from '../components/Edit_Color_Class.vue'
import editColor from '../components/Edit_Color.vue'

export default {
  // このページの上で表示するコンポーネント
  components: {
    editSection,
    editCharacter,
    editOwner,
    editClass,
    editColor_Class,
    editColor
  },
  data() {
    return {
      // タブ切り替え
      tab: 1,
      // 取得するデータ
      optionSections: [],
      optionColor_Classes: [],
      gainSet: {
        characters: [],
        owners: [],
        classes: [],
        colors: []
      },
      // 区分編集
      showContent_section: false,
      postSection: "",
      // 登場人物編集
      showContent_character: false,
      postCharacter: "",
      // 持ち主編集
      showContent_owner: false,
      postOwner: "",
      // 衣装分類編集
      showContent_class: false,
      postClass: "",
      // 色分類編集
      showContent_color_class: false,
      postColor_Class: "",
      // 色編集
      showContent_color: false,
      postColor: "",
      // 登録内容
      registerForm_section: null,
      registerForm_character: {
        character: null,
        section: null
      },
      registerForm_owner: null,
      registerForm_class: null,
      registerForm_color_class: null,
      registerForm_color: {
        color: null,
        color_class: null
      },
    }
  },
  watch: {
    $route: {
      async handler () {
        await this.fetchSections();
        await this.fetchCharacters();
        await this.fetchOwners();
        await this.fetchClasses();
        await this.fetchColor_Classes();
        await this.fetchColors();
      },
      immediate: true
    }
  },
  methods: {
    // 区分を取得
    async fetchSections () {
      const response = await axios.get('/api/informations/sections');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }

      this.optionSections = response.data;
    },

    // 登場人物を取得
    async fetchCharacters () {
      const response = await axios.get('/api/informations/characters');
      
      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }
      
      this.gainSet.characters = response.data;
    },

    // 持ち主を取得
    async fetchOwners () {
      const response = await axios.get('/api/informations/owners');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }
      
      this.gainSet.owners = response.data;
    },

    // 衣装分類を取得
    async fetchClasses () {
      const response = await axios.get('/api/informations/classes');

      if (response.status !== 200) {
        this.$store.commit('error/setCode', response.status);
        return false;
      }
      
      this.gainSet.classes = response.data;
    },

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
      
      this.gainSet.colors = response.data;
    },

    // 区分編集のモーダル表示 
    openModal_sectionEdit (id) {
      this.showContent_section = true;
      this.postSection = id;
    },
    // 区分編集のモーダル非表示
    async closeModal_sectionEdit() {
      await this.fetchSections();
      await this.fetchCharacters();
      this.showContent_section = false;
    },

    // 登場人物編集のモーダル表示 
    openModal_characterEdit (id) {
      this.showContent_character = true;
      this.postCharacter = id;
    },
    // 登場人物編集のモーダル非表示
    async closeModal_characterEdit() {
      await this.fetchSections(); // なぜかこれをつけるとうまくいく
      await this.fetchCharacters();
      this.showContent_character = false;
    },

    // 持ち主編集のモーダル表示 
    openModal_ownerEdit (id) {
      this.showContent_owner = true;
      this.postOwner = id;
    },
    // 持ち主編集のモーダル非表示
    async closeModal_ownerEdit() {
      await this.fetchSections(); // なぜかこれをつけるとうまくいく
      await this.fetchOwners();
      this.showContent_owner = false;
    },

    // 衣装分類編集のモーダル表示 
    openModal_classEdit (id) {
      this.showContent_class = true;
      this.postClass = id;
    },
    // 持ち主編集のモーダル非表示
    async closeModal_classEdit() {
      await this.fetchSections(); // なぜかこれをつけるとうまくいく
      await this.fetchClasses();
      this.showContent_class = false;
    },

    // 色分類編集のモーダル表示 
    openModal_color_classEdit (id) {
      this.showContent_color_class = true;
      this.postColor_Class = id;
    },
    // 色分類編集のモーダル非表示
    async closeModal_color_classEdit() {
      await this.fetchColor_Classes();
      await this.fetchColors();
      this.showContent_color_class = false;
    },

    // 色編集のモーダル表示 
    openModal_colorEdit (id) {
      this.showContent_color = true;
      this.postColor = id;
    },
    // 色編集のモーダル非表示
    async closeModal_colorEdit() {
      await this.fetchColor_Classes(); // なぜかこれをつけるとうまくいく
      await this.fetchColors();
      this.showContent_color= false;
    },


    // 登録する
    async register_section () {
      let section = 0;
      this.optionSections.forEach((name) => {
        if(name.section === this.registerForm_section) {
          section = 1;
          return false;
        }
      }, this);

      if(section){
        alert('同じ名前は登録できません。');
        return false;
      }else{
        const response = await axios.post('/api/informations/sections', {
          section: this.registerForm_section
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.registerForm_section = null;
      
        await this.fetchSections();
        await this.fetchCharacters();

        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '区分が登録されました！',
          timeout: 6000
        });
      }      
    },

    async register_character () {
      let character = 0;
      this.gainSet.characters.forEach((names,index) => {
        names.characters.forEach((name) => {
          console.log(name);
          console.log(this.registerForm_character);
          if(name.name === this.registerForm_character.character) {
            character = 1;
            return false;
          }
        }, this);
        if(index === this.gainSet.characters.length-1){
          console.log(character);
        }
      }, this);

      if(character){
        alert('同じ名前は登録できません。');
        return false;
      }else{
        const response = await axios.post('/api/informations/characters', {
          section_id: this.registerForm_character.section,
          name: this.registerForm_character.character
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.registerForm_character.section = null;
        this.registerForm_character.character = null;
      
        await this.fetchCharacters();
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '登場人物が登録されました！',
          timeout: 6000
        });
      }      
    },
    
    async register_owner () {
      let owner = 0;
      this.gainSet.owners.forEach((name) => {
        if(name.name === this.registerForm_owner) {
          owner = 1;
          return false;
        }
      }, this);

      if(owner){
        alert('同じ名前は登録できません。');
        return false;
      }else{
        const response = await axios.post('/api/informations/owners', {
          name: this.registerForm_owner
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.registerForm_owner = null;

        await this.fetchOwners();
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '持ち主が登録されました！',
          timeout: 6000
        });
      }      
    },

    async register_class () {
      let classes = 0;
      this.gainSet.classes.forEach((costume) => {
        if(costume.class === this.registerForm_class) {
          classes = 1;
          return false;
        }
      }, this);

      if(classes){
        alert('同じ名前は登録できません。');
      }else{
        const response = await axios.post('/api/informations/classes', {
          class: this.registerForm_class
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }
      
        this.registerForm_class = null;

        await this.fetchClasses();
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '衣装分類が登録されました！',
          timeout: 6000
        });
      }      
    },

    async register_color_class () {
      let color_class = 0;
      this.optionColor_Classes.forEach((color_class) => {
        if(color_class.color_class === this.registerForm_color_class) {
          color_class = 1;
          return false;
        }
      }, this);

      if(color_class){
        alert('同じ色分類は登録できません。');
        return false;
      }else{
        const response = await axios.post('/api/informations/color_classes', {
          color_class: this.registerForm_color_class
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.registerForm_color_class = null;
      
        await this.fetchColor_Classes();
        await this.fetchColors();

        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '色分類が登録されました！',
          timeout: 6000
        });
      }      
    },

    async register_color () {
      let color = 0;
      this.gainSet.colors.forEach((color_classes,index) => {
        color_classes.colors.forEach((color_class) => {
          if(color_class.color === this.registerForm_color.color) {
            color = 1;
            return false;
          }
        }, this);
      }, this);

      if(color){
        alert('同じ色は登録できません。');
        return false;
      }else{
        const response = await axios.post('/api/informations/colors', {
          color_class_id: this.registerForm_color.color_class,
          color: this.registerForm_color.color
        });

        if (response.status === 422) {
          this.errors.error = response.data.errors;
          return false;
        }

        if (response.status !== 201) {
          this.$store.commit('error/setCode', response.status);
          return false;
        }

        this.registerForm_color.color_class = null;
        this.registerForm_color.color = null;
      
        await this.fetchColors();
        // メッセージ登録
        this.$store.commit('message/setContent', {
          content: '色が登録されました！',
          timeout: 6000
        });
      }      
    }
  }
}
</script>