import axios from '@axios'
import { defineStore } from 'pinia'

export const tasksApi = defineStore('tasks', {
  actions: {

    fetchAll(params) { return axios.get('/auth/tasks', { params }) },

    fetchTask(id, params={}) { return axios.get('/auth/tasks/show/'+id, { params }) },

    fetchStatistics(params={}) { return axios.get('/auth/tasks/statistics', { params }) },

    saveTask(data) {
      return new Promise((resolve, reject) => {
        axios.post('/auth/tasks/save/', data,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          }
        }).then(response => resolve(response))
          .catch(error => reject(error))
      })
    },

    // 👉 Delete Center
    actionTask(id, data) {
      return new Promise((resolve, reject) => {
        axios.post(`/auth/tasks/action/${id}`, data).then(response => resolve(response)).catch(error => reject(error))
      })
    },

    // 👉 Delete Center
    deleteTask(id) {
      return new Promise((resolve, reject) => {
        axios.delete(`/auth/tasks/${id}`).then(response => resolve(response)).catch(error => reject(error))
      })
    },
  },
})
