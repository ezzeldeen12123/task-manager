<script setup>
import { usersApi } from "@/plugins/apis/usersRequest";
import avatar1 from '@images/avatars/avatar-1.png';
import { ref } from "vue";

const route = useRoute()
const router = useRouter()
const usersRequest = usersApi()
const userData = ref()
const user = JSON.parse(localStorage.getItem('user') || 'null')
const photo = ref(avatar1)
  
usersRequest.fetchUser(user.id).then(response => {
  userData.value = response.data;
  photo.value = userData.value.photo;
}).catch(e => {
  console.error(e)
})

const logout = () => {
  localStorage.removeItem('user')
  localStorage.removeItem('accessToken')
  usersRequest.logout().then(response => {
    router.push('/login').then(() => {
      location.reload()
    })
  }).catch(e => {
    console.error(e)
  })
}
</script>

<template>
  <VBadge
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="photo" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="photo" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.name }}
            </VListItemTitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem @click="router.push('/account-settings')">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-user"
                size="22"
              />
            </template>

            <VListItemTitle >Profile</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="bx-log-out"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
