import request from '@/utils/request'

// 列表
export function fetchList(query) {
  return request({
    url: '/api/admin/auth/media/list',
    method: 'post',
    params: query
  })
}

// 详情
export function fetchItem(id) {
  return request({
    url: '/api/admin/auth/media/details',
    method: 'post',
    params: { id }
  })
}

// 创建
export function createItem(data) {
  return request({
    url: '/api/admin/auth/media/create',
    method: 'post',
    params: data
  })
}

// 编辑
export function editItem(data) {
  return request({
    url: '/api/admin/auth/media/edit',
    method: 'post',
    params: data
  })
}

// 删除
export function deleteItem(id) {
  return request({
    url: '/api/admin/auth/media/deleteOSSFile',
    method: 'delete',
    params: { id }
  })
}