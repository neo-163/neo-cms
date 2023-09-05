import request from '@/utils/request'

// 列表
export function fetchList(query) {
  return request({
    url: '/api/admin/auth/page/list',
    method: 'post',
    params: query
  })
}

// 详情
export function fetchItem(id) {
  return request({
    url: '/api/admin/auth/page/details',
    method: 'post',
    params: { id }
  })
}

// 创建
export function createItem(data) {
  return request({
    url: '/api/admin/auth/page/create',
    method: 'post',
    params: data
  })
}

// 编辑
export function editItem(data) {
  return request({
    url: '/api/admin/auth/page/edit',
    method: 'post',
    params: data
  })
}

// 删除
export function deleteItem(id) {
  return request({
    url: '/api/admin/auth/page/delete',
    method: 'delete',
    params: { id }
  })
}

// export function fetchPv(pv) {
//   return request({
//     // url: '/api/admin/auth/page/pv',
//     url: '/api/admin/auth/page/detail',
//     method: 'post',
//     params: { pv }
//   })
// }

// export function updateArticle(data) {
//   return request({
//     // url: '/api/admin/auth/page/update',
//     url: '/api/admin/auth/page/detail',
//     method: 'post',
//     data
//   })
// }
