import request from '@/utils/request'

export function searchUser(name) {
  return request({
    // url: '/api/admin/auth/search/user',
    url: '/api/admin/auth/info',
    method: 'post',
    params: { name }
  })
}

export function transactionList(query) {
  return request({
    // url: '/api/admin/auth/transaction/list',
    url: '/api/admin/auth/info',
    method: 'post',
    params: query
  })
}
