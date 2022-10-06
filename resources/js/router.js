import Vue from 'vue'
import VueRouter from 'vue-router'

// ページコンポーネントをインポートする
import showScene from './pages/Show_Scene.vue'
import showCostume from './pages/Show_Costume.vue'
import registerScene from './pages/Register_Scene.vue'
import registerCostume from './pages/Register_Costume.vue'
import Setting from './pages/Setting.vue'
import SystemError from './pages/errors/System.vue'
import NotFound from './pages/errors/NotFound.vue'

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter)

// パスとコンポーネントのマッピング
const routes = [
  {
    path: '/',
    component: showScene
  },
  {
    path: '/show_Costume',
    component: showCostume
  },
  {
    path: '/register_scene',
    component: registerScene
  },
  {
    path: '/register_costume',
    component: registerCostume
  },
  {
    path: '/setting',
    component: Setting
  },
  {
    path: '/500',
    component: SystemError
  },
  {
    path: '*',
    component: NotFound
  }
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
  mode: 'history',
  routes
})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router