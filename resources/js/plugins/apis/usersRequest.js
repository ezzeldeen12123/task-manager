import axios from '@axios';
import { defineStore } from 'pinia';

export const usersApi = defineStore('users', {
  actions: {

    register(data) { return axios.post('/auth/register', data) },

    login(data) { return axios.post('/auth/login', data) },

    logout() { return axios.get('/auth/logout') },

    fetchAll(params) { return axios.get('/auth/users', { params }) },

    fetchUser(id, params={}) { return axios.get('/auth/users/show/'+id, { params }) },

    updateUser(data, id) {
      return new Promise((resolve, reject) => {
        axios.post('/auth/users/update/'+id, data,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          }
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    changeUserPassword(data, id) {
      return new Promise((resolve, reject) => {
        axios.put('/auth/users/change-password/'+id, data)
        .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Center
    deleteUser(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/auth/users/delete/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
