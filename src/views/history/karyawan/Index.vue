<template>
  <BCard no-body>
    <BCardHeader>
      <img src="@/assets/images/page/history-staf.png" class="me-1 page-img" />
      <h5 class="fs-15 d-inline-block">
        Kedisiplinan Staff PT Catur Karsa Inkrisuba
      </h5>
    </BCardHeader>
    <BCardBody>
      <router-view v-slot="{ Component }" :key="$route.fullPath">
        <transition name="slide-fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </BCardBody>
  </BCard>
</template>
<script>
export default {};
</script>
