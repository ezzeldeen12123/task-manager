<script setup>
import { usersApi } from "@/plugins/apis/usersRequest";
import SnackbarComponent from '@core/components/SnackbarCustom.vue';
import {
  confirmedValidator,
  passwordValidator,
  requiredValidator
} from '@core/utils/validators';

const user = JSON.parse(localStorage.getItem('user') || 'null')
const usersRequest = usersApi()
const snackbarRef = ref(null);
const refVForm = ref()
const isNewPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const newPassword = ref()
const confirmPassword = ref()

const passwordRequirements = [
  'Minimum 8 characters long - the more, the better',
  'At least one lowercase and one uppercase character',
  'At least one number and one symbol',
]

const changePassword = () => {

  refVForm.value?.validate().then(({ valid: isValid }) => {
    if(isValid) {

    const formData = new FormData();
      formData.append('id', user.id);
      formData.append('password', newPassword.value);
      
      usersRequest.changeUserPassword(formData, user.id).then(response => {
        snackbarRef.value.exposevisibleSnackbar('changed Successfully', 'success');
      }).catch(e => {
        console.error(e)
      })
    }
  })
}
</script>

<template>
  <div>
    <VRow>
      <!-- SECTION: Change Password -->
      <VCol cols="12">
        <VCard title="Change Password">
          <VForm 
            ref="refVForm"
            @submit.prevent="changePassword"
          >
            <VCardText>

              <!-- 👉 New Password -->
              <VRow>
                <VCol
                  cols="12"
                  md="6"
                >
                  <!-- 👉 new password -->
                  <VTextField
                    v-model="newPassword"
                    :type="isNewPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="isNewPasswordVisible ? 'bx-hide' : 'bx-show'"
                    label="New Password"
                    placeholder="············"
                    @click:append-inner="isNewPasswordVisible = !isNewPasswordVisible"
                    :rules="[requiredValidator, passwordValidator]"
                  />
                </VCol>

                <VCol
                  cols="12"
                  md="6"
                >
                  <!-- 👉 confirm password -->
                  <VTextField
                    v-model="confirmPassword"
                    :type="isConfirmPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="isConfirmPasswordVisible ? 'bx-hide' : 'bx-show'"
                    label="Confirm New Password"
                    placeholder="············"
                    @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                  :rules="[requiredValidator, confirmedValidator(newPassword, confirmPassword)]"
                  />
                </VCol>
              </VRow>
            </VCardText>

            <!-- 👉 Password Requirements -->
            <VCardText>
              <p class="text-base font-weight-medium mt-2">
                Password Requirements:
              </p>

              <ul class="d-flex flex-column gap-y-3">
                <li
                  v-for="item in passwordRequirements"
                  :key="item"
                  class="d-flex"
                >
                  <div>
                    <VIcon
                      size="7"
                      icon="bxs-circle"
                      class="me-3"
                    />
                  </div>
                  <span class="font-weight-medium">{{ item }}</span>
                </li>
              </ul>
            </VCardText>

            <!-- 👉 Action Buttons -->
            <VCardText class="d-flex flex-wrap gap-4">
              <VBtn type="submit">Save changes</VBtn>

              <VBtn
                type="reset"
                color="secondary"
                variant="tonal"
              >
                Reset
              </VBtn>
            </VCardText>
          </VForm>
        </VCard>
      </VCol>
      <!-- !SECTION -->
    </VRow>
    <SnackbarComponent ref="snackbarRef"></SnackbarComponent>
  </div>
</template>
