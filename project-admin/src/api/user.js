import request from '@/utils/request'

export function login(data) {
  return request({
    url: '/api/admin/login',
    method: 'post',
    data
  })
}

export function getInfo(token) {
  return request({
    url: '/api/admin/auth/info',
    method: 'post',
    params: { token }
  })
}

export function logout() {
  return request({
    url: '/api/admin/auth/logout',
    method: 'post'
  })
}

// 列表
export function fetchList(query) {
  return request({
    url: '/api/admin/auth/user/list',
    method: 'post',
    params: query
  })
}

// 详情
export function fetchItem(id) {
  return request({
    url: '/api/admin/auth/user/details',
    method: 'post',
    params: { id }
  })
}

// 创建
export function createItem(data) {
  return request({
    url: '/api/admin/auth/user/register',
    method: 'post',
    params: data
  })
}

// 编辑
export function editItem(data) {
  return request({
    url: '/api/admin/auth/user/edit',
    method: 'post',
    params: data
  })
}

// 删除
export function deleteItem(id) {
  return request({
    url: '/api/admin/auth/user/delete',
    method: 'delete',
    params: { id }
  })
}

// 谨慎删除用户，采用单个id
export function deleteUser(id) {
  return request({
    url: '/api/admin/auth/user/delete',
    method: 'delete',
    params: { id }
  })
}