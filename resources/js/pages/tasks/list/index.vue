<script setup>
import { tasksApi } from "@/plugins/apis/tasksRequest";
import SnackbarComponent from '@core/components/SnackbarCustom.vue';
import {
  requiredValidator
} from '@core/utils/validators';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { VDataTable } from 'vuetify/labs/VDataTable';

const tasksRequest = tasksApi()
const refVForm = ref()
const snackbarRef = ref(null);
const searchQuery = ref('')
const tasks = ref([])
const totalPage = ref(1)
const totalTasks = ref(0)
const isDialogVisible = ref(false)
const isActionDialogVisible = ref(false)
const taskID = ref(0)
const title = ref('')
const description = ref('')
const dueDate = ref('')
const dialogTitle = ref('')
const dialogButton = ref('')
const actionDialogAction = ref('')
const actionTaskID = ref('')
const actionDialogTitle = ref('')
const actionDialogMessage = ref('')
const actionDialogButton = ref('')

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

const tableHeader = [
  {
    title: 'Title',
    key: 'title',
    width: 20,
  },
  {
    title: 'Description',
    key: 'description',
    width: 40,
  },
  {
    title: 'Due Date',
    key: 'due_date',
    width: 15,
  },
  {
    title: 'Status',
    key: 'status',
    width: 10,
  },
  {
    title: '',
    key: 'actions',
    width: 15,
    sortable: false,
  },
]

const statusItems = [
  {
    title: 'Finished',
    value: 1,
  },
  {
    title: 'In Progress',
    value: 0,
  },
]

watch([searchQuery], () => {
  options.value.page = 1
});

const fetchTasks = () => {
  tasksRequest.fetchAll({
    search: searchQuery.value,
    options: options.value,
    page: options.value.page,
  }).then(response => {
    tasks.value = response.data.data.data
    totalPage.value = Math.ceil(response.data.total / response.data.perPage)
    totalTasks.value = response.data.total
    options.value.page = response.data.currentPage
  }).catch(e => {
    console.error(e)
  })
}

const taskDialog = (task=null) => {
  
  window.scrollTo({
    top: 0,
  });

  taskID.value = 0
  title.value = ''
  description.value = '';
  dueDate.value = '';
  dialogTitle.value = 'Add Task';
  dialogButton.value = 'Add';
  if(task) {
    taskID.value = task.id
    title.value = task.title
    description.value = task.description;
    dueDate.value = task.due_date;
    dialogTitle.value = 'Update Task';
    dialogButton.value = 'Update';
  }
  isDialogVisible.value = true;
}

const saveTask = id => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if(isValid) {
      
      const formData = new FormData();
      formData.append('id', taskID.value);
      formData.append('title', title.value);
      formData.append('description', description.value);
      formData.append('due_date', dueDate.value.toISOString());

      tasksRequest.saveTask(formData).then(response => {
        if(response.data['status']) {
          snackbarRef.value.exposevisibleSnackbar(response.data['message'], 'success');
          isDialogVisible.value = false;
          fetchTasks()
        }
      })
    }
  })
}

const actionDialog = (id, action) => {
  
  window.scrollTo({
    top: 0,
  });

  actionTaskID.value = id
  actionDialogAction.value = action
  actionDialogTitle.value = '';
  actionDialogMessage.value = '';
  actionDialogButton.value = '';
  if(actionDialogAction.value == 'delete') {
    actionDialogTitle.value = 'Delete Task';
    actionDialogMessage.value = 'Are you sure you want to delete this task?';
    actionDialogButton.value = 'Delete';
  }
  else if(actionDialogAction.value == 'in-progress') {
    actionDialogTitle.value = 'In Progress Task';
    actionDialogMessage.value = 'Are you sure you want to change this task status to in-progress?';
    actionDialogButton.value = 'Yes';
  }
  else if(actionDialogAction.value == 'finished') {
    actionDialogTitle.value = 'Finish Task';
    actionDialogMessage.value = 'Are you sure you want to change this task status to finished?';
    actionDialogButton.value = 'Yes';
  }
  isActionDialogVisible.value = true;
}

const actionTask = () => {
  if(actionDialogAction.value == 'delete') {
    tasksRequest.deleteTask(actionTaskID.value).then(response => {
      if(response.data['status']) {
        snackbarRef.value.exposevisibleSnackbar(response.data['message'], 'success');
        isActionDialogVisible.value = false;
        fetchTasks();
      }
    })
  }
  else if(actionDialogAction.value == 'in-progress' || actionDialogAction.value == 'finished') {
    tasksRequest.actionTask(actionTaskID.value, { action: actionDialogAction.value}).then(response => {
      if(response.data['status']) {
        snackbarRef.value.exposevisibleSnackbar(response.data['message'], 'success');
        isActionDialogVisible.value = false;
        fetchTasks();
      }
    })
  }
}

watchEffect(fetchTasks)
</script>

<template>
  <section>
  <VRow>
    <VCol cols="12">
      <VCard>

        <VCardText class="d-flex flex-wrap py-4 gap-4">
          <div class="me-3 d-flex gap-3"></div>
          
          <VSpacer />

          <div class="justify-end d-flex align-center flex-wrap gap-4">
            <!-- 👉 Search  -->
            <div style="inline-size: 20rem;">
              <VTextField
                v-model="searchQuery"
                placeholder="Search"
                density="compact"
              />
            </div>

            <VBtn @click="taskDialog()"><VIcon size="20" icon="bx-plus"/>Add Task</VBtn>
          </div>
        </VCardText>

        <VDivider />
        
        <VDataTable
          :headers="tableHeader"
          :items="tasks"
          v-model:items-per-page="options.itemsPerPage"
          v-model:page="options.page"
          hide-default-footer
        >
          <template #item.status="{ item }">
            <span :style="{ color: (item.raw.status==1) ? '#00AB41' : '#3abeff' }">{{ statusItems.find(statusItem => statusItem.value === item.raw.status)?.title }}</span>
          </template>

          <!-- Actions -->
          <template #item.actions="{ item }">

            <IconBtn v-if="item.raw.status==1" @click="actionDialog(item.raw.id, 'in-progress')" title="In Progress">
              <VIcon style="color: #3abeff" size="20" icon="bx-reset"/>
            </IconBtn>

            <IconBtn v-else @click="actionDialog(item.raw.id, 'finished')" title="Finished">
              <VIcon style="color: #00AB41" size="20" icon="bx-check"/>
            </IconBtn>

            <IconBtn @click="taskDialog(item.raw)" title="Edit">
              <VIcon color="primary" size="20" icon="bx-pencil"/>
            </IconBtn>

            <IconBtn @click="actionDialog(item.raw.id, 'delete')" title="Delete">
              
              <VIcon style="color: #FF3535" size="20" icon="bxs-trash"/>
            </IconBtn>
          </template>
        </VDataTable>
      </VCard>
    </VCol>
  </VRow>

  <VDialog
    v-model="isDialogVisible"
    :close-on-back="false"
    persistent
    width="500"
  >
    <VForm 
      ref="refVForm"
      @submit.prevent="saveTask"
    >
      <!-- Dialog Content -->
      <VCard :title="dialogTitle">
        <VCardText>
          <VRow>
            <VCol
              cols="12"
            >
              <VTextField
                v-model="title" 
                label="Title" 
                :rules="[requiredValidator]"
              />
            </VCol>
            <VCol
              cols="12"
            >
              <VTextarea 
                v-model="description" 
                label="Description" 
                :rules="[requiredValidator]"
              />
            </VCol>
          </VRow>

          <VRow>
            <VCol cols="12">
              <VueDatePicker 
              v-model="dueDate" 
              label="Due Date" 
              placeholder="Due Date"
              :rules="[requiredValidator]"
              ignore-time-validation
              :enable-time-picker="false"
            />
            </VCol>
          </VRow>
        </VCardText>
        <VCardText class="d-flex justify-end">
          <VBtn type="submit" style="margin-right: 5px;">
            {{ dialogButton }}
          </VBtn>
          <VBtn
          color="secondary"
          variant="tonal"
          @click="isDialogVisible = false"
          >
            Close
          </VBtn>
        </VCardText>
      </VCard>
    </VForm>
  </VDialog>

  <VDialog
    v-model="isActionDialogVisible"
    :close-on-back="false"
    persistent
    width="500"
  >
    <VForm 
      ref="refVForm"
      @submit.prevent="actionTask"
    >
      <!-- Dialog Content -->
      <VCard :title="actionDialogTitle">
        <VCardText>
          {{ actionDialogMessage }}
        </VCardText>
        <VCardText class="d-flex justify-end">
          <VBtn type="submit" style="margin-right: 5px;">
            {{ actionDialogButton }}
          </VBtn>
          <VBtn
          color="secondary"
          variant="tonal"
          @click="isActionDialogVisible = false"
          >
            Close
          </VBtn>
        </VCardText>
      </VCard>
    </VForm>
  </VDialog>
  <SnackbarComponent ref="snackbarRef"></SnackbarComponent>
  </section>
</template>
