<script setup>
import { usersApi } from "@/plugins/apis/usersRequest";
import SnackbarComponent from '@core/components/SnackbarCustom.vue';
import {
  emailValidator,
  requiredValidator
} from '@core/utils/validators';
import avatar1 from '@images/avatars/avatar-1.png';

const user = JSON.parse(localStorage.getItem('user') || 'null')
const usersRequest = usersApi()
const userData = ref()
const snackbarRef = ref(null);
const refVForm = ref()
const accountData = ref({
  id: '',
  name: '',
  email: '',
  avatarImg: avatar1,
})

const refInputEl = ref()
  
usersRequest.fetchUser(user.id).then(response => {
  userData.value = response.data;
  accountData.value.id = userData.value.id;
  accountData.value.name = userData.value.name;
  accountData.value.email = userData.value.email;
}).catch(e => {
  console.error(e)
})

const update = () => {

  refVForm.value?.validate().then(({ valid: isValid }) => {
    if(isValid) {

      const formData = new FormData();
      formData.append('id', user.id);
      formData.append('name', accountData.value.name);
      formData.append('email', accountData.value.email);
      
      usersRequest.updateUser(formData, user.id).then(response => {
        userData.value = response.data.user;
        snackbarRef.value.exposevisibleSnackbar('Updated Successfully', 'success');
      }).catch(e => {
        console.error(e)
      })
    }
  })
}

const resetForm = () => {
  accountData.value.id = userData.value.id;
  accountData.value.name = userData.value.name;
  accountData.value.email = userData.value.email;
}

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string')
        accountData.value.avatarImg = fileReader.result
    }
  }
}

// reset avatar image
const resetAvatar = () => {
  accountData.value.avatarImg = avatar1
}
</script>

<template>
  <div>
    <VRow>
      <VCol cols="12">
        <VCard title="Account Details">
          <VCardText class="d-flex">
            <!-- 👉 Avatar -->
            <VAvatar
              rounded="lg"
              size="100"
              class="me-6"
              :image="accountData.avatarImg"
            />

            <!-- 👉 Upload Photo -->
            <form class="d-flex flex-column justify-center gap-5">
              <div class="d-flex flex-wrap gap-2">
                <VBtn
                  color="primary"
                  @click="refInputEl?.click()"
                >
                  <VIcon
                    icon="bx-cloud-upload"
                    class="d-sm-none"
                  />
                  <span class="d-none d-sm-block">Upload new photo</span>
                </VBtn>

                <input
                  ref="refInputEl"
                  type="file"
                  name="file"
                  accept=".jpeg,.png,.jpg,GIF"
                  hidden
                  @input="changeAvatar"
                >

                <VBtn
                  type="reset"
                  color="error"
                  variant="tonal"
                  @click="resetAvatar"
                >
                  <span class="d-none d-sm-block">Reset</span>
                  <VIcon
                    icon="bx-refresh"
                    class="d-sm-none"
                  />
                </VBtn>
              </div>

              <p class="text-body-1 mb-0">
                Allowed JPG, JPEG or PNG.
              </p>
            </form>
          </VCardText>

          <VDivider />

          <VCardText>
            <!-- 👉 Form -->
            <VForm 
              class="mt-6"
              ref="refVForm"
              @submit.prevent="update"
            >
              <VRow>
                <!-- 👉 First Name -->
                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="accountData.name"
                    placeholder="John"
                    label="First Name"
                    :rules="[requiredValidator]"
                  />
                </VCol>

                <!-- 👉 Email -->
                <VCol
                  cols="12"
                  md="6"
                >
                  <VTextField
                    v-model="accountData.email"
                    label="E-mail"
                    placeholder="johndoe@gmail.com"
                    type="email"
                    :rules="[requiredValidator, emailValidator]"
                  />
                </VCol>

                <!-- 👉 Form Actions -->
                <VCol
                  cols="12"
                  class="d-flex flex-wrap gap-4"
                >
                  <VBtn type="submit">Save changes</VBtn>

                  <VBtn
                    color="secondary"
                    variant="tonal"
                    type="reset"
                    @click.prevent="resetForm"
                  >
                    Reset
                  </VBtn>
                </VCol>
              </VRow>
            </VForm>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
    <SnackbarComponent ref="snackbarRef"></SnackbarComponent>
  </div>
</template>
