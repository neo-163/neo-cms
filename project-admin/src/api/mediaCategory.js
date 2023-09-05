import request from '@/utils/request'

// 列表
export function fetchList(query) {
  return request({
    url: '/api/admin/auth/mediaCategory/list',
    method: 'post',
    params: query
  })
}

// 详情
export function fetchItem(id) {
  return request({
    url: '/api/admin/auth/mediaCategory/details',
    method: 'post',
    params: { id }
  })
}

// 创建
export function createItem(data) {
  return request({
    url: '/api/admin/auth/mediaCategory/create',
    method: 'post',
    params: data
  })
}

// 编辑
export function editItem(data) {
  return request({
    url: '/api/admin/auth/mediaCategory/edit',
    method: 'post',
    params: data
  })
}

// 删除
export function deleteItem(id) {
  return request({
    url: '/api/admin/auth/mediaCategory/delete',
    method: 'delete',
    params: { id }
  })
}