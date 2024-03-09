<script setup>
import { usersApi } from "@/plugins/apis/usersRequest";
import SnackbarComponent from '@core/components/SnackbarCustom.vue';
import {
  confirmedValidator,
  emailValidator,
  passwordValidator,
  requiredValidator
} from '@core/utils/validators';

const usersRequest = usersApi()
const snackbarRef = ref(null);
const refVForm = ref()
const form = ref({
  name: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)

const register = () => {
  refVForm.value?.validate().then(({ valid: isValid }) => {
    if(isValid) {
      usersRequest.register(form.value).then(response => {
        console.log(response.data.message)
        if(response) {
          snackbarRef.value.exposevisibleSnackbar('Registered Successfully', 'success');
          router.push('/login').then(() => {
            location.reload()
          })
        }
      }).catch(e => {
        if(e && e.response && e.response.data && e.response.data.message)
          snackbarRef.value.exposevisibleSnackbar(e.response.data.message, 'error');
        console.error(e)
      });
    }
  })
}
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">

        <VCardTitle class="text-2xl font-weight-bold">
          Task Management
        </VCardTitle>
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 mb-1">
          Register
        </h5>
      </VCardText>

      <VCardText>
        <VForm 
          ref="refVForm"
          @submit.prevent="register"
        >
          <VRow>
            <!-- name -->
            <VCol cols="12">
              <VTextField
                v-model="form.name"
                autofocus
                label="name"
                placeholder="Johndoe"
                :rules="[requiredValidator]"
              />
            </VCol>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="form.email"
                label="Email"
                placeholder="johndoe@email.com"
                type="email"
                :rules="[requiredValidator, emailValidator]"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                v-model="form.password"
                label="Password"
                placeholder="············"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'bx-hide' : 'bx-show'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
                :rules="[requiredValidator, passwordValidator]"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                v-model="form.confirmPassword"
                label="Confirm Password"
                placeholder="············"
                :type="isConfirmPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isConfirmPasswordVisible ? 'bx-hide' : 'bx-show'"
                @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                :rules="[requiredValidator, confirmedValidator(form.password, form.confirmPassword)]"
              />
              <div class="d-flex align-center mt-1 mb-4"></div>

              <VBtn
                block
                type="submit"
              >
                Sign up
              </VBtn>
            </VCol>

            <!-- login instead -->
            <VCol
              cols="12"
              class="text-center text-base"
            >
              <span>Already have an account?</span>
              <RouterLink
                class="text-primary ms-2"
                to="/login"
              >
                Sign in instead
              </RouterLink>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  <SnackbarComponent ref="snackbarRef"></SnackbarComponent>
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>
