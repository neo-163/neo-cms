import request from '@/utils/request'

// 列表
export function fetchList(query) {
  return request({
    url: '/api/admin/auth/permission/list',
    method: 'post',
    params: query
  })
}

export function getParentSelectList(id) {
  return request({
    url: '/api/admin/auth/permission/selects',
    method: 'post',
    params: { id }
  })
}

// 详情
export function fetchItem(id) {
  return request({
    url: '/api/admin/auth/permission/details',
    method: 'post',
    params: { id }
  })
}

// 保存：新增+编辑
export function storeItem(data) {
  return request({
    url: '/api/admin/auth/permission/store',
    method: 'post',
    params: data
  })
}

// 删除
export function deleteItem(id) {
  return request({
    url: '/api/admin/auth/permission/delete',
    method: 'delete',
    params: { id }
  })
}