import request from '@/utils/request'

export function getRoutes() {
  return request({
    url: '/api/admin/auth/permission/list',
    method: 'post'
  })
}

export function fetchList() {
  return request({
    url: '/api/admin/auth/role/list',
    method: 'post'
  })
}

export function addRole(data) {
  return request({
    url: '/api/admin/auth/role',
    method: 'post',
    data
  })
}

export function updateRole(id, data) {
  return request({
    url: '/api/admin/auth/role/store',
    method: 'post',
    data
  })
}

export function deleteRole(id) {
  return request({
    url: `/api/admin/auth/role/${id}`,
    method: 'delete'
  })
}
