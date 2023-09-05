/** When your routing table is too long, you can split it into small modules **/

import Layout from '@/layout'

const tableRouter = {
  path: '/table',
  component: Layout,
  redirect: '/table/complex-table',
  name: 'Table',
  meta: {
    title: '文章',
    icon: 'table'
  },
  children: [
    {
      path: 'create',
      component: () => import('@/views/example/create'),
      name: 'CreateArticle',
      meta: { title: '创建' }
    },
    {
      path: 'edit/:id(\\d+)',
      component: () => import('@/views/example/edit'),
      name: 'EditArticle',
      meta: { title: 'Edit Article', noCache: true, activeMenu: '/example/list' },
      hidden: true
    }
  ]
}
export default tableRouter
